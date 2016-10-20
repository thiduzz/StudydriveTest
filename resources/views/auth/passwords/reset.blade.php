@extends('layouts.app')

@section('breadcrumbs')
    <li class="active">Password Reset</li>
@endsection

@section('content')
<div class="container">


    @if (count($errors->all()) > 0)
        <div class="row" style="margin-top: 50px;">
            <div class="col-sm-12 col-md-8 col-md-offset-2">
                <div class="alert bg-danger" role="alert">
                    <svg class="glyph stroked cancel"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#stroked-cancel"></use></svg>
                    <ul style="display: inline-block;">
                        @foreach($errors->all() as $error)
                            {{ $error }}
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif
    <div class="row">

        <div class="col-xs-10 col-xs-offset-1 col-sm-8 col-sm-offset-2 col-md-4 col-md-offset-4">
            <form class="form-horizontal" role="form" method="POST" action="{{ url('/password/reset') }}">
                <div class="panel panel-blue">
                    <div class="panel-heading dark-overlay">
                        <svg class="glyph stroked lock"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#stroked-lock"/></svg>Password Reset</div>

                        {{ csrf_field() }}
                        <div class="panel-body  white-panel">
                            <input type="hidden" name="token" value="{{ $token }}">

                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                <label for="email" class="col-md-4 control-label">E-mail</label>

                                <div class="col-md-6">
                                    <input id="email" type="email" class="form-control" name="email" value="{{ $email or old('email') }}" required autofocus>

                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                <label for="password" class="col-md-4 control-label">Password</label>

                                <div class="col-md-6">
                                    <input id="password" type="password" class="form-control" name="password" required>

                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                                <label for="password-confirm" class="col-md-4 control-label">Confirm Password</label>
                                <div class="col-md-6">
                                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>

                                </div>
                            </div>
                        </div>
                        <div class="panel-footer">
                            <div class="form-group">
                                <div class="col-md-6 col-md-offset-4">
                                    <button type="submit" class="btn btn-primary btn-md">
                                        Reset Password
                                    </button>
                                </div>
                            </div>
                        </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
