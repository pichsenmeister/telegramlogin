@extends('app')

@section('navbar')
    <li class="active"><a href="/">Home</a></li>
    <li><a href="/faq">FAQs</a></li>
    <li><a href="/docs">Documentation</a></li>
    <li><a href="/contact">Contact</a></li>
    @if(Auth::user())
        <li><a href="/dashboard">Dashboard</a></li>
    @endif
@endsection

@section('content')
    <div class="hero">
        <div class="logo">
            <i class="fa fa-6 fa-key logo-key"></i>
        </div>
        <h1>TelegramLogin</h1>
        <div class="text-muted"><small>(unofficial)</small></div>
    </div>
@endsection
