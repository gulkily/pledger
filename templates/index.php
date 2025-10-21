<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Co-fund Community Research · GitHub Universe Trip</title>
    <link rel="stylesheet" href="assets/css/main.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚀 Co-fund research for healthier online communities</h1>
            <p>I’m heading to GitHub Universe to advance community infrastructure research—and I’d love you to co-own the learning.</p>
        </div>

        <div class="story-card">
            <h2>Why this trip matters</h2>
            <p>
                My current work is laser-focused on open source infrastructure that keeps online communities healthy:
                humane moderation tooling, participation rituals that feel welcoming, and spaces where belonging doesn’t
                require performance. GitHub Universe is where I can pressure-test those ideas with other people building in
                the same direction.
            </p>
            <p>
                A long-time collaborator already offered to pay for the flight, but I’d rather distribute both the risk and
                the breakthroughs. When the community co-invests, the insights belong to everyone—not just the person who
                can underwrite a plane ticket.
            </p>
            <p>
                This pledge drive is an experiment in community-supported research: you pledge a percentage today, I only
                collect if/when we book, and you receive first access to conference notes, prototypes, and rituals that come
                out of the trip. Your downside is zero; your upside is direct influence on OSS community tooling.
            </p>
            <p>
                Booking deadline is tight, and every percentage pledged today lightens the anchor sponsor’s load. Thank you
                for backing meaningful, community-focused FOSS research.
            </p>
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
                    <div class="supporter-banner">
                        Community goal: shift the anchor sponsor’s share from 100% to 40% so everyone co-owns the outcome.
                    </div>
                    <div class="progress-text">
                        <span id="displayPercent">0</span>% of commitments
                    </div>
                    <div class="progress-subtext" id="rawTotalText">
                        Total pledged: 0%
                    </div>
                    <p class="normalization-note" id="normalizationNote" hidden>
                        When commitments climb past 100%, the chart shows each pledge’s share so we can see how the community carries the load.
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
                        <label for="name">Your Name <span class="field-tag">Public</span></label>
                        <input type="text" id="name" required placeholder="Enter your name">
                        <p class="helper-text">Displayed on the pledge list and credited on any prototypes, talks, or notes that grow from this trip.</p>
                    </div>

                    <div class="form-group">
                        <label for="email">Contact Email <span class="field-tag field-tag--private">Private</span></label>
                        <input type="email" id="email" placeholder="you@example.com">
                        <p class="helper-text">Only Ilya sees this—used to coordinate payment details and follow up after the conference.</p>
                    </div>

                    <div class="form-group">
                        <label for="percentage">Pledge Percentage</label>
                        <div class="percentage-input">
                            <input type="number" id="percentage" min="1" max="100" required placeholder="10">
                            <span>%</span>
                        </div>
                        <div class="helper-text">
                            Commit to any slice of the flight (5%, 25%, whatever fits). I only collect if we book, and the anchor sponsor
                            backstops the rest—so your risk is zero while your influence is real.
                        </div>
                        <div class="estimate" id="estimate">
                            This will be approximately <strong>$0 - $0</strong>
                        </div>
                    </div>

                    <button type="submit" class="btn" id="submitBtn">Commit My Pledge</button>
                    <div class="success-message" id="successMessage">
                        Thank you! You’re part of the supporter circle—I’ll share booking details and research updates soon.
                    </div>
                    <div class="error-message" id="errorMessage"></div>
                </form>
            </div>
        </div>

        <div class="faq-card">
            <h2>Questions you might have</h2>
            <div class="faq-item">
                <h3>When will I actually pay?</h3>
                <p>I’ll follow up right before booking to confirm your pledge amount based on the final ticket price—no funds move until that moment.</p>
            </div>
            <div class="faq-item">
                <h3>What happens to the anchor sponsor?</h3>
                <p>Your pledges reduce what they cover. If we go beyond 100%, they scale back and we collectively decide how to reinvest the surplus.</p>
            </div>
            <div class="faq-item">
                <h3>What if the trip falls through?</h3>
                <p>No funds are collected until the flight is purchased. If plans change, I’ll let everyone know and no pledges will be called in.</p>
            </div>
            <div class="faq-item">
                <h3>How will supporters benefit?</h3>
                <p>You’ll receive rapid research notes, prototypes, and practical rituals from Universe conversations—plus ongoing credit in anything that ships.</p>
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
