<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-md text-center max-w-md">
            <h1 class="text-3xl font-bold mb-4">Welcome to Exam System</h1>
            <p class="text-gray-600 mb-6">A comprehensive online examination platform</p>
            <div class="space-y-3">
                <a href="{{ route('login') }}" class="block w-full bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Login
                </a>
                <a href="{{ route('register') }}" class="block w-full bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                    Register
                </a>
            </div>
        </div>
    </div>
</body>
</html>
