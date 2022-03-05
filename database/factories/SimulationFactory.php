<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Simulation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SimulationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Simulation::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'user_id' => User::find(rand(1, 9)),
            'category_id' => Category::findOrFail(rand(1, 4)),
            'slug' => $this->faker->slug(),
            'access' => rand(0, 1),
        ];
    }
}
