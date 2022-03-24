<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Message;
use App\Models\Simulation;
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
        $user = User::factory()->create([
            'username' => 'fake_user',
            'slug' => 'fake_user',
            'email' => 'fake_email',
            'password' => Hash::make('fake_password')
        ]);
        $sim = Simulation::factory()->create([
            'user_id' => $user->id,
            'slug' => 'fake_sim',
            'access' => 0
        ]);
        SimulationWithVersion::create([
            'name' => 'fake_name',
            'slug' => 'fake_slug',
            'synopsis' => 'fake_sentence',
            'root_path' => 'fake_path',
            'simulation_id' => $sim->id,
            'status_id' => $sim->id,
        ]);
//        User::factory()->create([
//            'username' => 'test',
//            'slug' => 'test',
//            'email' => '714223317@qq.com',
//            'email_verified_at' => now(),
//            'password' => Hash::make('test'),
//        ]);
//        User::factory(3)
//            ->has(User::factory(3), 'followers')
//            ->create();
//        User::factory(3)
//            ->has(User::factory(3), 'followings')
//            ->create();
//        SimulationWithVersion::factory(100)->create();
//        Comment::factory(300)->create();
//        Comment::factory(300)->create();
//        Message::factory(50)->create();
    }
}
