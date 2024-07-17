<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            width: 100%;
            height: 100%;
            box-sizing: border-box;
            position: relative;
        }
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 5px;
            height: 60px;
        }
        .header-title {
            background-color: #4CAF50;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            margin: 0;
        }
        h4 {
            margin: 0;
            display: inline;
        }
        img {
            height: 80px;
            align-self: center;
            margin-top: -10px;
        }
        .watermark {
            position: absolute;
            right: 10px;
            width: 150px;
            pointer-events: none;
        }
        .info {
            margin-top: 10px;
            padding: 5px;
            border-radius: 5px;
            font-size: 12px; /* Tamaño de letra más pequeño */
        }
        .info p {
            margin: 5px 0; /* Espacio entre líneas */
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        td {
            padding: 0px;
            margin: 0 4px;
            text-align: left;
            border: none; /* Sin borde */
        }
        .title-cell {
            font-size: 14px; /* Tamaño de letra más pequeño */
            width: 200px; /* Ajusta el ancho aquí */
        }
        .content-cell {
            font-size: 14px;
        }
        .image-container {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
            padding-top: 40px;
        }
        footer {
            position: absolute;
            bottom: 20px; /* Espacio desde el fondo */
            left: 0;
            right: 0;
            text-align: center;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <img src="{{ asset('images/logo.png') }}" class="watermark">
    <header>
        <h4 class="header-title">Ingeniería de Mantenimiento y Confiabilidad</h4>
    </header>

    <div class="info">
        <table>
            <tr>
                <td class="title-cell"><strong>PLANTA:</strong></td>
                <td class="content-cell">{{ $reportData->equipment->company->name }}</td>
            </tr>
            <tr>
                <td class="title-cell"><strong>EQUIPO:</strong></td>
                <td class="content-cell">{{ $reportData->equipment->name }}</td>
            </tr>
            <tr>
                <td class="title-cell">CONDICIÓN:</td>
                <td class="content-cell">{{ $reportData->condition }}</td>
            </tr>
            <tr>
                <td class="title-cell">MECANISMO FALLA:</td>
                <td class="content-cell">{{ $reportData->failure_mechanism }}</td>
            </tr>
            <tr>
                <td class="title-cell">COMPONENTE EN FALLA:</td>
                <td class="content-cell">{{ $reportData->component_failure }}</td>
            </tr>
            <tr>
                <td class="title-cell">PRIORIDAD ATENCIÓN:</td>
                <td class="content-cell">{{ $reportData->priority }}</td>
            </tr>
            <tr>
                <td class="title-cell">FECHA MEDICIÓN:</td>
                <td class="content-cell">{{ $reportData->measurement_date }}</td>
            </tr>
        </table>

    </div>
    <div class="image-container">
        <img src="{{ asset('storage/' . $reportData->image_1) }}" alt="Imagen 1" style="width: 48%; height: 200px;">
        <img src="{{ asset('storage/' . $reportData->image_1) }}" alt="Imagen 2" style="width: 48%; height: 200px;">
    </div>
    <div class="info">
        <h4>RECOMENDACIÓN GENERAL:</h4>
        <ul>
            @foreach($reportData->recommendations as $recommendation)
                <li>{{ $recommendation }}</li>
            @endforeach
        </ul>
    </div>
    <footer>
        Carrera 5 # 14-36, Barrio San Nicolás, Fundación, Magdalena.<br>
        Móvil: 316 4048830. E-mail: alvaro.sanchezm1@gmail.com
    </footer>
</body>
</html>
