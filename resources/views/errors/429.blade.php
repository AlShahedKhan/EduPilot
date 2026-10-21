@extends('errors.layout')

@section('code', '429')
@section('title', function_exists('get_phrase') ? get_phrase('Too Many Requests') : 'Too Many Requests')
@section('message', function_exists('get_phrase') ? get_phrase('You have sent too many requests in a given amount of time. Please wait a moment and try again.') : 'You have sent too many requests in a given amount of time. Please wait a moment and try again.')
@section('icon')
    <i class="bi bi-speedometer2"></i>
@endsection
@section('accent-rgb', '234, 179, 8')
