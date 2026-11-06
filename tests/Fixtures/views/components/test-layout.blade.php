@props(['title' => null, 'description' => null])
<!DOCTYPE html>
<html>
<head>
    <title>LAYOUT: {{ $title }}</title>
    <meta name="description" content="{{ $description ?? 'NO-DESCRIPTION' }}">
</head>
<body>
    {{ $slot }}
</body>
</html>
