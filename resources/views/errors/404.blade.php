@extends('errors.layout')

@section('code', '404')
@section('title', function_exists('get_phrase') ? get_phrase('Page Not Found') : 'Page Not Found')
@section('message', function_exists('get_phrase') ? get_phrase('The page you are looking for might have been moved, deleted, or does not exist.') : 'The page you are looking for might have been moved, deleted, or does not exist.')
@section('icon')
    <i class="bi bi-compass-fill"></i>
@endsection
@section('accent-rgb', '14, 165, 233')
