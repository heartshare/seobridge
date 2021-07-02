<?php

return [
    'seo_allowed_free_plans' => 1,

    'seo_plans' => [
        'seo_free' => [
            'id' => 'seo_free',
            'stripe_id' => env('STRIPE_SEO_TEAM_PLAN_LOW'),
            'included_namespaces' => 1,
            'additional_namespaces' => false,
            'check_namespaces' => true,
            'max_urls' => 100,
            'display_price' => 0,
            'image' => '/images/app/sp_freelancer.png',
            'name' => 'Free',
            'slug' => 'free',
        ],
    
        'seo_low' => [
            'id' => 'seo_low',
            'stripe_id' => env('STRIPE_SEO_TEAM_PLAN_LOW'),
            'included_namespaces' => 3,
            'additional_namespaces' => false,
            'check_namespaces' => true,
            'max_urls' => 150,
            'display_price' => 1000,
            'image' => '/images/app/sp_freelancer.png',
            'name' => 'Starter',
            'slug' => 'starter',
        ],
    
        'seo_mid' => [
            'id' => 'seo_mid',
            'stripe_id' => env('STRIPE_SEO_TEAM_PLAN_MID'),
            'included_namespaces' => 3,
            'additional_namespaces' => true,
            'check_namespaces' => true,
            'max_urls' => 500,
            'display_price' => 2500,
            'image' => '/images/app/sp_startup.png',
            'name' => 'Growing',
            'slug' => 'growing',
        ],
    
        'seo_high' => [
            'id' => 'seo_high',
            'stripe_id' => env('STRIPE_SEO_TEAM_PLAN_HIGH'),
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
];