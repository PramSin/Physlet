<?php

namespace App\Http\Controllers;

use App\Models\Category;
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
            return $directory . "index.html";
        } else {
            throw new Exception();
        }
    }

    public function getCategories(): array
    {
        $this->r['code'] = 200;
        $this->r['message'] = "获取目录成功";
        $this->r['data'] = Category::all();
        return $this->r;
    }

    public function getSimulations(): array
    {
        $this->r['code'] = 200;
        $this->r['message'] = "获取模拟成功";
        $this->r['data'] = Simulation::where("access", "=", 1)
            ->get()->load('version');
        return $this->r;
    }

    public function getMySimulations(Request $request): array
    {
        $this->r['code'] = 200;
        $this->r['message'] = "获取模拟成功";
        $this->r['data'] = $request->user()->simulations->load('version');
        return $this->r;
    }

    protected function uploadSimulation(Request $request): array
    {
        try {
            $name = $request->post('name');
            $category = $request->post('category');
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

            $this->r['code'] = 200;
            $this->r['message'] = "模拟程序上传成功";
            $this->r['data'] = $sim->load('version');
        } catch (Exception $e) {
            $this->r['code'] = 400;
            $this->r['message'] = $e->getMessage();
        }

        return $this->r;
    }

    protected function editSimulation(Request $request): array
    {
        try {
            $simulation = Simulation::findOrFail($request->post('simulation'));
            if ($simulation->user->id === $request->user()->id) {
                $simulation->category_id = $request->post('category');
                $simulation->access = $request->post('access');
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
            $simulation = Simulation::findOrFail($request->post('simulation'));
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

    protected function getPackage(Request $request): array
    {
        try {
            $ver = SimulationWithVersion::findOrFail($request->get('version'));
            if ($ver->simulation->access) {
                $url = Storage::Url(
                    $ver->root_path
                );

                $this->r['code'] = 200;
                $this->r['message'] = '获取版本链接成功';
                $this->r['data'] = $url;
            } else {
                $this->r['code'] = 400;
                $this->r['message'] = '没有当前模拟的查看权限';
            }
        } catch (Exception $e) {
            $this->r['code'] = 400;
            $this->r['message'] = $e->getMessage();
        }

        return $this->r;
    }
}
