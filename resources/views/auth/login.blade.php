@extends('layouts.app')

@section('breadcrumbs')
    <li class="active">Login/Register</li>
@endsection

@section('content')

<div class="container">
    @if (session()->has('activation_failed'))
        <div class="row" style="margin-top: 50px;">
            <div class="col-sm-12 col-md-8 col-md-offset-2">
                <div class="alert bg-danger" role="alert">
                    <svg class="glyph stroked cancel"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#stroked-cancel"></use></svg> {!! session()->get('activation_failed') !!} <a href="#" class="pull-right"><span class="glyphicon glyphicon-remove"></span></a>
                </div>
            </div>
        </div>
        <?php session()->forget('activation_failed'); ?>
    @elseif(session()->has('activated_success'))
        <div class="row" style="margin-top: 50px;">
            <div class="col-sm-12 col-md-8 col-md-offset-2">
                <div class="alert bg-success" role="alert">
                    <svg class="glyph stroked checkmark"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#stroked-checkmark"></use></svg>
                    {!! session()->get('activated_success') !!} <a href="#" class="pull-right"><span class="glyphicon glyphicon-remove"></span></a>
                </div>
            </div>
        </div>
        <?php session()->forget('activated_success'); ?>
    @endif
    <div class="row">
        <div class="col-xs-10 col-xs-offset-1 col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3">
            <login-register></login-register>

        </div>
    </div>
</div>
@endsection
