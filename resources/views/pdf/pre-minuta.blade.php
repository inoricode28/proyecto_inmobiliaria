<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Pre-Minuta de Venta</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            line-height: 1.6;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .section {
            margin-bottom: 20px;
        }
        .label {
            font-weight: bold;
            display: inline-block;
            width: 150px;
        }
        .value {
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">PRE-MINUTA DE VENTA</div>
    </div>
    
    <div class="section">
        <div><span class="label">Cliente:</span> <span class="value">{{ $separacion->proforma->nombres }} {{ $separacion->proforma->apellidos }}</span></div>
        <div><span class="label">Documento:</span> <span class="value">{{ $separacion->proforma->tipoDocumento->nombre }} {{ $separacion->proforma->numero_documento }}</span></div>
        <div><span class="label">Proyecto:</span> <span class="value">{{ $separacion->proforma->proyecto->nombre }}</span></div>
        <div><span class="label">Departamento:</span> <span class="value">{{ $separacion->proforma->departamento->numero }}</span></div>
        <div><span class="label">Código Separación:</span> <span class="value">{{ $separacion->codigo }}</span></div>
        <div><span class="label">Fecha:</span> <span class="value">{{ now()->format('d/m/Y') }}</span></div>
    </div>
    
    <div class="section">
        <h3>Detalles del Inmueble</h3>
        <div><span class="label">Área:</span> <span class="value">{{ $separacion->proforma->departamento->area_total }} m²</span></div>
        <div><span class="label">Precio:</span> <span class="value">S/ {{ number_format($separacion->proforma->departamento->precio, 2) }}</span></div>
    </div>
    
    <div class="section">
        <p>Este documento constituye una pre-minuta para la venta del inmueble descrito anteriormente.</p>
    </div>
</body>
</html>
## 1. Crear la plantilla PDF

Primero, crea el directorio y archivo para la plantilla:
```php
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Pre-Minuta de Venta</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            line-height: 1.6;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .section {
            margin-bottom: 20px;
        }
        .label {
            font-weight: bold;
            display: inline-block;
            width: 150px;
        }
        .value {
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">PRE-MINUTA DE VENTA</div>
    </div>
    
    <div class="section">
        <div><span class="label">Cliente:</span> <span class="value">{{ $separacion->proforma->nombres }} {{ $separacion->proforma->apellidos }}</span></div>
        <div><span class="label">Documento:</span> <span class="value">{{ $separacion->proforma->tipoDocumento->nombre }} {{ $separacion->proforma->numero_documento }}</span></div>
        <div><span class="label">Proyecto:</span> <span class="value">{{ $separacion->proforma->proyecto->nombre }}</span></div>
        <div><span class="label">Departamento:</span> <span class="value">{{ $separacion->proforma->departamento->numero }}</span></div>
        <div><span class="label">Código Separación:</span> <span class="value">{{ $separacion->codigo }}</span></div>
        <div><span class="label">Fecha:</span> <span class="value">{{ now()->format('d/m/Y') }}</span></div>
    </div>
    
    <div class="section">
        <h3>Detalles del Inmueble</h3>
        <div><span class="label">Área:</span> <span class="value">{{ $separacion->proforma->departamento->area_total }} m²</span></div>
        <div><span class="label">Precio:</span> <span class="value">S/ {{ number_format($separacion->proforma->departamento->precio, 2) }}</span></div>
    </div>
    
    <div class="section">
        <p>Este documento constituye una pre-minuta para la venta del inmueble descrito anteriormente.</p>
    </div>
</body>
</html>