<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SmartPrint - Admin Dashboard')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body class="min-h-screen flex antialiased"
      data-dashboard-url="{{ route('admin.dashboard') }}"
      data-notifications-url="{{ route('admin.notifications') }}"
      data-monthly-sales="{{ json_encode($monthlySales ?? array_fill(0, 12, 0)) }}">

    @include('admin.partials.sidebar')

    <!-- MAIN CONTENT AREA -->
    <div class="flex-1 flex flex-col min-w-0">
        @include('admin.partials.header')

        <main class="p-10 space-y-8 flex-1 max-w-none w-full">
            @if ($errors->any())
                <div class="glass-card border border-red-500/40 text-red-300 rounded-2xl p-4 text-xs space-y-1">
                    @foreach ($errors->all() as $error)
                        <p><i class="fa-solid fa-circle-exclamation mr-1.5"></i>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    @yield('modals')

    <!-- Toast container -->
    <div id="toastBox" class="fixed top-32 right-4 z-[100] space-y-3 w-80 max-w-[calc(100vw-2rem)]"></div>

    <script src="{{ asset('js/admin-config.js') }}"></script>
    <script src="{{ asset('js/admin-notifications.js') }}"></script>
    <script src="{{ asset('js/admin.js') }}"></script>
</body>
</html>