<?php

namespace App\Http\Controllers;

use App\Mail\RegisteredMail;
use App\Models\User;
use Auth;
use Exception;
use Hash;
use Illuminate\Http\Request;
use Mail;

class UserAuthController extends Controller
{
    private array $r = [
        'code' => -1,
        'message' => 'unknown error',
    ];

    protected function register(Request $request): array
    {
        try {
            $user_name = $request->post('username');
            $email = $request->post('email');
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
            $user->password = Hash::make($password);
            $user->save();

            Mail::to($user)->send(new RegisteredMail($user));

            Auth::login($user);


            $this->r['code'] = 200;
            $this->r['message'] = '注册成功，等待邮箱验证';
        } catch (Exception $e) {
            $this->r['code'] = 400;
            $this->r['message'] = $e->getMessage();
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
            $emailOrUsername = $request->post('emailOrUsername');
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
                $this->r['code'] = 200;
                $this->r['message'] = "登陆成功";
                $this->r['data'] = User::whereEmail($email)->first();
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
                $this->r['code'] = 200;
                $this->r['message'] = "密码修改成功";
            }
        } catch (Exception $e) {
            $this->r['code'] = 400;
            $this->r['message'] = $e->getMessage();
        }

        return $this->r;
    }
}
