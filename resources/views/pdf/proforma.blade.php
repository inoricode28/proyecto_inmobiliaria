<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proforma {{ $proforma->codigo_formateado }}</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        font-size: 12px;
        margin: 0;
        padding: 20px;
        color: #333;
    }

    .header {
        text-align: left;
        margin-bottom: 20px;
    }

    .logo {
        font-size: 24px;
        font-weight: bold;
        color: #333;
        margin-bottom: 5px;
    }

    .subtitle {
        font-size: 12px;
        color: #666;
        margin-bottom: 20px;
    }

    .proforma-info {
        display: flex;
        justify-content: space-between;
        margin-bottom: 20px;
    }

    .proforma-number {
        font-weight: bold;
        font-size: 14px;
    }

    .fecha {
        font-size: 12px;
    }

    .section-header {
        background-color: #666;
        color: white;
        padding: 8px;
        text-align: center;
        font-weight: bold;
        margin-top: 15px;
        margin-bottom: 0;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 15px;
    }

    .data-table td {
        border: 1px solid #ccc;
        padding: 6px;
        font-size: 11px;
    }

    .data-table .label {
        background-color: #f5f5f5;
        font-weight: bold;
        width: 25%;
    }

    .cotizacion-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 15px;
    }

    .cotizacion-table th,
    .cotizacion-table td {
        border: 1px solid #ccc;
        padding: 8px;
        text-align: center;
        font-size: 11px;
    }

    .cotizacion-table th {
        background-color: #666;
        color: white;
        font-weight: bold;
    }

    .precio-section {
        margin: 15px 0;
    }

    .precio-row {
        display: flex;
        justify-content: space-between;
        margin: 5px 0;
        font-size: 11px;
    }

    .precio-label {
        width: 40%;
    }

    .precio-porcentaje {
        width: 15%;
        text-align: center;
    }

    .precio-valor {
        width: 25%;
        text-align: right;
    }

    .proceso-compra {
        margin: 20px 0;
    }

    .proceso-list {
        font-size: 11px;
        line-height: 1.4;
    }

    .proceso-list ol {
        margin: 0;
        padding-left: 20px;
    }

    .banco-info {
        margin: 15px 0;
        font-size: 11px;
    }

    .contacto-section {
        margin: 15px 0;
    }

    .contacto-table {
        width: 100%;
        border-collapse: collapse;
    }

    .contacto-table td {
        border: 1px solid #ccc;
        padding: 6px;
        font-size: 11px;
    }

    .contacto-table .label {
        background-color: #666;
        color: white;
        font-weight: bold;
        width: 20%;
    }

    .nota-box {
        border: 1px solid #ccc;
        padding: 10px;
        margin: 15px 0;
        font-size: 10px;
    }

    .footer {
        position: fixed;
        bottom: 20px;
        left: 20px;
        right: 20px;
        text-align: center;
        font-size: 10px;
        background-color: #666;
        color: white;
        padding: 8px;
    }

    .text-right {
        text-align: right;
    }

    .font-bold {
        font-weight: bold;
    }
    </style>
</head>

