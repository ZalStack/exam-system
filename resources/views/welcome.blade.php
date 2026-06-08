<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam System</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-md text-center max-w-md">
            <h1 class="text-3xl font-bold mb-4" style="font-family: 'Poppins', sans-serif;">Welcome to Exam System</h1>
            <p class="text-gray-600 mb-6" style="font-family: 'Poppins', sans-serif;">A comprehensive online examination platform</p>
            <div class="space-y-3">
                <a href="{{ route('login') }}" class="block w-full bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition" style="font-family: 'Poppins', sans-serif;">
                    Login
                </a>
                <a href="{{ route('register') }}" class="block w-full bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition" style="font-family: 'Poppins', sans-serif;">
                    Register
                </a>
            </div>
        </div>
    </div>
</body>
</html>
