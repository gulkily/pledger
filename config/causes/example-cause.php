<?php
// Template cause config. Duplicate this file, rename it, and customize values per campaign.
$projectRoot = dirname(__DIR__, 2);

return [
    'slug' => 'example-cause',
    'display_name' => 'Example Campaign',
    'db_path' => $projectRoot . '/data/example-cause.db',
    'price_range' => [
        'min' => 100,
        'max' => 500,
        'description' => 'Estimated project budget'
    ],
    'deadline' => '2025-12-31',
    'goal_banner' => 'Goal statement for this cause goes here.',
    'hero' => [
        'headline' => 'Headline for the cause hero',
        'tagline' => 'Why the cause matters in one or two sentences.',
        'subtext' => 'Optional supporting text for the hero section.',
        'avatar' => [
            'src' => 'image/placeholder.png',
            'alt' => 'Campaign avatar',
            'link' => '#'
        ]
    ],
    'story' => [
        'why_it_matters' => [
            'Paragraph about the importance of this cause.',
            'Additional context or anecdotes to make the story resonate.'
        ],
        'explore' => [
            'heading' => 'What we are exploring',
            'items' => [
                'Bullet one describing an area of focus.',
                'Bullet two describing another exploration theme.'
            ]
        ]
    ],
    'research_projects' => []
];
