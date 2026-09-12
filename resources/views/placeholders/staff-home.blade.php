<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Halaman Role Sementara</title>
    <style>
        body{font-family:Arial,sans-serif;background:#f6f7fb;margin:0;padding:40px;color:#1f2937}
        .box{max-width:760px;margin:0 auto;background:#fff;border:1px solid #e5e7eb;border-radius:14px;padding:32px;box-shadow:0 10px 30px rgba(0,0,0,.05)}
        h1{margin-top:0}
        .muted{color:#6b7280}
        .badge{display:inline-block;padding:6px 10px;border-radius:999px;background:#eef2ff;color:#3730a3;font-size:14px}
        form{margin-top:24px}
        button{background:#111827;color:#fff;border:0;border-radius:10px;padding:10px 16px;cursor:pointer}
    </style>
</head>
<body>
    <div class="box">
        <span class="badge">Placeholder Role</span>
        <h1>Akses role non-superadmin sedang disiapkan</h1>
        <p class="muted">
            Anda sudah berhasil login, tetapi halaman khusus untuk role Anda belum diaktifkan.
            Untuk sementara, akses ke dashboard superadmin tidak diberikan.
        </p>
        <p class="muted">
            Silakan hubungi administrator bila Anda membutuhkan akses modul tertentu.
        </p>

        <form method="POST" action="{{ url('/logout') }}">
            @csrf
            <button type="submit">Logout</button>
        </form>
    </div>
</body>
</html>
