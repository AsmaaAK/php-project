
<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <title><?= htmlspecialchars($title) ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    body{font-family:system-ui, sans-serif; margin:20px; background:#f7f7f8}
    header, footer{margin-bottom:20px}
    .container{max-width:900px; margin:0 auto; background:#fff; padding:20px; border-radius:12px; box-shadow:0 2px 10px rgba(0,0,0,.06)}
    .btn{padding:8px 14px; border-radius:8px; border:1px solid #ddd; background:#f1f5f9; cursor:pointer}
    .btn-primary{background:#e5efff; border-color:#bcd0ff}
    .table{width:100%; border-collapse:collapse}
    .table th,.table td{border-bottom:1px solid #eee; padding:10px}
    .error{background:#ffe5e5; padding:10px; border:1px solid #ffcaca; border-radius:8px; margin-bottom:10px}
    form{display:block; margin:0}
  </style>
</head>
<body>
  <div class="container">
    <header>
      <!-- <h1 style="margin:0">Capstone4 MVC</h1> -->
      <nav style="display:flex; gap:10px; margin-top:10px">
        <a href="/volunteer-managment/public/users" class="btn">المستخدمون</a>
        <form action="/volunteer-managment/public/auth/logout" method="post"  style="margin-inline-start:auto">
          <button class="btn">خروج</button>
        </form>
      </nav>
    </header>

    <main>
      <?= $content ?? '' ?>
    </main>

    <!-- <footer style="margin-top:20px; color:#666">©></footer> -->
  </div>
</body>
</html>
