@extends('layouts.app')

@section('breadcrumbs')
    <li class="active">Password Reset</li>
@endsection

        <!-- Main Content -->
@section('content')
<div class="container">
    @if (session('status'))
        <div class="row" style="margin-top: 50px;">
            <div class="col-sm-12 col-md-8 col-md-offset-2">
                <div class="alert bg-success" role="alert">
                    <svg class="glyph stroked checkmark"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#stroked-checkmark"></use></svg>
                    {{ session('status') }} <a href="#" class="pull-right"><span class="glyphicon glyphicon-remove"></span></a>
                </div>
            </div>
        </div>
    @endif
    @if ($errors->has('email'))
        <div class="row" style="margin-top: 50px;">
            <div class="col-sm-12 col-md-8 col-md-offset-2">
                <div class="alert bg-danger" role="alert">
                    <svg class="glyph stroked cancel"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#stroked-cancel"></use></svg>
                    {{ $errors->first('email') }} <a href="#" class="pull-right"><span class="glyphicon glyphicon-remove"></span></a>
                </div>
            </div>
        </div>
    @endif
    <div class="row">
        <div class="col-xs-10 col-xs-offset-1 col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3">

            <form class="form-horizontal" role="form" method="POST" action="{{ url('/password/email') }}">
                <div class="panel panel-blue">
                    <div class="panel-heading dark-overlay">
                        <svg class="glyph stroked lock"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#stroked-lock"/></svg>Password Reset</div>

                        {{ csrf_field() }}
                        <div class="panel-body white-panel">

                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                <label for="email" class="col-md-4 control-label">E-mail</label>

                                <div class="col-md-6">
                                    <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="panel-footer">
                            <div class="form-group">
                                <div class="col-md-6 col-md-offset-4">
                                    <button type="submit" class="btn btn-primary btn-md">
                                        Send Password Reset Link
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
