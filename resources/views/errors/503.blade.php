@extends('errors.layout')

@section('code', '503')
@section('title', function_exists('get_phrase') ? get_phrase('Service Unavailable') : 'Service Unavailable')
@section('message', function_exists('get_phrase') ? get_phrase('The server is temporarily unable to handle the request due to maintenance or high traffic.') : 'The server is temporarily unable to handle the request due to maintenance or high traffic.')
@section('icon')
    <i class="bi bi-cloud-slash-fill"></i>
@endsection
@section('accent-rgb', '59, 130, 246')
