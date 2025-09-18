<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ditressiber Document Editor</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}"> <!-- kalau pakai Tailwind/Bootstrap -->
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
    </style>
</head>
<body class="h-screen w-screen">
    @yield('content')
</body>
</html>
