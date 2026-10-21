@extends('errors.layout')

@section('code', '409')
@section('title', function_exists('get_phrase') ? get_phrase('Resource Conflict') : 'Resource Conflict')
@section('message', function_exists('get_phrase') ? get_phrase('The request could not be completed due to a conflict with the current state of the resource.') : 'The request could not be completed due to a conflict with the current state of the resource.')
@section('icon')
    <i class="bi bi-intersect"></i>
@endsection
@section('accent-rgb', '225, 29, 72')
