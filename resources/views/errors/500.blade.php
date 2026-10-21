@extends('errors.layout')

@section('code', '500')
@section('title', function_exists('get_phrase') ? get_phrase('Internal Server Error') : 'Internal Server Error')
@section('message', function_exists('get_phrase') ? get_phrase('Something went wrong on our end. Our technical team has been notified.') : 'Something went wrong on our end. Our technical team has been notified.')
@section('icon')
    <i class="bi bi-cpu-fill"></i>
@endsection
@section('accent-rgb', '239, 68, 68')
