@extends('layouts.public')

@section('title', $folder ? $folder->name : $user->email)
@section('user-label', $user->email)

@section('content')
  <nav class="text-sm text-gray-500 mb-6">
    <a href="/{{ rawurlencode($user->email) }}" class="text-indigo-600 hover:underline">{{ $user->email }}</a>
    @foreach ($ancestors as $a)
      <span class="mx-1">/</span>
      @if (!$loop->last)
        <a href="/{{ rawurlencode($user->email) }}{{ $a['url'] }}" class="text-indigo-600 hover:underline">{{ $a['folder']->name }}</a>
      @else
        <span class="text-gray-800 font-medium">{{ $a['folder']->name }}</span>
      @endif
    @endforeach
  </nav>

  @if ($folder)
    <h1 class="text-2xl font-bold mb-6">{{ $folder->name }}</h1>
  @else
    <h1 class="text-2xl font-bold mb-6">{{ $user->email }}'s bookmarks</h1>
  @endif

  @if ($folders->isNotEmpty())
    <h2 class="text-lg font-semibold text-gray-700 mb-3">{{ $folder ? 'Subfolders' : 'Folders' }}</h2>
    <div class="grid gap-2 mb-8">
      @foreach ($folders as $sub)
        @php $parentPath = $ancestors ? $ancestors[count($ancestors) - 1]['url'] : ''; @endphp
        <a href="/{{ rawurlencode($user->email) }}{{ $parentPath }}/{{ rawurlencode($sub->name) }}"
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

  @if ($folders->isEmpty() && $bookmarks->isEmpty())
    <p class="text-gray-500">{{ $folder ? 'This folder is empty.' : 'No bookmarks yet.' }}</p>
  @endif
@endsection
