{{-- Carnet QR como PDF tamaño carta: la imagen del carnet (1275×1650, la
     misma proporción que la hoja) ocupa la página completa, sin márgenes. --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Carnet QR</title>
    <style>
        @page { margin: 0; }
        body { margin: 0; }
        img { width: 612pt; height: 792pt; }
    </style>
</head>
<body>
    <img src="{{ $imagen }}" alt="Carnet con código QR">
</body>
</html>
