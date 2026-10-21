@extends('errors.layout')

@section('code', '402')
@section('title', function_exists('get_phrase') ? get_phrase('Payment Required') : 'Payment Required')
@section('message', function_exists('get_phrase') ? get_phrase('Payment is required to access this feature or subscription plan.') : 'Payment is required to access this feature or subscription plan.')
@section('icon')
    <i class="bi bi-credit-card-2-front-fill"></i>
@endsection
@section('accent-rgb', '16, 185, 129')
