<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\Simulation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Comment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $sim = Simulation::find(array_rand(Simulation::all()->toarray()) + 1);
        return [
            'user_id' => array_rand(User::all()->toarray()) + 1,
            'content' => $this->faker->paragraph,
            'simulation_id' => $sim->id,
            'parent_id' => (lcg_value() > 0.4 || !$sim->comments->count()) ?
                0 : $sim->comments[array_rand($sim->comments->toArray())]->id
        ];
    }
}
