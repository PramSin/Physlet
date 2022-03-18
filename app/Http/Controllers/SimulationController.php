<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Message;
use App\Models\Simulation;
use App\Models\SimulationWithVersion;
use Exception;
use Illuminate\Contracts\Filesystem\FileExistsException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\Exception\UnexpectedTypeException;
use ZipArchive;

class SimulationController extends Controller
{
    private array $r = [
        'code' => -1,
        'message' => 'unknown error',
    ];

    /**
     * @throws FileExistsException
     * @throws Exception
     */
    protected static function unzip_file(string $filename): string
    {
        $direct = substr($filename, 0, strrpos($filename, '.')) . "/";
        $filename = storage_path("app/" . $filename);
        $ext = substr($filename, strrpos($filename, '.') + 1);
        $directory = substr($filename, 0, strrpos($filename, '.')) . "/";

        if (!is_file($filename)) {
            throw new FileExistsException("文件 $filename 不存在");
        }
        if ($ext !== 'zip') {
            throw new UnexpectedTypeException($filename, "zip");
        }
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }
        $zip = new ZipArchive();
        if ($zip->open($filename)) {
            $zip->extractTo($directory);
            $zip->close();
            return $direct . "index.html";
        } else {
            throw new Exception();
        }
    }

    protected function uploadSimulation(Request $request): array
    {
        try {
            $name = $request->post('sname');
            $category = $request->post('cid');
            $synopsis = $request->post('synopsis');
            $access = $request->post('access');

            if ($request->hasFile('file')) {
                $package = $request->file('file')
                    ->store("public/" . Category::findOrFail($category)->name);
                $file = self::unzip_file($package);
            } else {
                $this->r['code'] = 400;
                $this->r['message'] = $_FILES['file'];
                return $this->r;
            }

            $sim = Simulation::create([
                "user_id" => $request->user()->id,
                "category_id" => $category,
                "slug" => "abc",
                "access" => $access,
            ]);

            SimulationWithVersion::create([
                'simulation_id' => $sim->id,
                'status_id' => $sim->id,
                'name' => $name,
                "slug" => "abc-1",
                'root_path' => $file,
                'synopsis' => $synopsis,
            ]);

            $data = [
                'sid' => $sim->id,
                'url' => Storage::url($sim->version->root_path)
            ];
            $this->r['code'] = 200;
            $this->r['message'] = "模拟程序上传成功";
            $this->r['data'] = $data;
        } catch (Exception $e) {
            $this->r['code'] = 400;
            $this->r['message'] = $e->getMessage();
        }

        return $this->r;
    }

    protected function editSimulation(Request $request): array
    {
        try {
            $simulation = Simulation::findOrFail($request->post('sid'));
            if ($simulation->user->id === $request->user()->id) {
                $simulation->category_id = $request->post('cid');
                $simulation->access = $request->post('access');

                if ($request->hasFile('file')) {
                    $package = $request->file('file')
                        ->store("public/" . Category::findOrFail($request->post('cid'))
                                ->name
                        );
                    $file = self::unzip_file($package);
                } else {
                    $file = $simulation->version->root_path;
                }
                $simulation->version->name = $request->post('sname');
                $simulation->version->synopsis = $request->post('synopsis');
                $simulation->version->root_path = $file;

                $simulation->version->save();
                $simulation->save();

                $this->r['code'] = 200;
                $this->r['message'] = "模拟修改成功";
                $this->r['data'] = $simulation;
            } else {
                $this->r['code'] = 400;
                $this->r['message'] = "无法修改他人创建的模拟";
            }
        } catch (Exception $e) {
            $this->r['code'] = 400;
            $this->r['message'] = $e->getMessage();
        }

        return $this->r;
    }

    protected function deleteSimulation(Request $request): array
    {
        try {
            $simulation = Simulation::findOrFail($request->post('sid'));
            if ($request->user()->id === $simulation->user->id) {
                foreach ($simulation->versions as $version) {
                    $version->delete();
                }
                if ($simulation->delete()) {
                    $this->r['code'] = 200;
                    $this->r['message'] = "模拟删除成功";
                } else {
                    $this->r['code'] = 400;
                    $this->r['message'] = "模拟删除失败";
                }
            } else {
                $this->r['code'] = 400;
                $this->r['message'] = "无法删除他人创建的模拟";
            }
        } catch (Exception $e) {
            $this->r['code'] = 400;
            $this->r['message'] = $e->getMessage();
        }

        return $this->r;
    }

    protected function getSimulation(Request $request)
    {
        try {
            $sim = Simulation::find($request->post('sid'));

            if (Storage::exists($sim->version->root_path)) {
                $data = [
                    'sid' => $sim->id,
                    'sname' => $sim->version->name,
                    'cid' => $sim->category_id,
                    'cname' => $sim->category->name,
                    'synopsis' => $sim->version->synopsis,
                    'views' => $sim->views,
                    'likes' => $sim->likes,
                    'uid' => $sim->user_id,
                    'uname' => $sim->user->username,
                    'url' => Storage::url($sim->version->root_path),
                    'create_time' => $sim->created_at
                ];
                if ($sim->access) {
                    $sim->views += 1;
                    $sim->save();
                }
                $this->r['code'] = 200;
                $this->r['message'] = "获取模拟成功";
            } else {
                $data = [
                    'sid' => $sim->id,
                    'sname' => $sim->version->name,
                    'cid' => $sim->category_id,
                    'cname' => $sim->category->name,
                    'synopsis' => $sim->version->synopsis,
                    'likes' => $sim->likes,
                    'uid' => $sim->user_id,
                    'uname' => $sim->user->username,
                    'url' => Storage::url($sim->version->root_path),
                    'create_time' => $sim->created_at
                ];
                $this->r['code'] = 404;
                $this->r['message'] = "找不到相应的模拟文件";
            }
            $this->r['data'] = $data;
        } catch (Exception $e) {
            $this->r['code'] = 400;
            $this->r['message'] = $e->getMessage();
        }

        return $this->r;
    }

    protected function getVersion(Request $request): array
    {
        try {
            $simulation = Simulation::findOrFail($request->get('simulation'));
            $version = SimulationWithVersion::findOrFail($request->get('version'));
            if ($version->simulation->id === $simulation->id) {
                $this->r['code'] = 200;
                $this->r['message'] = "获取版本成功";
                $this->r['data'] = SimulationWithVersion::with('simulation')->findOrFail($request->post('version'));
            } else {
                $this->r['code'] = 400;
                $this->r['message'] = "获取版本失败";
            }
        } catch (Exception $e) {
            $this->r['code'] = 400;
            $this->r['message'] = $e->getMessage();
        }

        return $this->r;
    }

    protected function setVersion(Request $request): array
    {
        try {
            $simulation = Simulation::findOrFail($request->post('simulation'));
            if ($simulation->user->id === $request->user()->id) {
                $version = SimulationWithVersion::findOrFail($request->post('version'));
                if ($version->simulation->id === $simulation->id) {
                    $simulation->version->status_id = 0;
                    $version->status_id = $simulation->id;
                    $this->r['code'] = 200;
                    $this->r['message'] = "获取版本成功";
                    $this->r['data'] = $version;
                } else {
                    $this->r['code'] = 400;
                    $this->r['message'] = "获取版本失败";
                }
            } else {
                $this->r['code'] = 400;
                $this->r['message'] = "无法修改他人创建的模拟";
            }
        } catch (Exception $e) {
            $this->r['code'] = 400;
            $this->r['message'] = $e->getMessage();
        }

        return $this->r;
    }

    protected function addVersion(Request $request): array
    {
        try {
            $simulation = Simulation::findOrFail($request->post('simulation'));
            $name = $request->post('name');
            $synopsis = $request->post('synopsis');
            $package = $request->post('package');
            $status = $request->post('status');

            if ($status) {
                $status_id = $simulation->id;
            } else {
                $status_id = 0;
            }

            $siv = SimulationWithVersion::create([
                'simulation_id' => $simulation->id,
                'status_id' => $status_id,
                'name' => $name,
                "slug" => "abc-1",
                'root_path' => $package,
                'synopsis' => $synopsis,
            ]);

            $this->r['code'] = 200;
            $this->r['message'] = "添加新版本成功";
            $this->r['data'] = $siv;
        } catch (Exception $e) {
            $this->r['code'] = 400;
            $this->r['message'] = $e->getMessage();
        }

        return $this->r;
    }

    protected function editVersion(Request $request): array
    {
        try {
            $sim = Simulation::findOrFail($request->post('simulation'));
            if ($request->user()->id === $sim->user->id) {
                $ver = SimulationWithVersion::findOrFail($request->post('version'));
                if ($ver->simulation->id === $sim->id) {
                    $name = $request->post('name');
                    $synopsis = $request->post('name');
                    if ($request->hasFile('package')) {
                        $package = $request->file('package')->store($sim->category->name);
                        Storage::delete($ver->root_path);
                        $ver->name = $name;
                        $ver->synopsis = $synopsis;
                        $ver->root_path = $package;
                        $ver->save();

                        $this->r['code'] = 200;
                        $this->r['message'] = "版本修改成功";
                    } else {
                        $this->r['code'] = 400;
                        $this->r['message'] = "无法接收文件";
                    }
                } else {
                    $this->r['code'] = 400;
                    $this->r['message'] = "版本修改失败";
                }
            } else {
                $this->r['code'] = 400;
                $this->r['message'] = "无法修改他人创建的模拟";
            }
        } catch (Exception $e) {
            $this->r['code'] = 400;
            $this->r['message'] = $e->getMessage();
        }
        return $this->r;
    }

    protected function deleteVersion(Request $request): array
    {
        try {
            $sim = Simulation::findOrFail($request->post('simulation'));
            if ($request->user()->id === $sim->user->id) {
                $ver = SimulationWithVersion::findOrFail($request->post('version'));
                if ($ver->simulation->id === $sim->id) {
                    if ($ver->status_id === $sim->id) {
                        $this->r['code'] = 200;
                        $this->r['message'] = "无法删除正在使用的版本";
                    } else {
                        $ver->delete();

                        $this->r['code'] = 200;
                        $this->r['message'] = "版本删除成功";
                    }
                } else {
                    $this->r['code'] = 400;
                    $this->r['message'] = "版本删除失败";
                }
            } else {
                $this->r['code'] = 400;
                $this->r['message'] = "无法修改他人创建的模拟";
            }
        } catch (Exception $e) {
            $this->r['code'] = 400;
            $this->r['message'] = $e->getMessage();
        }
        return $this->r;
    }

    protected function sendComment(Request $request)
    {
        try {
            $sid = $request->post('sid');
            $content = $request->post('content');
            $pid = $request->post('pid') ? $request->post('pid') : 0;

            $comment = Comment::create(
                [
                    'user_id' => $request->user()->id,
                    'simulation_id' => $sid,
                    'parent_id' => $pid,
                    'content' => $content
                ]
            );

            $comment->save();

            if ($pid == 0) {
                $message = Message::create(
                    [
                        'state' => '1',
                        'class' => '2',
                        'user_id' => Simulation::findOrFail($sid)->user_id,
                        'send_id' => $request->user()->id,
                        'simulation_id' => $sid,
                        'content' => $comment->content
                    ]
                );
            } else {
                $message = Message::create(
                    [
                        'state' => '1',
                        'class' => '3',
                        'user_id' => Comment::findOrFail($pid)->user_id,
                        'send_id' => $request->user()->id,
                        'simulation_id' => $sid,
                        'comment_id' => $pid,
                        'content' => $comment->content
                    ]
                );
            }

            $message->save();

            $this->r['code'] = 200;
            $this->r['message'] = "发送评论成功";
        } catch (Exception $e) {
            $this->r['code'] = 400;
            $this->r['message'] = $e->getMessage();
        }

        return $this->r;
    }

    protected function deleteComment(Request $request)
    {
        try {
            $com = Comment::find($request->post('coid'));
            if ($com->user->id === $request->user()->id) {
                $com->delete();
                $this->r['code'] = 200;
                $this->r['message'] = "删除评论成功";
            } else {
                $this->r['code'] = 400;
                $this->r['message'] = "无法删除他人的评论";
            }
        } catch (Exception $e) {
            $this->r['code'] = 400;
            $this->r['message'] = $e->getMessage();
        }

        return $this->r;
    }

    protected function checkLike(Request $request)
    {
        try {
            $user = $request->user();
            $sid = $request->post('sid');

            $this->r['code'] = 200;
            $this->r['message'] = "获取状态成功";
            $this->r['data'] = $user->liked()->where('id', '=', $sid)->exists();
        } catch (Exception $e) {
            $this->r['code'] = 400;
            $this->r['message'] = $e->getMessage();
        }

        return $this->r;
    }

    protected function like(Request $request)
    {
        try {
            $user = $request->user();
            $sid = $request->post('sid');
            $sim = Simulation::find($sid);

            if ($user->liked()->where('id', '=', $sid)->exists()) {
                $user->liked()->detach($sid);
                $sim->likes -= 1;
                $this->r['message'] = "取消点赞";
            } else {
                $user->liked()->attach($sid);
                $sim->likes += 1;
                $this->r['message'] = "点赞成功";
            }
            $sim->save();
            $this->r['code'] = 200;
        } catch (Exception $e) {
            $this->r['code'] = 400;
            $this->r['message'] = $e->getMessage();
        }

        return $this->r;
    }

    protected function download(Request $request)
    {
        try {
            $sim = Simulation::find($request->post('sid'));
            $string = $sim->version->root_path;
            return Storage::download(substr($string, 0, strlen($string) - 11) . ".zip");
        } catch (Exception $e) {
            $this->r['code'] = 400;
            $this->r['message'] = $e->getMessage();
            return $this->r;
        }
    }

    protected function read(Request $request)
    {
        try {
            $messages = Message::findMany($request->post('mids'));
            foreach ($messages as $message) {
                $message->state = 0;
                $message->save();
            }
            $this->r['code'] = 200;
            $this->r['message'] = "消息已读";
        } catch (Exception $e) {
            $this->r['code'] = 400;
            $this->r['message'] = $e->getMessage();
        }

        return $this->r;
    }

    protected function mark(Request $request)
    {
        try {
            $messages = Message::findMany($request->post('mids'));
            foreach ($messages as $message) {
                $message->state = 1;
                $message->save();
            }
            $this->r['code'] = 200;
            $this->r['message'] = "消息已标记为未读";
        } catch (Exception $e) {
            $this->r['code'] = 400;
            $this->r['message'] = $e->getMessage();
        }

        return $this->r;
    }
}
