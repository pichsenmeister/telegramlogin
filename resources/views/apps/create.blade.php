@extends('app')

@section('navbar')
    <li><a href="/">Home</a></li>
    <li><a href="/faq">FAQs</a></li>
    <li><a href="/docs">Documentation</a></li>
    <li><a href="/contact">Contact</a></li>
    <li class="active"><a href="/dashboard">Dashboard</a></li>
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h2>Create new app</h2>
            </div>
        </div>
        @if(count($errors))
            <div class="row mt">
                <div class="col-sm-12">
                    <ul>
                        @foreach($errors->all() as $e)
                            <li class="text-danger">{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
        <div class="row mt">
            <div class="col-sm-8">
                <form action="/app" method="POST">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="form-group">
                        <label for="inputName">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" id="inputName" placeholder="Choose a name" value="{{ old('name') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="inputRedirectURL">Redirect URL <span class="text-danger">*</span></label>
                        <input type="text" name="redirect_url" class="form-control" id="inputRedirectURL" placeholder="http://" value="{{ old('redirect_url') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="inputWebsite">Website</label>
                        <input type="text" name="website" class="form-control" id="inputWebsite" value="{{ old('website') }}" placeholder="http://">
                    </div>
                    <a href="/dashboard" class="btn btn-warning">Cancel</a>
                    <button type="submit" class="btn btn-success">Create</button>
                </form>
            </div>
        </div>
    </div>
@endsection
