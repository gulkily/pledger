<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help Me Get to GitHub Universe!</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
            color: #333;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            color: white;
            margin-bottom: 30px;
        }

        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
        }

        .header p {
            font-size: 1.2em;
            opacity: 0.9;
        }

        .countdown {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            text-align: center;
            color: white;
            font-size: 1.1em;
        }

        .countdown strong {
            font-size: 1.5em;
            display: block;
            margin-top: 5px;
        }

        .card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            margin-bottom: 20px;
        }

        .chart-container {
            display: flex;
            gap: 30px;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .pie-chart {
            position: relative;
            width: 250px;
            height: 250px;
            flex-shrink: 0;
        }

        .pie-chart svg {
            transform: rotate(-90deg);
        }

        .chart-center {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            pointer-events: none;
        }

        .chart-center-percentage {
            font-size: 2.5em;
            font-weight: bold;
            color: #667eea;
            line-height: 1;
        }

        .chart-center-label {
            font-size: 0.9em;
            color: #666;
            margin-top: 5px;
        }

        .pie-segment {
            transition: opacity 0.3s, stroke-width 0.3s;
            cursor: pointer;
        }

        .pie-segment:hover {
            opacity: 0.8;
            stroke-width: 52;
        }

        .tooltip {
            position: absolute;
            background: rgba(0, 0, 0, 0.9);
            color: white;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 0.9em;
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.2s;
            z-index: 1000;
            white-space: nowrap;
        }

        .tooltip.show {
            opacity: 1;
        }

        .chart-info {
            flex: 1;
            min-width: 250px;
        }

        .progress-text {
            font-size: 2em;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 10px;
        }

        .price-range {
            font-size: 1.1em;
            color: #666;
            margin-bottom: 20px;
        }

        .sponsors-list {
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 15px;
        }

        .sponsor-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .sponsor-item:last-child {
            border-bottom: none;
        }

        .sponsor-name {
            font-weight: 600;
            color: #333;
        }

        .sponsor-amount {
            color: #667eea;
            font-weight: 600;
        }

        .form-section {
            margin-top: 30px;
            padding-top: 30px;
            border-top: 2px solid #f0f0f0;
        }

        .form-section h2 {
            margin-bottom: 20px;
            color: #333;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #555;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1em;
            transition: border-color 0.3s;
        }

        .form-group input:focus {
            outline: none;
            border-color: #667eea;
        }

        .percentage-input {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .percentage-input input {
            flex: 1;
        }

        .percentage-input span {
            font-size: 1.2em;
            color: #667eea;
            font-weight: bold;
        }

        .estimate {
            background: #f8f9ff;
            padding: 15px;
            border-radius: 8px;
            margin-top: 10px;
            color: #555;
        }

        .btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 8px;
            font-size: 1.1em;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }

        .btn:active {
            transform: translateY(0);
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .success-message {
            background: #4caf50;
            color: white;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            display: none;
        }

        .error-message {
            background: #f44336;
            color: white;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            display: none;
        }

        @media (max-width: 600px) {
            .header h1 {
                font-size: 1.8em;
            }
            
            .chart-container {
                flex-direction: column;
            }
            
            .pie-chart {
                margin: 0 auto;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚀 Help Me Get to GitHub Universe!</h1>
            <p>I've been invited to attend - help sponsor my flight!</p>
        </div>

        <div class="countdown">
            Need to book by: <strong id="deadline">Loading...</strong>
            <div id="timeLeft"></div>
        </div>

        <div class="card">
            <div class="chart-container">
                <div class="pie-chart">
                    <svg width="250" height="250" viewBox="0 0 250 250" id="pieChartSvg">
                        <!-- Background circle -->
                        <circle cx="125" cy="125" r="100" fill="#f0f0f0"/>
                        <!-- Segments will be added here dynamically -->
                        <g id="segmentsContainer"></g>
                        <!-- Inner circle for donut effect -->
                        <circle cx="125" cy="125" r="70" fill="white"/>
                    </svg>
                    <div class="chart-center">
                        <div class="chart-center-percentage"><span id="totalPercent">0</span>%</div>
                        <div class="chart-center-label">Pledged</div>
                    </div>
                    <div class="tooltip" id="tooltip"></div>
                </div>

                <div class="chart-info">
                    <div class="progress-text"><span id="totalPercent">0</span>% Pledged</div>
                    <div class="price-range" id="priceRange">
                        Estimated flight cost: <strong>Loading...</strong>
                    </div>
                    <div class="sponsors-list" id="sponsorsList">
                        <p style="color: #999; text-align: center;">Loading pledges...</p>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h2>Make a Pledge</h2>
                <form id="pledgeForm">
                    <div class="form-group">
                        <label for="name">Your Name</label>
                        <input type="text" id="name" required placeholder="Enter your name">
                    </div>

                    <div class="form-group">
                        <label for="percentage">Pledge Percentage</label>
                        <div class="percentage-input">
                            <input type="number" id="percentage" min="1" max="100" required placeholder="Enter percentage">
                            <span>%</span>
                        </div>
                        <div class="estimate" id="estimate">
                            This will be approximately <strong>$0 - $0</strong>
                        </div>
                    </div>

                    <button type="submit" class="btn" id="submitBtn">Commit My Pledge</button>
                    <div class="success-message" id="successMessage">
                        Thank you! Your pledge has been recorded. I'll reach out closer to the booking date!
                    </div>
                    <div class="error-message" id="errorMessage"></div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Configuration - loaded from backend
        let config = {
            minPrice: 300,
            maxPrice: 600,
            deadline: '2025-10-23'
        };

        let pledges = [];

        // API base URL - adjust this to match your setup
        const API_URL = 'api.php';

        // Load configuration from backend
        async function loadConfig() {
            try {
                const response = await fetch(`${API_URL}?action=get_config`);
                const data = await response.json();
                
                if (data.success) {
                    config.minPrice = parseInt(data.config.min_price);
                    config.maxPrice = parseInt(data.config.max_price);
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
                const response = await fetch(`${API_URL}?action=get_pledges`);
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

        // Generate colors for pie segments
        function generateColor(index, total) {
            // Create a harmonious color palette based on the main gradient
            const hues = [
                260, // Purple (main)
                240, // Blue-purple
                220, // Blue
                280, // Violet
                200, // Cyan-blue
                300, // Magenta
                180, // Cyan
                320, // Pink-purple
            ];
            
            const hue = hues[index % hues.length];
            const saturation = 70 + (index % 3) * 10;
            const lightness = 55 + (index % 4) * 5;
            
            return `hsl(${hue}, ${saturation}%, ${lightness}%)`;
        }

        // Update pie chart with segments
        function updateChart() {
            const total = pledges.reduce((sum, p) => sum + parseFloat(p.percentage), 0);
            document.getElementById('totalPercent').textContent = total.toFixed(1);
            
            const container = document.getElementById('segmentsContainer');
            container.innerHTML = '';
            
            if (pledges.length === 0) {
                return;
            }
            
            const radius = 100;
            const circumference = 2 * Math.PI * radius;
            let currentOffset = 0;
            
            pledges.forEach((pledge, index) => {
                const percentage = parseFloat(pledge.percentage);
                const segmentLength = (percentage / 100) * circumference;
                const color = generateColor(index, pledges.length);
                
                // Create SVG circle segment
                const circle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
                circle.setAttribute('cx', '125');
                circle.setAttribute('cy', '125');
                circle.setAttribute('r', radius);
                circle.setAttribute('fill', 'none');
                circle.setAttribute('stroke', color);
                circle.setAttribute('stroke-width', '50');
                circle.setAttribute('stroke-dasharray', `${segmentLength} ${circumference}`);
                circle.setAttribute('stroke-dashoffset', -currentOffset);
                circle.setAttribute('class', 'pie-segment');
                circle.style.transition = 'all 0.5s ease';
                
                // Add hover effects
                const tooltip = document.getElementById('tooltip');
                circle.addEventListener('mouseenter', (e) => {
                    const minAmount = Math.round(config.minPrice * percentage / 100);
                    const maxAmount = Math.round(config.maxPrice * percentage / 100);
                    tooltip.innerHTML = `<strong>${pledge.name}</strong><br>${percentage}% (${minAmount} - ${maxAmount})`;
                    tooltip.classList.add('show');
                });
                
                circle.addEventListener('mousemove', (e) => {
                    const rect = e.target.getBoundingClientRect();
                    tooltip.style.left = (e.clientX - rect.left + 15) + 'px';
                    tooltip.style.top = (e.clientY - rect.top - 15) + 'px';
                });
                
                circle.addEventListener('mouseleave', () => {
                    tooltip.classList.remove('show');
                });
                
                container.appendChild(circle);
                currentOffset += segmentLength;
            });
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
        document.getElementById('percentage').addEventListener('input', function() {
            const pct = parseFloat(this.value) || 0;
            const minAmount = Math.round(config.minPrice * pct / 100);
            const maxAmount = Math.round(config.maxPrice * pct / 100);
            document.getElementById('estimate').innerHTML = 
                `This will be approximately <strong>$${minAmount} - $${maxAmount}</strong>`;
        });

        // Handle form submission
        document.getElementById('pledgeForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const submitBtn = document.getElementById('submitBtn');
            const successMsg = document.getElementById('successMessage');
            const errorMsg = document.getElementById('errorMessage');
            
            // Hide previous messages
            successMsg.style.display = 'none';
            errorMsg.style.display = 'none';
            
            // Disable submit button
            submitBtn.disabled = true;
            submitBtn.textContent = 'Submitting...';
            
            const name = document.getElementById('name').value;
            const percentage = parseFloat(document.getElementById('percentage').value);
            
            try {
                const response = await fetch(`${API_URL}?action=add_pledge`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ name, percentage })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Reload pledges
                    await loadPledges();
                    
                    // Show success message
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
        setInterval(updateCountdown, 60000); // Update every minute
    </script>
</body>
</html>
