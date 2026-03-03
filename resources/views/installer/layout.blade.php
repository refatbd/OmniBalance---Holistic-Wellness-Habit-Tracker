<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Install - OmniBalance</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background-color: #f3f4f6; }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">

    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl overflow-hidden">
        <div class="bg-emerald-500 p-6 text-center">
            <h1 class="text-2xl font-bold text-white">Setup App</h1>
            <p class="text-emerald-100 text-sm mt-1">OmniBalance - Daily Wellness Hub</p>
        </div>
        
        <div class="p-6">
            @yield('content')
        </div>
    </div>

</body>
</html>