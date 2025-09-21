<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Seguimientos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #333;
            margin-bottom: 10px;
        }
        .date {
            color: #666;
            font-size: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte de Seguimientos</h1>
        <div class="date">Generado el: {{ date('d/m/Y H:i:s') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombres</th>
                <th>Teléfono</th>
                <th>N° Documento</th>
                <th>Proyecto</th>
                <th>Fuente de Referencia</th>
                <th>Fecha de Registro</th>
                <th>Fecha Último Contacto</th>
                <th>Fecha de Tarea</th>
                <th>Responsable</th>
            </tr>
        </thead>
        <tbody>
            @foreach($seguimientos as $seguimiento)
            <tr>
                <td>{{ $seguimiento['id'] }}</td>
                <td>{{ $seguimiento['nombres'] }}</td>
                <td>{{ $seguimiento['telefono'] }}</td>
                <td>{{ $seguimiento['documento'] }}</td>
                <td>{{ $seguimiento['proyecto'] }}</td>
                <td>{{ $seguimiento['fuente_referencia'] }}</td>
                <td>{{ $seguimiento['fecha_registro'] }}</td>
                <td>{{ $seguimiento['fecha_ultimo_contacto'] }}</td>
                <td>{{ $seguimiento['fecha_tarea'] }}</td>
                <td>{{ $seguimiento['responsable'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Total de registros: {{ count($seguimientos) }}</p>
    </div>
</body>
</html>