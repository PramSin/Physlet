<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $user = User::findOrFail(1);
        return [
            'state' => rand(0, 1),
            'class' => 2,
            'user_id' => 1,
            'send_id' => array_rand(User::all()->toarray()) + 1,
            'simulation_id' => $user->simulations[rand(0, $user->simulations()->count() - 1)]->id,
            'content' => $this->faker->sentence
        ];
    }
}
