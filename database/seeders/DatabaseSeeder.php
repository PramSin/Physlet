<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Message;
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
        $categories = ["力学", "电磁学", "热学", "光学", "近代物理"];
        foreach ($categories as $category) {
            Category::factory(['name' => $category])->create();
        }
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
        SimulationWithVersion::factory(100)->create();
        Comment::factory(300)->create();
        Comment::factory(300)->create();
        Message::factory(50)->create();
    }
}
