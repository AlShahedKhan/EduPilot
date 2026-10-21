@extends('errors.layout')

@section('code', '405')
@section('title', function_exists('get_phrase') ? get_phrase('Method Not Allowed') : 'Method Not Allowed')
@section('message', function_exists('get_phrase') ? get_phrase('The HTTP method used for this request is not supported by the requested route.') : 'The HTTP method used for this request is not supported by the requested route.')
@section('icon')
    <i class="bi bi-arrow-left-right"></i>
@endsection
@section('accent-rgb', '249, 115, 22')
