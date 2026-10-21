@extends('errors.layout')

@section('code', '403')
@section('title', function_exists('get_phrase') ? get_phrase('Access Forbidden') : 'Access Forbidden')
@section('message', function_exists('get_phrase') ? get_phrase('You do not have permission to view or perform operations on this resource.') : 'You do not have permission to view or perform operations on this resource.')
@section('icon')
    <i class="bi bi-shield-slash-fill"></i>
@endsection
@section('accent-rgb', '239, 68, 68')
