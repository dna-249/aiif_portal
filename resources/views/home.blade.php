<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <!-- Styles / Scripts -->
    
            @vite(['resources/css/app.css', 'resources/js/app.js'])
       
    </head><body>
       
  
<div class="text-5xl font-extrabold ...">
  <span class="bg-clip-text text-transparent bg-gradient-to-r from-green-400 to-blue-500">
    Hello world
  </span>
  <button class="bg-blue-500 md:bg-green-500 ...">Button</button>
  
</div>
<div class="bg-blue-500 bg-opacity-75 md:bg-opacity-50">
 hello world
</div>

</body>
</html>