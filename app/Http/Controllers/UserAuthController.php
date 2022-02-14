<?php

namespace App\Http\Controllers;

use App\Mail\RegisteredMail;
use App\Models\User;
use Auth;
use Exception;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Mail;

class UserAuthController extends Controller
{
    private array $r = [
        'code' => -1,
        'message' => 'unknown error',
    ];

    protected function myInfo(Request $request): array
    {
        $user = $request->user();

        $sims = 0;
        $likes = 0;
        foreach ($user->simulations as $simulation) {
            $sims += 1;
            $likes += $simulation->likes;
        }

        $data = [
            'uid' => $user->id,
            'uname' => $user->username,
            'avatar' => Storage::url($user->avatar),
            'sims' => $sims,
            'likes' => $likes
        ];
        $this->r['code'] = 200;
        $this->r['message'] = "获取用户信息成功";
        $this->r['data'] = $data;
        return $this->r;
    }

    protected function userInfo(Request $request)
    {
        $user = User::find($request->post('uid'));
        $sims = 0;
        $likes = 0;
        foreach ($user->simulations as $simulation) {
            $sims += 1;
            $likes += $simulation->likes;
        }

        $data = [
            'uid' => $user->id,
            'uname' => $user->username,
            'avatar' => Storage::url($user->avatar),
            'sims' => $sims,
            'likes' => $likes
        ];

        $this->r['code'] = 200;
        $this->r['message'] = "获取用户信息成功";
        $this->r['data'] = $data;
        return $this->r;
    }

    protected function checkLogin(Request $request)
    {
        $this->r['code'] = $request->user() ? 200 : 403;
        $this->r['message'] = $request->user() ? "已登录" : "当前用户未登录";
        return $this->r;
    }

    protected function register(Request $request): array
    {
        try {
            $user_name = $request->post('username');
            $email = $request->post('email');
            $number = $request->post('number');
            $password = $request->post('password');
            if (User::whereEmail($email)->exists()) {
                throw new Exception("邮箱{$email}已注册");
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception("无法向邮箱{$email}发送文件");
            }

            $user = new User();
            $user->username = $user_name;
            $user->slug = $user_name;
            $user->email = $email;
            $user->phone_number = $number;
            $user->password = Hash::make($password);
            $user->save();

            Mail::to($user)->send(new RegisteredMail($user));

//            Auth::login($user);
            $data = [
                'uid' => $user->id,
                'uname' => $user->username
            ];
            $this->r['code'] = 200;
            $this->r['message'] = '注册成功，等待邮箱验证';
            $this->r['data'] = $data;
        } catch (Exception $e) {
            $this->r['code'] = 400;
            $this->r['message'] = $e->getMessage();
            $user = User::whereEmail($request->post('email'));
            $user->delete();
        }

        return $this->r;
    }

    protected function confirmEmail(Request $request, $token)
    {
        try {
            $data = decrypt($token);

            $user_id = $data['user_id'];
            $user = User::find($user_id);
            $user->email_verified_at = now();
            $user->save();

            Auth::login($user);

            return redirect(route('physlet', ['message' => "邮箱验证成功"]));
        } catch (Exception $exception) {
            return redirect(route('physlet', ['message' => $exception->getMessage()]));
        }
    }

    protected function login(Request $request): array
    {
        try {
            $emailOrUsername = $request->post('email_or_username');
            $password = $request->post('password');

            if (User::whereUsername($emailOrUsername)->exists()) {
                $email = User::whereUsername($emailOrUsername)->first()->email;
            } elseif (User::whereEmail($emailOrUsername)->exists()) {
                $email = $emailOrUsername;
            } else {
                $this->r['code'] = 400;
                $this->r['message'] = "用户名或邮箱未注册";

                return $this->r;
            }

            if (Auth::attempt(['email' => $email, 'password' => $password], true)) {
                $user = User::whereEmail($email)->first();

                $data = [
                    'uid' => $user->id,
                    'uname' => $user->username
                ];
                $this->r['code'] = 200;
                $this->r['message'] = "登陆成功";
                $this->r['data'] = $data;
            } else {
                $this->r['code'] = 400;
                $this->r['message'] = "密码错误";
            }
        } catch (Exception $e) {
            $this->r['code'] = 400;
            $this->r['message'] = $e->getMessage();
        }

        return $this->r;
    }

    protected function logout(): array
    {
        try {
            Auth::logout();
            $this->r['code'] = 200;
            $this->r['message'] = '注销成功';
        } catch (Exception $e) {
            $this->r['code'] = 400;
            $this->r['message'] = $e->getMessage();
        }
        return $this->r;
    }

    protected function changePassword(Request $request): array
    {
        try {
            $user = $request->user();
            $ori_password = $request->post('ori_password');
            $password = $request->post('password');
            if (!Auth::once(['email' => $user->email, 'password' => $ori_password])) {
                $this->r['code'] = 400;
                $this->r['message'] = "原密码错误";
            } else {
                $user->password = Hash::make($password);
                $user->save();

                Auth::logout();
                $data = [
                    'uid' => $user->id,
                    'uname' => $user->username
                ];
                $this->r['code'] = 200;
                $this->r['message'] = "密码修改成功";
                $this->r['data'] = $data;
            }
        } catch (Exception $e) {
            $this->r['code'] = 400;
            $this->r['message'] = $e->getMessage();
        }

        return $this->r;
    }

    protected function changeInfo(Request $request)
    {
        try {
            $user = $request->user();
            $uname = $request->post('username');
            $number = $request->post('number');

            $user->username = $uname;
            $user->phone_number = $number;
            $user->save();

            $data = [
                'uid' => $user->id,
                'uname' => $user->username
            ];
            $this->r['code'] = 200;
            $this->r['message'] = "信息修改成功";
            $this->r['data'] = $data;
        } catch (Exception $e) {
            $this->r['code'] = 400;
            $this->r['message'] = $e->getMessage();
        }

        return $this->r;
    }

    protected function uploadAvatar(Request $request)
    {
        try {
            if ($request->hasFile('image')) {
                $user = $request->user();
                if (Storage::exists($user->avatar)) {
                    Storage::delete($user->avatar);
                }
                $avatar = $request->file('image')
                    ->store("public/avatar");
                $user->avatar = $avatar;
                $user->save();
                $this->r['code'] = 200;
                $this->r['message'] = $avatar;
            } else {
                $this->r['code'] = 400;
                $this->r['message'] = $_FILES['file'];
                return $this->r;
            }
        } catch (Exception $e) {
            $this->r['code'] = 400;
            $this->r['message'] = $e->getMessage();
        }

        return $this->r;
    }
}
