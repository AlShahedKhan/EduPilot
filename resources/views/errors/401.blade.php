@extends('errors.layout')

@section('code', '401')
@section('title', function_exists('get_phrase') ? get_phrase('Unauthorized Access') : 'Unauthorized Access')
@section('message', function_exists('get_phrase') ? get_phrase('You must be authenticated and logged in to access this page or resource.') : 'You must be authenticated and logged in to access this page or resource.')
@section('icon')
    <i class="bi bi-person-lock"></i>
@endsection
@section('accent-rgb', '99, 102, 241')
