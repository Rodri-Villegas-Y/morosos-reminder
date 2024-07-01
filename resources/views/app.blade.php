<!DOCTYPE html>
<html class="h-full">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">

    {{-- Inertia --}}
    <script src="https://polyfill.io/v3/polyfill.min.js?features=smoothscroll,NodeList.prototype.forEach,Promise,Object.values,Object.assign" defer></script>

    <script src="https://kit.fontawesome.com/cbe71f911d.js" crossorigin="anonymous"></script>

    @vite('resources/js/app.js')
    @inertiaHead
</head>
<body class="m-0 p-0">
    @inertia
</body>
</html>
