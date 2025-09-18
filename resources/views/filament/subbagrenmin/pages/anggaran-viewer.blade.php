<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Anggaran</title>
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
            const viewerConfig = {
                ...@json($config),
                editorConfig: {
                    ...@json($config['editorConfig']),
                    mode: "view", // ubah mode ke view
                    forcesave: false,
                    feedback: false
                },
                token: "{{ $token }}"
            };
            new DocsAPI.DocEditor("placeholder", viewerConfig);
        });
    </script>
</body>
</html>
