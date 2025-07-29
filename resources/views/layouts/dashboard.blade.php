<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>@yield('title', 'Nasanuera Academy')</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css"/>
</head>
<body>
  {{-- Navbar dengan tema hijau dan branding --}}
  <nav class="navbar navbar-expand-lg navbar-dark bg-success px-4">
    <a href="#" class="navbar-brand fw-bold">Nasanuera Academy</a>
    <a href="/" class="btn btn-outline-light btn-sm ms-auto">Dashboard</a>
  </nav>

  <div class="container py-4">
    @yield('content')
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

  @stack('scripts')
</body>
</html>
