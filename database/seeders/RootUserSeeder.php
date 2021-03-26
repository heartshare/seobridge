<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RootUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'id' => 'user_root',
            'email' => 'maurice.freuwoert@gmail.com',
            'username' => 'SEO_BRIDGE_ROOT',
            'firstname' => 'Maurice',
            'lastname' => 'Freuwört',
            'password' => Hash::make('TenDifferentBigTreeFlyAlert12~'),
            'metadata' => '{}',
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('author_profiles')->insert([
            'id' => 'author_root',
            'user_id' => 'user_root',
            'url' => 'maurice-freuwoert',
            'full_name' => 'Maurice Freuwört',
            'display_name' => 'Maurice Freuwört',
            'image' => 'https://pbs.twimg.com/profile_images/1184201669229383680/djl1vt4C_200x200.jpg',
            'biography' => 'Developer, creative person, passionate about code.',
            'social_links' => '[]',
            'metadata' => '{}',
            'verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
