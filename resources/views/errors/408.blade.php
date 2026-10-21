@extends('errors.layout')

@section('code', '408')
@section('title', function_exists('get_phrase') ? get_phrase('Request Timeout') : 'Request Timeout')
@section('message', function_exists('get_phrase') ? get_phrase('The server timed out waiting for the request to complete. Please try submitting again.') : 'The server timed out waiting for the request to complete. Please try submitting again.')
@section('icon')
    <i class="bi bi-hourglass-split"></i>
@endsection
@section('accent-rgb', '217, 119, 6')
