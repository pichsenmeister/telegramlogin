@extends('app')

@section('navbar')
    <li><a href="/docs">Documentation</a></li>
    <li><a href="/dashboard">Dashboard</a></li>
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-sm-4 text-center">
                @if($user->avatar)
                    <img src="{{ $user->avatar }}" class="profile-avatar" alt="avatar">
                @else
                    <img src="{{ asset('img/logo-white.png') }}" class="profile-avatar" alt="avatar">
                @endif
            </div>
            <div class="col-sm-8">
                <p><strong>Name:</strong> {{ $user->name }}</p>
                @if($user->username)
                    <p><strong>Username:</strong> <a href="https://telegram.me/{{ $user->username }}" target="_blank">{{ '@'.$user->username }}</a></p>
                @endif
                <p><strong>Telegram ID:</strong> {{ $user->telegram_id }}</p>
                <!-- <p><strong>Generated email:</strong> {{ $user->email }}</p> -->
                <p><a href="/logout">Logout</a></p>
            </div>
        </div>
        <div class="row mtl">
            <div class="col-sm-12">
                Raw data:
            </div>
            <div class="col-sm-12">
<pre id="telegramlogin-user-response">{
    id: {{ $user->id }},
    email: "{{ $user->email }}"",
    access_token: "{{ $user->access_token }}",
    active: 1,
    created_at: "{{ $user->created_at }}",
    telegram_user: {
        telegram_id: {{ $user->telegram_id }},
        name: "{{ $user->name }}",
        username: "{{ $user->username }}",
        avatar: "{{ $user->avatar }}"
    }
}</pre>
            </div>
        </div>
    </div>
@endsection
