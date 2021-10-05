<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Comment;
use App\Models\SimulationWithVersion;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'username' => 'test',
            'slug' => 'test',
            'email' => '714223317@qq.com',
            'email_verified_at' => now(),
            'password' => Hash::make('test')
        ]);
        User::factory(3)
            ->has(User::factory(3), 'followers')
            ->create();
        User::factory(3)
            ->has(User::factory(3), 'followings')
            ->create();
        Category::factory(4)->create();
        SimulationWithVersion::factory(10)->create();
        Comment::factory(20)->create();
    }
}
