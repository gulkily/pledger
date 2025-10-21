// Configuration - loaded from backend
const APP_CONFIG = window.APP_CONFIG || {};
const API_BASE_URL = APP_CONFIG.apiUrl || 'api.php';

let config = {
    minPrice: 300,
    maxPrice: 600,
    deadline: '2025-10-23'
};

let pledges = [];
let pledgeState = {
    rawTotal: 0,
    normalizedLookup: new Map()
};
let sessionToken = null;

const SESSION_STORAGE_KEY = 'pledgerSessionToken';

try {
    const storedToken = window.localStorage?.getItem(SESSION_STORAGE_KEY);
    if (storedToken) {
        sessionToken = storedToken;
    }
} catch (error) {
    console.warn('Unable to access localStorage for session token', error);
}

function pledgeKey(pledge) {
    if (pledge && pledge.id !== undefined && pledge.id !== null) {
        return String(pledge.id);
    }
    return `${pledge?.name || 'supporter'}-${pledge?.percentage}-${pledge?.created_at || ''}`;
}

function recalcPledgeState() {
    const rawTotal = pledges.reduce((sum, pledge) => {
        const pct = Number.parseFloat(pledge.percentage);
        return sum + (Number.isFinite(pct) ? pct : 0);
    }, 0);

    const normalizedLookup = new Map();
    if (rawTotal > 0) {
        pledges.forEach(pledge => {
            const pct = Number.parseFloat(pledge.percentage);
            const safePct = Number.isFinite(pct) ? pct : 0;
            normalizedLookup.set(pledgeKey(pledge), (safePct / rawTotal) * 100);
        });
    }

    pledgeState = { rawTotal, normalizedLookup };
}

function persistSessionToken(token) {
    if (!token || typeof token !== 'string') {
        return;
    }

    sessionToken = token;

    try {
        window.localStorage?.setItem(SESSION_STORAGE_KEY, token);
    } catch (error) {
        console.warn('Unable to save session token to localStorage', error);
    }
}

function getStoredSessionToken() {
    if (sessionToken) {
        return sessionToken;
    }
    try {
        return window.localStorage?.getItem(SESSION_STORAGE_KEY) || '';
    } catch (error) {
        return '';
    }
}

function withSessionOptions(options = {}) {
    const merged = { ...options };
    const headers = { ...(options.headers || {}) };
    const token = getStoredSessionToken();
    if (token) {
        headers['X-Pledger-Session'] = token;
    }
    merged.headers = headers;
    return merged;
}

function parseLocalDate(value) {
    if (!value) {
        return null;
    }
    const parts = value.split('-').map(Number);
    if (parts.length === 3 && parts.every(Number.isFinite)) {
        const [year, month, day] = parts;
        return new Date(year, month - 1, day);
    }
    return new Date(value);
}

function apiUrl(action) {
    const url = new URL(API_BASE_URL, window.location.href);
    url.searchParams.set('action', action);
    url.searchParams.set('_', Date.now().toString());
    return url.toString();
}

// Load configuration from backend
async function loadConfig() {
    try {
        const response = await fetch(apiUrl('get_config'), withSessionOptions({ cache: 'no-store' }));
        const data = await response.json();

        if (data.success) {
            config.minPrice = parseInt(data.config.min_price, 10);
            config.maxPrice = parseInt(data.config.max_price, 10);
            config.deadline = data.config.deadline;

            persistSessionToken(data.session_token);

            document.getElementById('priceRange').innerHTML =
                `Estimated flight cost: <strong>$${config.minPrice} - $${config.maxPrice}</strong>`;
            const deadlineDate = parseLocalDate(config.deadline);
            if (deadlineDate instanceof Date && !Number.isNaN(deadlineDate.valueOf())) {
                document.getElementById('deadline').textContent =
                    deadlineDate.toLocaleDateString('en-US', {
                        month: 'long',
                        day: 'numeric',
                        year: 'numeric'
                    });
            }

            updateCountdown();
        }
    } catch (error) {
        console.error('Error loading config:', error);
    }
}

// Load pledges from backend
async function loadPledges() {
    try {
        const response = await fetch(apiUrl('get_pledges'), withSessionOptions({ cache: 'no-store' }));
        const data = await response.json();

        if (data.success) {
            pledges = Array.isArray(data.pledges) ? data.pledges : [];
            persistSessionToken(data.session_token);
            updateChart();
            updateSponsorsList();
            updateManageSection();
        }
    } catch (error) {
        console.error('Error loading pledges:', error);
        document.getElementById('sponsorsList').innerHTML =
            '<p style="color: #999; text-align: center;">Error loading pledges</p>';
    }
}


