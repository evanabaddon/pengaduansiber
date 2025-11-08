<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editor Surat</title>
    <style>
        html, body, #placeholder {
            height: 100vh;
            margin: 0;
            padding: 0;
        }
    </style>
</head>
<body>
    <div id="placeholder"></div>

    <script src="https://office.kiwkiw.biz.id/web-apps/apps/api/documents/api.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            new DocsAPI.DocEditor("placeholder", {
                ...@json($config),
                token: "{{ $token }}"
            });
        });
    </script>
</body>
</html>
