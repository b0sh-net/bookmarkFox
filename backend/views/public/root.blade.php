@extends('layouts.public')

@section('title', $user->email)
@section('user-label', $user->email)

@section('content')
  <h1 class="text-2xl font-bold mb-6">{{ $user->email }}'s bookmarks</h1>

  @if ($folders->isEmpty() && $bookmarks->isEmpty())
    <p class="text-gray-500">No bookmarks yet.</p>
  @endif

  @if ($folders->isNotEmpty())
    <h2 class="text-lg font-semibold text-gray-700 mb-3">Folders</h2>
    <div class="grid gap-2 mb-8">
      @foreach ($folders as $folder)
        <a href="/{{ $user->email }}/{{ urlencode($folder->name) }}"
           class="block p-3 bg-white rounded-lg border border-gray-200 hover:border-indigo-300 hover:shadow-sm transition">
          <span class="font-medium text-indigo-600">&#128193;</span>
          <span class="ml-2">{{ $folder->name }}</span>
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
@endsection
