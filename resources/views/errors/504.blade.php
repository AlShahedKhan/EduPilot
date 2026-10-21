@extends('errors.layout')

@section('code', '504')
@section('title', function_exists('get_phrase') ? get_phrase('Gateway Timeout') : 'Gateway Timeout')
@section('message', function_exists('get_phrase') ? get_phrase('The upstream server failed to respond in a timely manner. Please refresh or try again.') : 'The upstream server failed to respond in a timely manner. Please refresh or try again.')
@section('icon')
    <i class="bi bi-stopwatch-fill"></i>
@endsection
@section('accent-rgb', '225, 29, 72')
