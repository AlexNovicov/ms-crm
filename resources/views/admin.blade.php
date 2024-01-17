<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="api-path" content="{{ \ProfilanceGroup\BackendSdk\Helpers\Site::getDomainName(true) }}">
    <title>Админ панель CRM микросервиса</title>
    <link href="{{ asset('/assets/css/admin.css', config('app_main.secure_url')) }}" rel="stylesheet">
</head>
<body>
<noscript>You need to enable JavaScript to run this app.</noscript>
<div id="root"></div>
<script src="{{ asset('/assets/js/admin.js', config('app_main.secure_url')) }}"></script>
</body>
</html>
