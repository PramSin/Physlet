<?php

namespace Database\Factories;

use App\Models\Simulation;
use App\Models\SimulationWithVersion;
use Illuminate\Database\Eloquent\Factories\Factory;

class SimulationWithVersionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SimulationWithVersion::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $sim = Simulation::factory()->create();
        return [
            'name' => $this->faker->word(),
            'slug' => $this->faker->slug(),
            'simulation_id' => $sim,
            'status_id' => $sim,
            'synopsis' => $this->faker->sentence(),
            'root_path' => $this->faker->filePath(),
        ];
    }
}