// Calculate countdown
function updateCountdown() {
    const now = new Date();
    const deadline = parseLocalDate(config.deadline);
    const diff = deadline - now;

    if (deadline && diff > 0) {
        const days = Math.floor(diff / (1000 * 60 * 60 * 24));
        const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        document.getElementById('timeLeft').innerHTML = `<strong>${days} days, ${hours} hours remaining</strong>`;
    } else {
        document.getElementById('timeLeft').innerHTML = '<strong>Deadline passed</strong>';
    }
}

// Update pie chart
function updateChart() {
    recalcPledgeState();

    const { rawTotal } = pledgeState;
    const displayPercent = rawTotal > 100 ? 100 : rawTotal;
    const displayPercentEl = document.getElementById('displayPercent');
    const rawTotalTextEl = document.getElementById('rawTotalText');
    const noteEl = document.getElementById('normalizationNote');

    if (displayPercentEl) {
        displayPercentEl.textContent = displayPercent.toFixed(1);
    }

    if (rawTotalTextEl) {
        rawTotalTextEl.textContent = `Total pledged: ${rawTotal.toFixed(1)}%`;
    }

    if (noteEl) {
        noteEl.hidden = !(rawTotal > 100);
    }

    const circumference = 2 * Math.PI * 100;
    const progress = Math.min(displayPercent / 100, 1) * circumference;
    const progressCircle = document.getElementById('progressCircle');
    if (progressCircle) {
        progressCircle.setAttribute('stroke-dasharray', `${progress} ${circumference}`);
    }
}

// Update sponsors list
function updateSponsorsList() {
    const list = document.getElementById('sponsorsList');

    if (pledges.length === 0) {
        list.innerHTML = '<p style="color: #999; text-align: center;">No pledges yet. Be the first!</p>';
        return;
    }

    // Ensure normalization data is up to date even if called independently
    recalcPledgeState();

    const { rawTotal, normalizedLookup } = pledgeState;

    list.innerHTML = pledges.map(p => {
        const pct = Number.parseFloat(p.percentage) || 0;
        const minAmount = Math.round(config.minPrice * pct / 100);
        const maxAmount = Math.round(config.maxPrice * pct / 100);
        const normalized = normalizedLookup.get(pledgeKey(p));
        const normalizedText = rawTotal > 100 && Number.isFinite(normalized)
            ? ` · ${normalized.toFixed(1)}% of commitments`
            : '';

        const ownedBadge = p.owned_by_session ? '<span class="sponsor-badge">You</span>' : '';
        const ownedClass = p.owned_by_session ? ' sponsor-item--owned' : '';

        return `
            <div class="sponsor-item${ownedClass}" data-pledge-id="${p.id}">
                <div class="sponsor-meta">
                    <span class="sponsor-name">${p.name} ${ownedBadge}</span>
                    <span class="sponsor-amount">${pct}%${normalizedText} ($${minAmount} - $${maxAmount})</span>
                </div>
            </div>
        `;
    }).join('');
}

function updateManageSection() {
    const section = document.getElementById('manageSection');
    const list = document.getElementById('manageList');
    const message = document.getElementById('manageMessage');

    if (!section || !list) {
        return;
    }

    const ownedPledges = pledges.filter(p => p.owned_by_session);

    if (ownedPledges.length === 0) {
        section.hidden = true;
        list.innerHTML = '';
        if (message) {
            message.hidden = true;
        }
        return;
    }

    section.hidden = false;

    list.innerHTML = ownedPledges.map(p => {
        const pct = Number.parseFloat(p.percentage) || 0;
        const emailValue = p.email ? p.email : '';

        return `
            <form class="manage-pledge-form" data-pledge-id="${p.id}">
                <div class="manage-row">
                    <label>Name</label>
                    <input type="text" name="name" value="${p.name}" required>
                </div>
                <div class="manage-row">
                    <label>Email (Private)</label>
                    <input type="email" name="email" value="${emailValue}">
                </div>
                <div class="manage-row manage-row--inline">
                    <label>Percentage</label>
                    <div class="manage-percentage-input">
                        <input type="number" name="percentage" min="1" max="100" value="${pct}" required>
                        <span>%</span>
                    </div>
                </div>
                <div class="manage-actions">
                    <button type="submit" class="manage-save-button">Save changes</button>
                    <button type="button" class="manage-delete-button" data-pledge-id="${p.id}">Remove</button>
                </div>
            </form>
        `;
    }).join('');

    list.querySelectorAll('.manage-pledge-form').forEach(form => {
        form.addEventListener('submit', handleManageFormSubmit);
    });

    list.querySelectorAll('.manage-delete-button').forEach(button => {
        button.addEventListener('click', handleManageDeleteClick);
    });
}

function setManageMessage(status, text) {
    const message = document.getElementById('manageMessage');
    if (!message) {
        return;
    }

    message.textContent = text;
    message.hidden = false;
    message.classList.remove('manage-message--error', 'manage-message--success');
    if (status === 'error') {
        message.classList.add('manage-message--error');
    }
    if (status === 'success') {
        message.classList.add('manage-message--success');
    }
}

