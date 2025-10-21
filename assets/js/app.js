// Configuration - loaded from backend
const APP_CONFIG = window.APP_CONFIG || {};
const API_BASE_URL = APP_CONFIG.apiUrl || 'api.php';

let config = {
    minPrice: 300,
    maxPrice: 600,
    deadline: '2025-10-23'
};

let pledges = [];

function apiUrl(action) {
    const url = new URL(API_BASE_URL, window.location.href);
    url.searchParams.set('action', action);
    url.searchParams.set('_', Date.now().toString());
    return url.toString();
}

// Load configuration from backend
async function loadConfig() {
    try {
        const response = await fetch(apiUrl('get_config'), { cache: 'no-store' });
        const data = await response.json();

        if (data.success) {
            config.minPrice = parseInt(data.config.min_price, 10);
            config.maxPrice = parseInt(data.config.max_price, 10);
            config.deadline = data.config.deadline;

            document.getElementById('priceRange').innerHTML =
                `Estimated flight cost: <strong>$${config.minPrice} - $${config.maxPrice}</strong>`;
            document.getElementById('deadline').textContent =
                new Date(config.deadline).toLocaleDateString('en-US', {
                    month: 'long',
                    day: 'numeric',
                    year: 'numeric'
                });

            updateCountdown();
        }
    } catch (error) {
        console.error('Error loading config:', error);
    }
}

// Load pledges from backend
async function loadPledges() {
    try {
        const response = await fetch(apiUrl('get_pledges'), { cache: 'no-store' });
        const data = await response.json();

        if (data.success) {
            pledges = data.pledges;
            updateChart();
            updateSponsorsList();
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
    const deadline = new Date(config.deadline);
    const diff = deadline - now;

    if (diff > 0) {
        const days = Math.floor(diff / (1000 * 60 * 60 * 24));
        const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        document.getElementById('timeLeft').innerHTML = `<strong>${days} days, ${hours} hours remaining</strong>`;
    } else {
        document.getElementById('timeLeft').innerHTML = '<strong>Deadline passed</strong>';
    }
}

// Update pie chart
function updateChart() {
    const total = pledges.reduce((sum, p) => sum + parseFloat(p.percentage), 0);
    document.getElementById('totalPercent').textContent = total.toFixed(1);

    const circumference = 2 * Math.PI * 100;
    const progress = Math.min(total / 100, 1) * circumference;
    document.getElementById('progressCircle').setAttribute('stroke-dasharray', `${progress} ${circumference}`);
}

// Update sponsors list
function updateSponsorsList() {
    const list = document.getElementById('sponsorsList');

    if (pledges.length === 0) {
        list.innerHTML = '<p style="color: #999; text-align: center;">No pledges yet. Be the first!</p>';
        return;
    }

    list.innerHTML = pledges.map(p => {
        const pct = parseFloat(p.percentage);
        const minAmount = Math.round(config.minPrice * pct / 100);
        const maxAmount = Math.round(config.maxPrice * pct / 100);

        return `
            <div class="sponsor-item">
                <span class="sponsor-name">${p.name}</span>
                <span class="sponsor-amount">${pct}% ($${minAmount} - $${maxAmount})</span>
            </div>
        `;
    }).join('');
}

// Update estimate
const percentageInput = document.getElementById('percentage');
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

    try {
        const response = await fetch(apiUrl('add_pledge'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ name, percentage }),
            cache: 'no-store'
        });

        const data = await response.json();

        if (data.success) {
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
