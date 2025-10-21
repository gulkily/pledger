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
            <h1>🚀 Fuel a FOSS maintainer’s trip to GitHub Universe</h1>
            <p>I’m researching healthier online community spaces through open source and need support to keep learning.</p>
        </div>

        <div class="story-card">
            <h2>Why this trip matters</h2>
            <p>
                I build and study free and open source tools that help communities connect, moderate, and grow with less
                friction. GitHub Universe is where I can learn from other researchers and maintainers experimenting with
                digital gathering spaces.
            </p>
            <p>
                Your pledge funds the flight. In return, I’ll share conference learnings, research notes, prototypes, and
                practical takeaways with the supporters who make the trip possible—so any benefits from this visit ripple
                back to the people backing it.
            </p>
            <p>
                One long-time collaborator has already stepped up to cover the entire ticket if needed. I’d love to lighten
                their load and turn this into a shared effort from the community that benefits most from the research.
            </p>
            <p>
                This pledge drive is an experiment in community-supported research: can we fund the learning together and
                then share whatever insights emerge so everyone gains?
            </p>
            <p>
                Booking deadline is tight, and every percentage pledged brings us closer to liftoff. Thank you for
                helping me keep meaningful community-focused FOSS work sustainable.
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
                        Community goal: reduce the anchor sponsor’s share from 100% to 40%.
                    </div>
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
                        <label for="name">Your Name <span class="field-tag">Public</span></label>
                        <input type="text" id="name" required placeholder="Enter your name">
                        <p class="helper-text">Displayed on the pledge list so everyone can see who’s backing the trip.</p>
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
                            Commit to a slice of the final ticket price so we lighten the anchor sponsor’s load. I’ll reach out with
                            the exact dollar amount before booking.
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

        <div class="faq-card">
            <h2>Questions you might have</h2>
            <div class="faq-item">
                <h3>When will I actually pay?</h3>
                <p>I’ll follow up right before booking to confirm your pledge amount based on the final ticket price.</p>
            </div>
            <div class="faq-item">
                <h3>What happens to the anchor sponsor?</h3>
                <p>Your pledges reduce what they cover. If we go beyond 100%, I’ll work with them to scale back their contribution.</p>
            </div>
            <div class="faq-item">
                <h3>What if the trip falls through?</h3>
                <p>No funds are collected until the flight is purchased. If plans change, I’ll let everyone know and no pledges will be called in.</p>
            </div>
            <div class="faq-item">
                <h3>How will supporters benefit?</h3>
                <p>You’ll receive research notes, prototypes, and practical takeaways from conversations at Universe so your communities benefit too.</p>
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
