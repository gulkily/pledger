<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help Me Get to GitHub Universe!</title>
    <link rel="stylesheet" href="assets/css/main.css">
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
                    <svg width="250" height="250" viewBox="0 0 250 250">
                        <circle cx="125" cy="125" r="100" fill="#f0f0f0"/>
                        <circle id="progressCircle" cx="125" cy="125" r="100"
                                fill="none"
                                stroke="url(#gradient)"
                                stroke-width="50"
                                stroke-dasharray="0 628.32"
                                style="transition: stroke-dasharray 0.5s ease"/>
                        <defs>
                            <linearGradient id="gradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" style="stop-color:#667eea;stop-opacity:1" />
                                <stop offset="100%" style="stop-color:#764ba2;stop-opacity:1" />
                            </linearGradient>
                        </defs>
                    </svg>
                </div>

                <div class="chart-info">
                    <div class="progress-text">
                        <span id="displayPercent">0</span>% of commitments
                    </div>
                    <div class="progress-subtext" id="rawTotalText">
                        Total pledged: 0%
                    </div>
                    <p class="normalization-note" id="normalizationNote" hidden>
                        Chart shows each pledge as a share of total commitments when promises go beyond 100%.
                    </p>
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
        window.APP_CONFIG = {
            apiUrl: <?php echo json_encode($apiUrl); ?>
        };
    </script>
    <script src="assets/js/app.js"></script>
</body>
</html>
