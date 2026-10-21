@extends('errors.layout')

@section('code', '406')
@section('title', function_exists('get_phrase') ? get_phrase('Not Acceptable') : 'Not Acceptable')
@section('message', function_exists('get_phrase') ? get_phrase('The requested resource is incapable of generating content matching the acceptable criteria.') : 'The requested resource is incapable of generating content matching the acceptable criteria.')
@section('icon')
    <i class="bi bi-file-earmark-x-fill"></i>
@endsection
@section('accent-rgb', '168, 85, 247')
