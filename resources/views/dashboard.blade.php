@extends('app')

@section('navbar')
    <li><a href="/">Home</a></li>
    <li><a href="/faq">FAQ</a></li>
    <li><a href="/docs">Documentation</a></li>
    <li class="active"><a href="/dashboard">Dashboard</a></li>
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h1>Dashboard</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <h2>My apps</h2>
            </div>
        </div>
        @if(count($apps) == 0)
        <div class="row mt">
            <div class="col-sm-12">
                <i>No apps created.</i>
            </div>
        </div>
        <hr>
        @endif
        @foreach($apps as $app)
            <div id="app-{{ $app->id}}">
                <div class="row mt">
                    <div class="col-sm-12">
                        <p><strong>Name:</strong> {{ $app->name}}</p>
                        <p><strong>Client ID:</strong> {{ $app->client_id}}</p>
                        <p><strong>Client Secret:</strong> {{ $app->client_secret}}</p>
                        <p><strong>Redirect URL:</strong> {{ $app->redirect_url}}</p>
                        @if($app->website)
                            <p><strong>Website:</strong> {{ $app->website}}</p>
                        @endif
                        <p>
                            <a href="/app/{{ $app->id }}/edit" class="btn btn-info">Edit app</a>
                            <button class="btn btn-danger" onclick="deleteApp({{ $app->toJson() }})">Delete app</button>
                        </p>
                    </div>
                </div>
                <hr>
            </div>
        @endforeach
        <div class="row mt mb">
            <div class="col-sm-12">
                <a href="/app/create" class="btn btn-success">Create new app</a>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<input id="token" type="hidden" value="{{ csrf_token() }}">
<script type="text/javascript" src="{{ asset('/js/dashboard.js') }}"></script>
@endsection
