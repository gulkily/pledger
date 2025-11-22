<?php

$projectRoot = dirname(__DIR__, 2);

return [
    'slug' => 'github-universe-trip',
    'display_name' => 'GitHub Universe Research Trip',
    'db_path' => $projectRoot . '/pledges.db',
    'price_range' => [
        'min' => 300,
        'max' => 600,
        'description' => 'Estimated flight cost'
    ],
    'deadline' => '2025-10-23',
    'goal_banner' => 'Community goal: shift the anchor sponsor’s share from 100% to 40% so everyone co-owns the outcome.',
    'hero' => [
        'headline' => 'Co-fund research for healthier online communities',
        'tagline' => 'I’m heading to <a href="https://githubuniverse.com/">GitHub Universe</a> to advance community infrastructure research—and I’d love you to co-own the learning.',
        'subtext' => 'Your pledge lightens a single sponsor’s load and keeps the insights we uncover open to everyone.',
        'avatar' => [
            'src' => 'image/1530699.jpeg',
            'alt' => 'Ilya Gulko',
            'link' => 'https://github.com/gulkily'
        ]
    ],
    'story' => [
        'why_it_matters' => [
            'My current work is laser-focused on open source infrastructure that keeps online communities healthy: humane moderation tooling, participation rituals that feel welcoming, and spaces where belonging doesn’t require performance. GitHub Universe is where I can pressure-test those ideas with other people building in the same direction.',
            'An existing collaborator already offered to pay for the flight, but I’d rather distribute both the risk and the breakthroughs. When the community co-invests, the insights belong to everyone—not just the person who can underwrite a plane ticket.',
            'This pledge drive is an experiment in community-supported research: you pledge a percentage today, I only collect if/when we book, and you receive first access to conference notes, prototypes, and rituals that come out of the trip. Your downside is zero; your upside is direct influence on OSS community tooling.',
            'You’ll be able to edit or cancel your pledge later from this browser—keep the tab bookmarked and the session remembers you for future supporter-only experiments.',
            'Booking deadline is tight, and every percentage pledged today lightens the anchor sponsor’s load. Thank you for backing meaningful, community-focused FOSS research.'
        ],
        'explore' => [
            'heading' => 'What I’m exploring',
            'items' => [
                'Designing consent-first social spaces where people control their identity, data, and moderation tools.',
                'Building mutual-support tooling that respects context—whether a prayer circle or any community that cares for its members.',
                'Experimenting with human-guided AI helpers that stay portable, auditable, and grounded in community intent.'
            ]
        ]
    ],
    'research_projects' => [
        [
            'title' => 'Pollyanna',
            'tags' => ['#consent-first', '#accessibility'],
            'description' => 'Framework for resilient online communities that keeps identities portable, content verifiable, and access universal.'
        ],
        [
            'title' => 'PrayerLift / ThyWill',
            'tags' => ['#mutual-care', '#ai-assistive'],
            'description' => 'Respectful AI-generated responses for support networks—useful case studies for any group practicing empathic check-ins.'
        ],
        [
            'title' => 'Prayer Record Text Format',
            'tags' => ['#portable-data', '#community-archives'],
            'description' => 'Human-readable, git-friendly specification for tracking support activity so communities can own their history.'
        ]
    ],
];
