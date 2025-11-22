<?php

$projectRoot = dirname(__DIR__, 2);

return [
    'slug' => 'placeholder',
    'display_name' => 'Configure Your Cause',
    'db_path' => $projectRoot . '/data/placeholder.db',
    'price_range' => [
        'min' => 100,
        'max' => 100,
        'description' => 'Estimated budget'
    ],
    'deadline' => '2099-12-31',
    'goal_banner' => 'This site has not been configured yet. Run the cause wizard to get started.',
    'hero' => [
        'headline' => 'Set up your pledge campaign',
        'tagline' => 'This is a placeholder cause. Configure a real campaign via the CLI wizard.',
        'subtext' => 'Edit `config/causes/placeholder.php` or run `php scripts/cause_wizard.php create` to launch your first cause.',
        'avatar' => [
            'src' => 'image/1530699.jpeg',
            'alt' => 'Placeholder',
            'link' => '#'
        ]
    ],
    'story' => [
        'why_it_matters' => [
            'You are seeing placeholder copy because no campaign has been configured yet.',
            'Run the CLI wizard or follow the setup guide to create your first cause.'
        ],
        'explore' => [
            'heading' => 'Next steps',
            'items' => [
                'Run `php scripts/cause_wizard.php create` to generate a new config.',
                'Update `config/current_cause.php` to point at your new slug.',
                'Deploy once the placeholder banner disappears.'
            ]
        ]
    ],
    'research_projects' => [
        [
            'title' => 'Placeholder Project',
            'tags' => ['#todo'],
            'description' => 'Replace this entry with your real experiments using the cause wizard.'
        ]
    ]
];
