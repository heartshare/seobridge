<?php

return [
    'allowed_free_team_plans' => 1,
    'free_team_plan_name' => 'team_free',

    'team_plans' => [
        'team_free' => [
            'id' => 'team_free',
            'stripe_id' => env('STRIPE_TEAM_PLAN_LOW'),
            'included_namespaces' => 1,
            'additional_namespaces' => false,
            'check_namespaces' => true,
            'max_urls' => 100,
            'display_price' => 0,
            'image' => '/images/app/sp_freelancer.png',
            'name' => 'Free',
            'slug' => 'free',
        ],
    
        'team_low' => [
            'id' => 'team_low',
            'stripe_id' => env('STRIPE_TEAM_PLAN_LOW'),
            'included_namespaces' => 3,
            'additional_namespaces' => false,
            'check_namespaces' => true,
            'max_urls' => 150,
            'display_price' => 1000,
            'image' => '/images/app/sp_freelancer.png',
            'name' => 'Starter',
            'slug' => 'starter',
        ],
    
        'team_mid' => [
            'id' => 'team_mid',
            'stripe_id' => env('STRIPE_TEAM_PLAN_MID'),
            'included_namespaces' => 3,
            'additional_namespaces' => true,
            'check_namespaces' => true,
            'max_urls' => 500,
            'display_price' => 2500,
            'image' => '/images/app/sp_startup.png',
            'name' => 'Growing',
            'slug' => 'growing',
        ],
    
        'team_high' => [
            'id' => 'team_high',
            'stripe_id' => env('STRIPE_TEAM_PLAN_HIGH'),
            'included_namespaces' => 0,
            'additional_namespaces' => true,
            'check_namespaces' => false,
            'max_urls' => 1000,
            'display_price' => 6000,
            'image' => '/images/app/sp_agency.png',
            'name' => 'Unlimited',
            'slug' => 'unlimited',
        ],
    ],

    'namespace_plans' => [
        'namespace_mid' => [
            'id' => 'namespace_mid',
            'stripe_id' => env('STRIPE_NAMESPACE_MID'),
            'display_price' => 300,
            'name' => 'Namespace',
            'slug' => 'namespace',
        ],
    ],
];