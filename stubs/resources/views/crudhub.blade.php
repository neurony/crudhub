<!DOCTYPE html>
<html class="h-full" lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />

    @routes

    @vite(['resources/js/crudhub/app.js', 'resources/css/crudhub/app.scss'])
</head>
<body class="h-full bg-gray-50">
@inertia
</body>
</html>
