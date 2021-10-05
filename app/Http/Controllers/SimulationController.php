<?php

namespace App\Http\Controllers;

use App\Models\Simulation;
use App\Models\SimulationWithVersion;
use Faker\Factory;
use Illuminate\Http\Request;

class SimulationController extends Controller
{
    private array $r = [
        'code' => -1,
        'message' => 'unknown error',
    ];

    protected function uploadSimulation(Request $request): array
    {
        $name = $request->post('name');
        $category = $request->post('category');
        $synopsis = $request->post('synopsis');
        $access = $request->post('access');
        $package = $request->file('package')->store('simulations');

        $sim = Simulation::create([
            "user_id" => $request->user()->id,
            "category_id" => $category,
            "slug" => Factory::create()->faker->slug(),
        ]);

        SimulationWithVersion::create([
            'simulation_id' => $sim,
            'root_path' => storage_path()
        ]);

        $this->r['code'] = 200;
        $this->r['message'] = "模拟程序上传成功";
        $this->r['data'] = 0;

        return $this->r;
    }
}
