<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Comment;
use Exception;
use App\Models\Simulation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ListController extends Controller
{
    private array $r = [
        'code' => -1,
        'message' => 'unknown error',
    ];

    protected function getSimulations(Request $request)
    {
        try {
            if (str_ends_with($request->url(), 'getHisSims')) {
                $sims = Simulation::where("access", "=", 1)
                    ->where('user_id', '=', $request->post('uid'));
            } elseif (str_ends_with($request->url(), 'search')) {
                $sims = Simulation::where("access", "=", 1)
                    ->whereHas('version', function (Builder $query) use ($request) {
                        $query
                            ->where(
                                'name', 'like',
                                '%' . $request->post('key') . '%'
                            )
                            ->orwhere(
                                'synopsis', 'like',
                                '%' . $request->post('key') . '%'
                            );
                    });
            } elseif (str_ends_with($request->url(), 'filter')) {
                $sims = Simulation::where("access", "=", 1)
                    ->where('category_id', $request->post('cid'));
            } else {
                $sims = Simulation::where("access", "=", 1);
            }

            $simulations = $sims->orderByDesc('likes')
                ->skip($request->post('opt') * 10)
                ->take(10)->get()
                ->load('version', 'category');

            $data = [];
            foreach ($simulations as $simulation) {
                $data[] = [
                    'sid' => $simulation->id,
                    'sname' => $simulation->version->name,
                    'cid' => $simulation->category_id,
                    'cname' => $simulation->category->name,
                    'synopsis' => $simulation->version->synopsis,
                    'likes' => $simulation->likes,
                    'uid' => $simulation->user_id,
                    'uname' => $simulation->user->username,
                    'create_time' => $simulation->created_at
                ];
            }

            $this->r['code'] = 200;
            $this->r['message'] = "获取模拟成功";
            $this->r['data'] = $data;
            $this->r['number'] = $sims->count();
        } catch (Exception $e) {
            $this->r['code'] = 400;
            $this->r['message'] = $e->getMessage();
        }

        return $this->r;
    }

    protected function getCategories()
    {
        try {
            $categories = Category::all();
            $data = [];
            foreach ($categories as $category) {
                $data[] = [
                    'cid' => $category->id,
                    'cname' => $category->name
                ];
            }
            $this->r['code'] = 200;
            $this->r['message'] = "获取分类成功";
            $this->r['data'] = $data;
        } catch (Exception $e) {
            $this->r['code'] = 400;
            $this->r['message'] = $e->getMessage();
        }

        return $this->r;
    }

    protected function getComments(Request $request)
    {
        try {
            $coms = Comment::where('simulation_id', $request->post('sid'));
            $comments = $coms->skip($request->post('opt') * 10)
                ->take(10)->get();
            $data = [];
            foreach ($comments as $comment) {
                $data[] = [
                    'coid' => $comment->id,
                    'content' => $comment->content,
                    'create_time' => $comment->created_at->format('Y-m-d h:i:s'),
                    'uid' => $comment->user_id,
                    'uname' => $comment->user->username,
                    'avatar' => Storage::url($comment->user->avatar),
                    'sid' => $comment->simulation_id,
                    'pid' => $comment->parent_id
                ];
            }
            $this->r['code'] = 200;
            $this->r['message'] = "获取分类成功";
            $this->r['data'] = $data;
            $this->r['number'] = $coms->count();
        } catch (Exception $e) {
            $this->r['code'] = 400;
            $this->r['message'] = $e->getMessage();
        }

        return $this->r;
    }

    protected function getMySimulations(Request $request)
    {
        try {
            $sims = Simulation::where('user_id', '=', $request->user()->id);
            $simulations = $sims->orderByDesc('likes')
                ->skip($request->post('opt') * 10)
                ->take(10)->get()
                ->load('version', 'category');

            $data = [];
            foreach ($simulations as $simulation) {
                $data[] = [
                    'sid' => $simulation->id,
                    'sname' => $simulation->version->name,
                    'cid' => $simulation->category_id,
                    'cname' => $simulation->category->name,
                    'synopsis' => $simulation->version->synopsis,
                    'likes' => $simulation->likes,
                    'uid' => $simulation->user_id,
                    'uname' => $simulation->user->username,
                    'create_time' => $simulation->created_at,
                    'access' => $simulation->access
                ];
            }

            $this->r['code'] = 200;
            $this->r['message'] = "获取模拟成功";
            $this->r['data'] = $data;
            $this->r['number'] = $sims->count();
        } catch (Exception $e) {
            $this->r['code'] = 400;
            $this->r['message'] = $e->getMessage();
        }

        return $this->r;
    }
}
