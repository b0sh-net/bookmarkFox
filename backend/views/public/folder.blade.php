@extends('layouts.public')

@section('title', $folder->name)
@section('user-label', $user->email)

@section('content')
  <nav class="text-sm text-gray-500 mb-6">
    <a href="/{{ $user->email }}" class="text-indigo-600 hover:underline">{{ $user->email }}</a>
    @if ($folder->parent)
      <span class="mx-1">/</span>
      <a href="/{{ $user->email }}/{{ urlencode($folder->parent->name) }}"
         class="text-indigo-600 hover:underline">{{ $folder->parent->name }}</a>
    @endif
    <span class="mx-1">/</span>
    <span class="text-gray-800 font-medium">{{ $folder->name }}</span>
  </nav>

  <h1 class="text-2xl font-bold mb-6">{{ $folder->name }}</h1>

  @if ($subfolders->isNotEmpty())
    <h2 class="text-lg font-semibold text-gray-700 mb-3">Subfolders</h2>
    <div class="grid gap-2 mb-8">
      @foreach ($subfolders as $sub)
        <a href="/{{ $user->email }}/{{ urlencode($folder->name) }}/{{ urlencode($sub->name) }}"
           class="block p-3 bg-white rounded-lg border border-gray-200 hover:border-indigo-300 hover:shadow-sm transition">
          <span class="font-medium text-indigo-600">&#128193;</span>
          <span class="ml-2">{{ $sub->name }}</span>
        </a>
      @endforeach
    </div>
  @endif

  @if ($bookmarks->isNotEmpty())
    <h2 class="text-lg font-semibold text-gray-700 mb-3">Bookmarks</h2>
    <div class="grid gap-2">
      @foreach ($bookmarks as $bookmark)
        <a href="{{ $bookmark->url }}" target="_blank" rel="noopener noreferrer"
           class="block p-3 bg-white rounded-lg border border-gray-200 hover:border-indigo-300 hover:shadow-sm transition">
          <span class="text-blue-600 font-medium">{{ $bookmark->title }}</span>
          <span class="block text-sm text-gray-400 truncate">{{ $bookmark->url }}</span>
        </a>
      @endforeach
    </div>
    <div class="mt-6">
      {{ $bookmarks->links() }}
    </div>
  @endif

  @if ($subfolders->isEmpty() && $bookmarks->isEmpty())
    <p class="text-gray-500">This folder is empty.</p>
  @endif
@endsection
