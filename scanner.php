<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Escáner de Credencial - Zu</title>
    <script src="https://cdn.jsdelivr.net/npm/html5-qrcode/html5-qrcode.min.js"></script>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; background: #f0f2f5; }
        #reader { 
            width: 100%; max-width: 400px; margin: 20px auto; 
            border: 2px solid #ddd; border-radius: 10px; overflow: hidden;
            background: #000; /* Fondo negro para saber que el div existe */
            min-height: 250px;
        }
        #resultado { font-weight: bold; padding: 20px; color: #333; }
    </style>
</head>
<body>

    <h2>Escaneo de Credencial</h2>
    <div id="reader"></div>
    <div id="resultado">Esperando cámara...</div>

    <script>
        function inicializarEscaner() {
            // Verificamos si la librería cargó
            if (typeof Html5QrcodeScanner === 'undefined') {
                document.getElementById('resultado').innerHTML = "Error: No se pudo cargar la librería. Revisa tu internet.";
                return;
            }

            const scanner = new Html5QrcodeScanner("reader", { 
                fps: 10, 
                qrbox: {width: 250, height: 150} 
            });

            scanner.render((text) => {
                document.getElementById("resultado").innerHTML = "¡Detectado!: " + text;
                // scanner.clear(); // Opcional: detener al detectar
            }, (err) => {
                // Errores silenciosos de escaneo
            });
        }

        // Ejecutar cuando el DOM esté listo
        document.addEventListener("DOMContentLoaded", inicializarEscaner);
    </script>
</body>
</html>

