<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Comment;
use App\Models\User;
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
            } elseif (str_ends_with($request->url(), 'getFollowings')) {
                $users = [];
                foreach ($request->user()->followings as $following) {
                    $users[] = $following->id;
                }
                $sims = Simulation::where("access", "=", 1)
                    ->whereIn('user_id', $users);
            } elseif (str_ends_with($request->url(), 'filter')) {
                $sims = Simulation::where("access", "=", 1)
                    ->where('category_id', $request->post('cid'));
            } else {
                $sims = Simulation::where("access", "=", 1);
                $sim = Simulation::whereSlug('fake_sim')->first();
                $sim->views += 1;
                $sim->save();
            }

            if ($request->post('sort') == 1) {
                $sims = $sims->orderBy('likes');
            } elseif ($request->post('sort') == 2) {
                $sims = $sims->orderByDesc('created_at');
            } elseif ($request->post('sort') == 3) {
                $sims = $sims->orderBy('created_at');
            } else {
                $sims = $sims->orderByDesc('likes');
            }

            $simulations = $sims->skip($request->post('opt') * 10)
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
                    'views' => $simulation->views,
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
            $coms = Comment::where('simulation_id', $request->post('sid'))
                ->where('parent_id', '=', '0');
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
                foreach ($comment->children as $child) {
                    $data[] = [
                        'coid' => $child->id,
                        'content' => $child->content,
                        'create_time' => $child->created_at->format('Y-m-d h:i:s'),
                        'uid' => $child->user_id,
                        'uname' => $child->user->username,
                        'avatar' => Storage::url($child->user->avatar),
                        'sid' => $child->simulation_id,
                        'pid' => $child->parent_id
                    ];
                }
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

            if ($request->post('sort') == 1) {
                $sims = $sims->orderBy('likes');
            } elseif ($request->post('sort') == 2) {
                $sims = $sims->orderByDesc('created_at');
            } elseif ($request->post('sort') == 3) {
                $sims = $sims->orderBy('created_at');
            } else {
                $sims = $sims->orderByDesc('likes');
            }

            $simulations = $sims->skip($request->post('opt') * 10)
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
                    'views' => $simulation->views,
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

    protected function getUsers(Request $request)
    {
        try {
            if (str_ends_with($request->url(), 'followingList')) {
                $users = $request->user()->followings;
            } elseif (str_ends_with($request->url(), 'followerList')) {
                $users = $request->user()->followers;
            } elseif (str_ends_with($request->url(), 'searchUser')) {
                $users = User::where('username',
                    'like',
                    '%' . $request->post('key') . '%')
                    ->orWhere('id', '=', $request->post('key'))
                    ->get();
            } else {
                $users = User::all();
            }
            $data = [];
            foreach ($users as $user) {
                $sims = 0;
                $likes = 0;
                foreach ($user->simulations->where('access', '=', '1') as $simulation) {
                    $sims += 1;
                    $likes += $simulation->likes;
                }

                $data[] = [
                    'uid' => $user->id,
                    'uname' => $user->username,
                    'avatar' => Storage::url($user->avatar),
                    'sims' => $sims,
                    'likes' => $likes
                ];
            }

            $this->r['code'] = 200;
            $this->r['message'] = "获取用户列表成功";
            $this->r['data'] = $data;
            $this->r['number'] = $users->count();
        } catch (Exception $e) {
            $this->r['code'] = 400;
            $this->r['message'] = $e->getMessage();
        }

        return $this->r;
    }

    protected function getMessages(Request $request)
    {
        try {
            $messages = $request->user()->messages;
            $data = [];
            foreach ($messages as $message) {
                $data[] = [
                    'mid' => $message->id,
                    'state' => $message->state,
                    'class' => $message->class,
                    'uid' => $message->send_id,
                    'uname' => $message->send->username,
                    'sid' => $message->simulation_id,
                    'sname' => $message->simulation->version->name,
                    'coid' => $message->comment_id,
                    'content' => $message->content,
                    'create_time' => $message->created_at->format('Y-m-d h:i:s')
                ];
            }

            $this->r['code'] = 200;
            $this->r['message'] = "获取消息列表成功";
            $this->r['data'] = $data;
            $this->r['number'] = $messages->count();
        } catch (Exception $e) {
            $this->r['code'] = 400;
            $this->r['message'] = $e->getMessage();
        }

        return $this->r;
    }
}
