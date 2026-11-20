<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>@yield('title', 'PS Finder Samarinda')</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,400;12..96,600;12..96,800&display=swap">
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
@stack('head')
</head>
<body>
<header>
  <div><b>PS Finder</b> <span>Samarinda</span></div>
  <nav>
    <a href="{{ route('home') }}">Peringkat</a>
    <a href="{{ route('survei') }}">Isi survei</a>
    <button id="th" title="Ganti tema" aria-label="Ganti tema">☾</button>
  </nav>
</header>
@yield('content')
<script>
(()=>{const r=document.documentElement;
r.dataset.theme=matchMedia('(prefers-color-scheme: dark)').matches?'dark':'light';
document.getElementById('th').onclick=()=>{r.dataset.theme=r.dataset.theme=='dark'?'light':'dark';window.dispatchEvent(new Event('themechange'))}})();
</script>
@stack('scripts')
</body>
</html>
