<?php

namespace App\Http\Controllers;

use App\Models\Simulation;
use App\Models\User;
use Illuminate\Http\Request;
use League\Flysystem\Exception;

class OtherController extends Controller
{
    private array $r = [
        'code' => -1,
        'message' => 'unknown error',
    ];

    protected function mainViews()
    {
        try {
            $data = [
                "counts" => Simulation::sum("views")
            ];
            $this->r['code'] = 200;
            $this->r['message'] = "获取模拟数量成功";
            $this->r['data'] = $data;
        } catch (Exception $e) {
            $this->r['code'] = 400;
            $this->r['message'] = $e->getMessage();
        }

        return $this->r;
    }

    protected function addTime(Request $request)
    {
        try {
            $user = User::findOrFail(1);
            if (str_ends_with($request->url(), 'addTime')) {
                $user->time += 1;
            }
            $data = [
                "time" => $user->time
            ];
            $user->save();
            $this->r['code'] = 200;
            $this->r['message'] = "获取模拟数量成功";
            $this->r['data'] = $data;
        } catch (Exception $e) {
            $this->r['code'] = 400;
            $this->r['message'] = $e->getMessage();
        }

        return $this->r;
    }

    protected function addUserTime(Request $request)
    {
        try {
            $user = $request->user();
            if (str_ends_with($request->url(), 'addUserTime')) {
                if ($user->id != 1) {
                    $user->time += 1;
                }
            }
            $data = [
                "time" => $user->time
            ];
            $user->save();
            $this->r['code'] = 200;
            $this->r['message'] = "获取模拟数量成功";
            $this->r['data'] = $data;
        } catch (Exception $e) {
            $this->r['code'] = 400;
            $this->r['message'] = $e->getMessage();
        }

        return $this->r;
    }
}