<body>
    <!-- Header -->
    <div class="header">
        <div class="logo">INIZIA</div>
        <div class="subtitle">inmobiliaria</div>

        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div class="proforma-number">PROFORMA Nro. {{ $proforma->codigo_formateado }}</div>
            <div class="fecha">FECHA {{ now()->format('d \d\e F \d\e\l Y') }}</div>
        </div>
    </div>

    <!-- Datos del Cliente -->
    <div class="section-header">DATOS DEL CLIENTE</div>
    <table class="data-table">
        <tr>
            <td class="label">Nombres y Apellidos:</td>
            <td>{{ $proforma->nombres }} {{ $proforma->ape_paterno }} {{ $proforma->ape_materno }}</td>
            <td class="label">Nro. Documento:</td>
            <td>{{ $proforma->numero_documento }}</td>
        </tr>
        <tr>
            <td class="label">Teléfono:</td>
            <td>{{ $proforma->telefono ?? '' }}</td>
            <td class="label">Dirección:</td>
            <td>{{ $proforma->direccion ?? '' }}</td>
        </tr>
        <tr>
            <td class="label">Email:</td>
            <td>{{ $proforma->correo ?? '' }}</td>
            <td class="label">Celular:</td>
            <td>{{ $proforma->celular }}</td>
        </tr>
    </table>

    <!-- Cotización -->
    <div class="section-header">COTIZACIÓN</div>
    <table class="cotizacion-table">
        <thead>
            <tr>
                <th>INMUEBLES</th>
                <th>PRECIO LISTA</th>
                <th>DESCUENTO</th>
                <th>PRECIO FINAL</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $proforma->proyecto->nombre ?? 'N/A' }}</td>
                <td>S/. {{ number_format($proforma->departamento->Precio_lista ?? 0, 2) }}</td>
                <td>S/.
                    {{ number_format(($proforma->descuento ?? 0) * ($proforma->departamento->Precio_lista ?? 0) / 100, 2) }}
                </td>
                <td>S/.
                    {{ number_format(($proforma->departamento->Precio_lista ?? 0) - (($proforma->descuento ?? 0) * ($proforma->departamento->Precio_lista ?? 0) / 100), 2) }}
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Desglose de precios -->
    <div class="precio-section">
        <div class="precio-row">
            <div class="precio-label">Separación</div>
            <div class="precio-porcentaje">:</div>
            <div class="precio-valor">S/. {{ number_format($proforma->monto_separacion ?? 0, 2) }}</div>
        </div>
        <div class="precio-row">
            <div class="precio-label">Cuota Inicial</div>
            <div class="precio-porcentaje">10%</div>
            <div class="precio-valor">S/. {{ number_format($proforma->monto_cuota_inicial ?? 0, 2) }}</div>
        </div>
        <div class="precio-row">
            <div class="precio-label">Saldo Financiar</div>
            <div class="precio-porcentaje">90%</div>
            <div class="precio-valor">S/.
                {{ number_format((($proforma->departamento->Precio_lista ?? 0) - (($proforma->descuento ?? 0) * ($proforma->departamento->Precio_lista ?? 0) / 100)) * 0.9, 2) }}
            </div>
        </div>
        <div class="precio-row font-bold">
            <div class="precio-label">Total</div>
            <div class="precio-porcentaje"></div>
            <div class="precio-valor">S/.
                {{ number_format(($proforma->departamento->Precio_lista ?? 0) - (($proforma->descuento ?? 0) * ($proforma->departamento->Precio_lista ?? 0) / 100), 2) }}
            </div>
        </div>
    </div>

    <!-- Proceso de Compra -->
    <div class="section-header">PROCESO DE COMPRA</div>
    <div class="proceso-compra">
        <div class="proceso-list">
            <div style="display: flex;">
                <div style="width: 50%;">
                    <ol>
                        <li>Cuota de Separación</li>
                        <li>Definición de forma de compra del Departamento: Crédito Hipotecario</li>
                        <li>Pago de la cuota inicial.</li>
                    </ol>
                </div>
                <div style="width: 50%;">
                    <ol start="4">
                        <li>Firma de minuta compra venta.</li>
                        <li>Desembolso de saldo a financiar.</li>
                        <li>Entrega del inmueble.</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Información Bancaria -->
    <div class="banco-info">
        <strong>BANCO</strong>
        <div style="margin-top: 10px;">
            <strong>CUENTA</strong> Cta Cte BCP 193-71139790-0-71 / CCI: 00219300711397907112
        </div>
    </div>

    <!-- Contacto -->
    <div class="section-header">CONTACTO</div>
    <table class="contacto-table">
        <tr>
            <td class="label">Vendedor:</td>
            <td>Francesca Barbaran Zumba</td>
            <td class="label">Celular:</td>
            <td>962213654</td>
        </tr>
        <tr>
            <td class="label">Email:</td>
            <td>ventas1@inizia.pe</td>
            <td></td>
            <td></td>
        </tr>
    </table>

    <!-- Nota -->
    <div class="nota-box">
        <strong>Nota:</strong>
        <ul style="margin: 5px 0; padding-left: 20px;">
            <li>Los precios están sujetos a cambios</li>
            <li>Validez de la proforma 7 días.</li>
            <li>La separación forma parte de la cuota inicial.</li>
        </ul>
    </div>

    <!-- Footer -->
    <div class="footer">
        CASTILLA 221, MAGDALENA DEL MAR
    </div>
</body>

</html>
