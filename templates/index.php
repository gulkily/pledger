<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Co-fund Community Research · GitHub Universe Trip</title>
    <link rel="stylesheet" href="assets/css/main.css">
</head>
<body>
<?php
$heroConfig = $causeConfig['hero'] ?? [];
$heroAvatar = $heroConfig['avatar'] ?? [];
$storyConfig = $causeConfig['story'] ?? [];
$storyParagraphs = $storyConfig['why_it_matters'] ?? [];
$storyExplore = $storyConfig['explore'] ?? [];
$researchProjects = $causeConfig['research_projects'] ?? [];
$goalBanner = $causeConfig['goal_banner'] ?? '';
$priceRange = $causeConfig['price_range'] ?? [];
$priceMin = isset($priceRange['min']) ? (int) $priceRange['min'] : null;
$priceMax = isset($priceRange['max']) ? (int) $priceRange['max'] : null;
$priceDescription = $priceRange['description'] ?? 'Estimated cost';
$deadline = $causeConfig['deadline'] ?? '';
$deadlineDisplay = 'Loading...';
if (!empty($deadline)) {
    $deadlineDate = DateTime::createFromFormat('Y-m-d', $deadline, new DateTimeZone('UTC'));
    if ($deadlineDate instanceof DateTime) {
        $deadlineDisplay = $deadlineDate->format('F j, Y');
    } else {
        $deadlineDisplay = $deadline;
    }
} else {
    $deadlineDisplay = 'Flexible';
}
$clientCausePayload = [
    'slug' => $causeConfig['cause_slug'] ?? ($causeConfig['slug'] ?? ''),
    'display_name' => $causeConfig['display_name'] ?? '',
    'goal_banner' => $goalBanner,
    'deadline' => $deadline,
    'hero' => $heroConfig,
    'story' => $storyConfig,
    'price_range' => $priceRange,
    'research_projects' => $researchProjects,
];
$clientInitialConfig = [
    'min_price' => $priceMin,
    'max_price' => $priceMax,
    'deadline' => $deadline,
];
?>
    <div class="container">
        <div class="hero">
            <div class="hero-avatar">
                <a id="heroAvatarLink" href="<?php echo htmlspecialchars($heroAvatar['link'] ?? '#'); ?>" target="_blank" rel="noreferrer">
                    <img id="heroAvatarImage" src="<?php echo htmlspecialchars($heroAvatar['src'] ?? 'image/1530699.jpeg'); ?>" alt="<?php echo htmlspecialchars($heroAvatar['alt'] ?? 'Campaign lead'); ?>">
                </a>
            </div>
            <div class="hero-content">
                <div class="hero-header">
                    <h1 id="heroHeadline"><?php echo htmlspecialchars($heroConfig['headline'] ?? 'Co-fund research for healthier online communities'); ?></h1>
                    <button type="button" id="themeToggle" class="theme-toggle" aria-label="Toggle color theme" data-theme-state="auto">
                        <span id="themeToggleIcon">🌓</span>
                        <span id="themeToggleLabel">Auto</span>
                    </button>
                </div>
                <p class="hero-tagline" id="heroTagline"><?php echo $heroConfig['tagline'] ?? 'Help co-fund research and keep the insights open to everyone.'; ?></p>
                <p class="hero-subtext" id="heroSubtext"><?php echo htmlspecialchars($heroConfig['subtext'] ?? 'Your pledge lightens a single sponsor’s load and keeps the insights we uncover open to everyone.'); ?></p>
            </div>
        </div>

        <div class="story-card">
            <h2>Why this trip matters</h2>
            <div id="storyWhy">
                <?php if (!empty($storyParagraphs)): ?>
                    <?php foreach ($storyParagraphs as $paragraph): ?>
                        <p><?php echo htmlspecialchars($paragraph); ?></p>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Support this work to keep open-source community research accountable to its supporters.</p>
                <?php endif; ?>
            </div>
            <div class="story-explore">
                <h3 id="storyExploreHeading"><?php echo htmlspecialchars($storyExplore['heading'] ?? 'What I’m exploring'); ?></h3>
                <ul id="storyExploreList">
                    <?php if (!empty($storyExplore['items'])): ?>
                        <?php foreach ($storyExplore['items'] as $item): ?>
                            <li><?php echo htmlspecialchars($item); ?></li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li>New exploration topics coming soon.</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

        <div class="research-card">
            <h2>Current experiments</h2>
            <ul class="research-list" id="researchList">
                <?php if (!empty($researchProjects)): ?>
                    <?php foreach ($researchProjects as $project): ?>
                        <li>
                            <span class="research-title"><?php echo htmlspecialchars($project['title'] ?? ''); ?></span>
                            <?php if (!empty($project['tags']) && is_array($project['tags'])): ?>
                                <span class="research-tags">
                                    <?php foreach ($project['tags'] as $tag): ?>
                                        <span><?php echo htmlspecialchars($tag); ?></span>
                                    <?php endforeach; ?>
                                </span>
                            <?php endif; ?>
                            <?php if (!empty($project['description'])): ?>
                                <p><?php echo htmlspecialchars($project['description']); ?></p>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li>
                        <span class="research-title">New experiments coming soon</span>
                    </li>
                <?php endif; ?>
            </ul>
            <p class="research-links">Want to dive deeper? Explore the projects on <a href="https://github.com/gulkily" target="_blank" rel="noreferrer">GitHub</a> or reach out for collaboration notes.</p>
        </div>

        <a class="btn mobile-cta" href="#pledgeForm">Commit My Pledge</a>

        <div class="countdown">
            Need to book by: <strong id="deadline"><?php echo htmlspecialchars($deadlineDisplay); ?></strong>
            <div id="timeLeft"></div>
        </div>

        <div class="card">
            <div class="chart-container">
                <div class="pie-chart">
                    <svg width="250" height="250" viewBox="0 0 250 250">
                        <circle class="ring-base" cx="125" cy="125" r="100"/>
                        <circle id="progressCircle" class="ring-progress" cx="125" cy="125" r="100"
                                fill="none"
                                stroke="url(#gradient)"
                                stroke-width="50"
                                stroke-dasharray="0 628.32"
                                style="transition: stroke-dasharray 0.5s ease"/>
                        <defs>
                            <linearGradient id="gradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" style="stop-color:var(--color-ring-start);stop-opacity:1" />
                                <stop offset="100%" style="stop-color:var(--color-ring-end);stop-opacity:1" />
                            </linearGradient>
                        </defs>
                    </svg>
                </div>

                <div class="chart-info">
                    <div class="supporter-banner" id="goalBanner">
                        <?php echo htmlspecialchars($goalBanner ?: 'Community goal: lighten the anchor sponsor’s load together.'); ?>
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
                        <?php if ($priceMin !== null && $priceMax !== null): ?>
                            <?php echo htmlspecialchars($priceDescription); ?>:
                            <strong>$<?php echo number_format($priceMin); ?> - $<?php echo number_format($priceMax); ?></strong>
                        <?php else: ?>
                            <?php echo htmlspecialchars($priceDescription); ?>: <strong>Loading...</strong>
                        <?php endif; ?>
                    </div>
                    <div class="sponsors-list" id="sponsorsList">
                        <p style="color: #999; text-align: center;">Loading pledges...</p>
                    </div>
                </div>
            </div>

            <div class="manage-section" id="manageSection" hidden>
                <h2>Manage your pledges</h2>
                <p class="helper-text">This device keeps your session so you can adjust or remove pledges later. Clearing cookies or storage removes quick access—just reach out if you ever need a manual change.</p>
                <div class="manage-message" id="manageMessage" hidden></div>
                <div id="manageList"></div>
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
                            <input type="number" id="percentage" min="1" max="100" required placeholder="e.g., 10">
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
                <h3>How do I update or cancel my pledge?</h3>
                <p>As long as you revisit from this browser, you’ll see manage buttons to edit or remove your pledge. If you lose access (cleared storage, new device), just DM or email me.</p>
            </div>
            <div class="faq-item">
                <h3>When will I actually pay?</h3>
                <p>I’ll follow up right before booking to confirm your pledge amount based on the final ticket price.</p>
            </div>
            <div class="faq-item">
                <h3>Is there an automated checkout?</h3>
                <p>Not this time. This sprint is deliberately lightweight and last-minute, so once we hit 100% I’ll reach out personally with a payment link or transfer instructions. Think of it as a manually orchestrated experiment rather than a SaaS flow.</p>
            </div>
            <div class="faq-item">
                <h3>What if my situation changes?</h3>
                <p>This is a trust-based collaboration. If life shifts—expenses pop up, you get cold feet, or the final number feels heavy—let me know. I won’t auto-charge or chase anyone; we’ll adjust the pledge or hand it back to the anchor sponsor.</p>
            </div>
            <div class="faq-item">
                <h3>Is this only for faith communities?</h3>
                <p>Faith circles are one of the labs where these tools are stress-tested, but the patterns—consent-first identity, respectful AI, resilient archives—apply to any group trying to treat people well. The research is open-source and reusable.</p>
            </div>
            <div class="faq-item">
                <h3>What happens to the anchor sponsor?</h3>
                <p>Your pledges shrink their commitment. If we push past 100%, I’ll scale everyone’s share down proportionally so the anchor—and every supporter—only pays what’s still needed.</p>
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
            apiUrl: <?php echo json_encode($apiUrl); ?>,
            cause: <?php echo json_encode($clientCausePayload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>,
            initialConfig: <?php echo json_encode($clientInitialConfig, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>
        };
    </script>
    <script src="assets/js/app.js"></script>
</body>
</html>
