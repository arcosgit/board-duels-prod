<html>
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
    @vite('resources/js/app.js')
    @vite('resources/css/app.css')
    @inertiaHead
    @routes
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <meta name="description" content="крестики нолики, играть онлайн в крестики нолики, играть в крестики нолики с друзьями">
    <link rel="canonical" href="https://board-duels.ru/">
    <meta property="og:title" content="board-duels">
    <meta property="og:description" content="крестики нолики" >
    <meta property="og:image" content="{{ asset('icon.svg') }}" >
    <meta property="og:url" content="https://board-duels.ru/" >
    <meta property="og:type" content="website">
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="board-duels" />
    <meta name="twitter:description" content="крестики нолики" />
    <meta name="twitter:image" content="{{ asset('icon.svg') }}" />
    <meta name="keywords" content="крестики нолики">
    <script type="application/ld+json">
      {
        "@context": "https://schema.org",
        "@type": "WebSite",
        "name": "board-duels",
        "url": "https://board-duels.ru/",
        "description": "крестики нолики"
      }
      </script>
    <link rel="icon" href="{{ asset('icon.svg') }}" type="image/svg+xml">
    <title>board-duels</title>
  </head>
  <body>
    @inertia
  </body>
</html>
