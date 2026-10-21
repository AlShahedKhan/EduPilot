@extends('errors.layout')

@section('code', '415')
@section('title', function_exists('get_phrase') ? get_phrase('Unsupported Media Type') : 'Unsupported Media Type')
@section('message', function_exists('get_phrase') ? get_phrase('The media format of the requested data or file upload is not supported by the server.') : 'The media format of the requested data or file upload is not supported by the server.')
@section('icon')
    <i class="bi bi-file-earmark-code-fill"></i>
@endsection
@section('accent-rgb', '79, 70, 229')
