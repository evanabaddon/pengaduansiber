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
            var editor = new DocsAPI.DocEditor("placeholder", {
                ...@json($config),
                token: "{{ $token }}"
            });

            // Event listener untuk mendeteksi ketika dokumen disimpan
            window.addEventListener('message', function(event) {
                // Handle events dari OnlyOffice
                if (event.data && event.data.event) {
                    console.log('OnlyOffice event:', event.data.event);
                    
                    // Event ketika dokumen siap untuk disimpan
                    if (event.data.event === 'documentReady') {
                        console.log('Document ready for saving');
                    }
                    
                    // Event ketika dokumen berhasil disimpan
                    if (event.data.event === 'documentSaved') {
                        console.log('Document saved successfully');
                        closeEditor();
                    }
                    
                    // Event ketika user meninggalkan editor
                    if (event.data.event === 'requestClose') {
                        console.log('Request to close editor');
                        closeEditor();
                    }
                }
            });

            // Function untuk menutup editor
            function closeEditor() {
                console.log('Closing editor...');
                
                // Coba tutup tab/window
                setTimeout(function() {
                    // Method 1: Tutup window jika bisa
                    if (window.history.length > 1) {
                        window.history.back();
                    } else {
                        // Method 2: Redirect ke halaman utama
                        window.location.href = '{{ url("/admin") }}';
                    }
                    
                    // Method 3: Force close setelah 2 detik
                    setTimeout(function() {
                        window.close();
                    }, 2000);
                    
                }, 1000);
            }

            // Fallback: Auto close setelah 30 detik (jika tidak ada interaksi)
            setTimeout(function() {
                console.log('Auto-close fallback triggered');
                // closeEditor();
            }, 30000);

        });
    </script>
</body>
</html>