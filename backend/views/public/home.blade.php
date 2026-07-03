@extends('layouts.public')

@section('title', 'Home')

@section('content')
  <h1 class="text-3xl font-bold mb-4">bookmarkFox</h1>

  <p class="text-gray-600 mb-8">
    Sync and share your Firefox bookmarks with the world.
    This backend powers the bookmarkFox browser extension and hosts
    public bookmark pages for every registered user.
  </p>

  <div class="mb-8">
    <a href="https://github.com/b0sh-net/bookmarkFox" target="_blank" rel="noopener noreferrer"
       class="inline-flex items-center gap-2 px-4 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition text-sm">
      <svg viewBox="0 0 16 16" class="w-4 h-4" fill="currentColor"><path d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27s1.36.09 2 .27c1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.013 8.013 0 0 0 16 8c0-4.42-3.58-8-8-8"/></svg>
      Source code on GitHub
    </a>
  </div>

  <h2 class="text-xl font-semibold text-gray-700 mb-4">Users ({{ $users->count() }})</h2>

  @if ($users->isEmpty())
    <p class="text-gray-500">No users registered yet.</p>
  @else
    <div class="overflow-hidden bg-white rounded-lg border border-gray-200">
      <table class="w-full text-sm">
        <thead>
          <tr class="bg-gray-50 border-b border-gray-200">
            <th class="text-left px-4 py-3 font-medium text-gray-600">Email</th>
            <th class="text-left px-4 py-3 font-medium text-gray-600">Registered</th>
            <th class="text-left px-4 py-3 font-medium text-gray-600">Bookmarks</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          @foreach ($users as $user)
            <tr class="hover:bg-gray-50">
              <td class="px-4 py-3">
                <a href="/{{ rawurlencode($user->email) }}" class="text-indigo-600 hover:underline">
                  {{ $user->email }}
                </a>
              </td>
              <td class="px-4 py-3 text-gray-500">
                {{ $user->created_at->format('Y-m-d') }}
              </td>
              <td class="px-4 py-3 text-gray-500">
                {{ $user->bookmarks()->count() }}
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @endif
@endsection
