<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Membuka Dokumen...</title>
    <meta http-equiv="refresh" content="2;url={{ route('onlyoffice.edit', $surat->id) }}">
    <style>
        body {
            display: flex;
            height: 100vh;
            justify-content: center;
            align-items: center;
            font-family: sans-serif;
            background: #f9fafb;
        }
        .spinner {
            border: 4px solid #ddd;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin-right: 10px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div style="display:flex; align-items:center; gap:10px;">
        <div class="spinner"></div>
        <h3>Menyiapkan editor dokumen...</h3>
    </div>
    <script>
        // Jaga-jaga, redirect manual kalau meta refresh gagal
        setTimeout(() => {
            window.location.href = "{{ route('onlyoffice.edit', $surat->id) }}";
        }, 2500);
    </script>
</body>
</html>
