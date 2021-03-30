<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SubscriptionPlan::create([
            'url' => 'freelancer',
            'stripe_price_id' => 'price_1IYTnNDa1TGHitv5V36cyfkc',
            'cost' => 990,
            'name' => 'SEO Bridge Freelancer',
            'description' => 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed no sea takimata sanctus est Lorem ipsum dolor sit amet.',
            'image' => '/images/app/sp_freelancer.png',
        ]);

        SubscriptionPlan::create([
            'url' => 'startup',
            'stripe_price_id' => 'price_1IYTnoDa1TGHitv5OJ84nwQf',
            'cost' => 1990,
            'name' => 'SEO Bridge Startup',
            'description' => 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed no sea takimata sanctus est Lorem ipsum dolor sit amet.',
            'image' => '/images/app/sp_startup.png',
        ]);

        SubscriptionPlan::create([
            'url' => 'agency',
            'stripe_price_id' => 'price_1IYTpBDa1TGHitv5BZPWoh7N',
            'cost' => 2990,
            'name' => 'SEO Bridge Agency',
            'description' => 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed no sea takimata sanctus est Lorem ipsum dolor sit amet.',
            'image' => '/images/app/sp_agency.png',
        ]);
    }
}
