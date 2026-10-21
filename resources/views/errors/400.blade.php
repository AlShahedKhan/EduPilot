@extends('errors.layout')

@section('code', '400')
@section('title', function_exists('get_phrase') ? get_phrase('Bad Request') : 'Bad Request')
@section('message', function_exists('get_phrase') ? get_phrase('The server could not understand the request due to invalid or malformed syntax.') : 'The server could not understand the request due to invalid or malformed syntax.')
@section('icon')
    <i class="bi bi-exclamation-triangle-fill"></i>
@endsection
@section('accent-rgb', '245, 158, 11')
