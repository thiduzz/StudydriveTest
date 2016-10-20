@extends('layouts.app')
@section('title')
    <h1 class="page-header">Home</h1>
@endsection
@section('breadcrumbs')
    <li class="active">Home</li>
@endsection
@section('content')
    <div class="container">

        @if (session()->has('activation_warning'))
            <div class="row">
                <div class="col-sm-12 col-md-8 col-md-offset-2">
                    <div class="alert bg-warning" role="alert">
                        <svg class="glyph stroked flag"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#stroked-flag"></use></svg>
                        {!! session()->get('activation_warning') !!}<a href="#" class="pull-right"><span class="glyphicon glyphicon-remove"></span></a>
                    </div>
                </div>
            </div>
        @elseif(session()->has('activated_success'))
            <div class="row">
                <div class="col-sm-12 col-md-8 col-md-offset-2">
                    <div class="alert bg-success" role="alert">
                        <svg class="glyph stroked checkmark"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#stroked-checkmark"></use></svg>
                        {!! session()->get('activated_success') !!} <a href="#" class="pull-right"><span class="glyphicon glyphicon-remove"></span></a>
                    </div>
                </div>
            </div>
            <?php session()->forget('activated_success'); ?>
        @endif

        <logged-panel></logged-panel>
    </div>
@endsection
