@extends('errors.layout')

@section('code', '501')
@section('title', function_exists('get_phrase') ? get_phrase('Not Implemented') : 'Not Implemented')
@section('message', function_exists('get_phrase') ? get_phrase('The server does not support the functionality required to fulfill the request.') : 'The server does not support the functionality required to fulfill the request.')
@section('icon')
    <i class="bi bi-tools"></i>
@endsection
@section('accent-rgb', '6, 182, 212')
