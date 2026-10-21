@extends('errors.layout')

@section('code', '422')
@section('title', function_exists('get_phrase') ? get_phrase('Unprocessable Content') : 'Unprocessable Content')
@section('message', function_exists('get_phrase') ? get_phrase('The request was well-formed but contained semantic or validation errors.') : 'The request was well-formed but contained semantic or validation errors.')
@section('icon')
    <i class="bi bi-exclamation-octagon-fill"></i>
@endsection
@section('accent-rgb', '236, 72, 153')