function clearManageMessage() {
    const message = document.getElementById('manageMessage');
    if (message) {
        message.hidden = true;
    }
}

async function handleManageFormSubmit(event) {
    event.preventDefault();
    const form = event.currentTarget;
    const pledgeId = parseInt(form.dataset.pledgeId, 10);

    if (!Number.isInteger(pledgeId) || pledgeId <= 0) {
        setManageMessage('error', 'Unable to determine which pledge to update.');
        return;
    }

    const name = form.elements.name.value.trim();
    const email = form.elements.email.value.trim();
    const percentage = parseFloat(form.elements.percentage.value);

    if (!name) {
        setManageMessage('error', 'Name cannot be empty.');
        return;
    }

    if (!Number.isFinite(percentage) || percentage <= 0 || percentage > 100) {
        setManageMessage('error', 'Percentage must be between 1 and 100.');
        return;
    }

    const submitButton = form.querySelector('.manage-save-button');
    if (submitButton) {
        submitButton.disabled = true;
    }

    setManageMessage('info', 'Saving your changes...');

    try {
        const response = await fetch(apiUrl('update_pledge'), withSessionOptions({
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ pledge_id: pledgeId, name, email, percentage })
        }));
        const data = await response.json();

        if (data.success) {
            persistSessionToken(data.session_token);
            setManageMessage('success', 'Pledge updated.');
            await loadPledges();
        } else {
            setManageMessage('error', data.error || 'Could not update the pledge.');
        }
    } catch (error) {
        console.error('Error updating pledge:', error);
        setManageMessage('error', 'Something went wrong while updating.');
    } finally {
        if (submitButton) {
            submitButton.disabled = false;
        }
    }
}

async function handleManageDeleteClick(event) {
    const pledgeId = parseInt(event.currentTarget.dataset.pledgeId, 10);
    if (!Number.isInteger(pledgeId) || pledgeId <= 0) {
        setManageMessage('error', 'Unable to determine which pledge to remove.');
        return;
    }

    const confirmed = window.confirm('Remove this pledge? This action cannot be undone.');
    if (!confirmed) {
        return;
    }

    event.currentTarget.disabled = true;
    setManageMessage('info', 'Removing pledge...');

    try {
        const response = await fetch(apiUrl('delete_pledge'), withSessionOptions({
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ pledge_id: pledgeId })
        }));
        const data = await response.json();

        if (data.success) {
            persistSessionToken(data.session_token);
            setManageMessage('success', 'Pledge removed.');
            await loadPledges();
        } else {
            setManageMessage('error', data.error || 'Could not remove the pledge.');
        }
    } catch (error) {
        console.error('Error deleting pledge:', error);
        setManageMessage('error', 'Something went wrong while removing the pledge.');
    } finally {
        event.currentTarget.disabled = false;
    }
}

// Update estimate
const percentageInput = document.getElementById('percentage');
const emailInput = document.getElementById('email');

percentageInput.addEventListener('input', function () {
    const pct = parseFloat(this.value) || 0;
    const minAmount = Math.round(config.minPrice * pct / 100);
    const maxAmount = Math.round(config.maxPrice * pct / 100);
    document.getElementById('estimate').innerHTML =
        `This will be approximately <strong>$${minAmount} - $${maxAmount}</strong>`;
});

// Handle form submission
document.getElementById('pledgeForm').addEventListener('submit', async function (e) {
    e.preventDefault();

    const submitBtn = document.getElementById('submitBtn');
    const successMsg = document.getElementById('successMessage');
    const errorMsg = document.getElementById('errorMessage');

    successMsg.style.display = 'none';
    errorMsg.style.display = 'none';

    submitBtn.disabled = true;
    submitBtn.textContent = 'Submitting...';

    const name = document.getElementById('name').value;
    const percentage = parseFloat(document.getElementById('percentage').value);
    const email = emailInput ? emailInput.value.trim() : '';

    try {
        const response = await fetch(apiUrl('add_pledge'), withSessionOptions({
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ name, percentage, email }),
            cache: 'no-store'
        }));

        const data = await response.json();

        if (data.success) {
            persistSessionToken(data.session_token);
            await loadPledges();

            successMsg.style.display = 'block';
            this.reset();
            document.getElementById('estimate').innerHTML =
                'This will be approximately <strong>$0 - $0</strong>';

            setTimeout(() => {
                successMsg.style.display = 'none';
            }, 5000);
        } else {
            errorMsg.textContent = data.error || 'Failed to submit pledge. Please try again.';
            errorMsg.style.display = 'block';
        }
    } catch (error) {
        console.error('Error submitting pledge:', error);
        errorMsg.textContent = 'Network error. Please check your connection and try again.';
        errorMsg.style.display = 'block';
    } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Commit My Pledge';
    }
});

// Initialize
loadConfig();
loadPledges();
setInterval(updateCountdown, 60000);
