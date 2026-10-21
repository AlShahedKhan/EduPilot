@extends('errors.layout')

@section('code', '410')
@section('title', function_exists('get_phrase') ? get_phrase('Resource Gone') : 'Resource Gone')
@section('message', function_exists('get_phrase') ? get_phrase('The requested resource is no longer available and has been permanently removed.') : 'The requested resource is no longer available and has been permanently removed.')
@section('icon')
    <i class="bi bi-trash3-fill"></i>
@endsection
@section('accent-rgb', '100, 116, 139')
