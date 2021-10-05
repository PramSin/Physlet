<h1 style="color: #E6A23C;font-family: sans-serif;">Physlet</h1>
<p style="font-family: sans-serif;">亲爱的 {{ $user->username }}：</p>
<p style="font-family: sans-serif;">您好！欢迎使用 Physlet 模拟实验平台</p>
@php
    $url = route('physlet.confirmEmail', ['token' => encrypt(['user_id' => $user->id])]);
@endphp
<p style="font-family: sans-serif;">请点击 <a href="{{ $url }}">此链接</a> 以验证您的邮箱。</p>
<p style="color: #888888;font-family: sans-serif;">若您无法点击上述链接，亦可复制以下链接然后在新窗口中打开：</p>
<a href="{{ $url }}"><p style="color: #888888;font-family: sans-serif;">{{ $url }}</p></a>
<p>&nbsp;</p>
<p style="color: #888888;font-family: sans-serif;">如果这不是您的操作，请忽略本邮件。如有打扰，万分抱歉。</p>
