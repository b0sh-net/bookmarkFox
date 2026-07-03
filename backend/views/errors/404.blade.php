@extends('layouts.public')

@section('title', 'Not Found')

@section('content')
  <div class="text-center py-16">
    <h1 class="text-4xl font-bold text-gray-300 mb-4">404</h1>
    <p class="text-lg text-gray-600">{{ $message ?? 'Page not found.' }}</p>
    <a href="/" class="mt-6 inline-block text-indigo-600 hover:underline">Go to homepage</a>
  </div>
@endsection
