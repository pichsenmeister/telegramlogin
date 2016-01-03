@extends('app')

@section('navbar')
    <li><a href="/">Home</a></li>
    <li><a href="/faq">FAQ</a></li>
    <li class="active"><a href="/docs">Documentation</a></li>
    @if(Auth::user())
        <li><a href="/dashboard">Dashboard</a></li>
    @endif
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h1>Documentation</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">

            </div>
        </div>
    </div>
@endsection
