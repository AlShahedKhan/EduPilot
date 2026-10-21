@extends('errors.layout')

@section('code', '502')
@section('title', function_exists('get_phrase') ? get_phrase('Bad Gateway') : 'Bad Gateway')
@section('message', function_exists('get_phrase') ? get_phrase('The server received an invalid response while acting as a gateway or proxy server.') : 'The server received an invalid response while acting as a gateway or proxy server.')
@section('icon')
    <i class="bi bi-diagram-3-fill"></i>
@endsection
@section('accent-rgb', '249, 115, 22')
