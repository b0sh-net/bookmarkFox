<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'bookmarkFox') &middot; bookmarkFox</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-900 min-h-screen">
  <header class="bg-white border-b border-gray-200">
    <div class="max-w-4xl mx-auto px-4 py-4 flex items-center justify-between">
      <a href="/" class="text-xl font-bold text-indigo-600">bookmarkFox</a>
      <span class="text-sm text-gray-500">@yield('user-label')</span>
    </div>
  </header>

  <main class="max-w-4xl mx-auto px-4 py-8">
    @yield('content')
  </main>
</body>
</html>
