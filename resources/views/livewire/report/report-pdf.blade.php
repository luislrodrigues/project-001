<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            background-color: #f7f7f7;
            color: #333;
            height: 100%;
            width: 100%;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #4CAF50;
            color: white;
            padding: 20px;
        }

        .header-title {
            font-size: 24px;
            font-weight: bold;
            margin: 0;
        }

        h4 {
            font-size: 18px;
            margin: 0;
        }

        .watermark {
            position: absolute;
            top: 20px;
            right: 20px;
            width: 150px;
            opacity: 0.1;
        }

        .info {
            background-color: white;
            padding: 20px;
            margin-top: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .info p {
            font-size: 14px;
            margin: 10px 0;
            line-height: 1.6;
        }

        .info table {
            width: 100%;
            border-spacing: 0;
            margin-top: 10px;
        }

        .title-cell {
            font-weight: bold;
            width: 200px;
            padding: 10px;
            background-color: #f2f2f2;
        }

        .content-cell {
            padding: 10px;
            text-align: left;
        }

        .image-container {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .image-container img {
            width: 48%;
            height: 200px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        h3 {
            font-size: 18px;
            font-weight: bold;
            margin-top: 20px;
        }

        ul {
            margin: 10px 0;
            padding-left: 20px;
        }

        footer {
            text-align: center;
            font-size: 14px;
            margin-top: 30px;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
        }

        footer a {
            color: #ffffff;
            text-decoration: none;
        }

        footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <header>
        <div>
            <h4 class="header-title">Ingeniería de Mantenimiento y Confiabilidad</h4>
        </div>
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
        <img src="{{ asset('storage/' . $reportData->image_1) }}" alt="Imagen 1">
        <img src="{{ asset('storage/' . $reportData->image_2) }}" alt="Imagen 2">
    </div>

    @foreach($components as $component)
        <div class="info">
            <h3>{{$component->componentType->name}}</h3>
            <table>
                <tr>
                    <td class="title-cell"><strong>Marca:</strong></td>
                    <td class="content-cell">{{ $component->brand->name }}</td>
                </tr>
                @foreach($component->fields as $field)
                    <tr>
                        <td class="title-cell"><strong>{{ $field->name }}:</strong></td>
                        <td class="content-cell">{{ $field->value->value }}</td>
                    </tr>
                @endforeach
            </table>

            @if(isset($reportData->diagnosis[$component->id]) && !empty($reportData->diagnosis[$component->id]))
                <p><strong>Diagnóstico:</strong> {{ $reportData->diagnosis[$component->id] }}</p>
            @else
                <p><strong>Diagnóstico:</strong></p>
            @endif

            <p><strong>Recomendaciones:</strong></p>
            <ul>
                @if(isset($reportData->recommendations[$component->id]) && is_array($reportData->recommendations[$component->id]) && !empty($reportData->recommendations[$component->id]))
                    @foreach ($reportData->recommendations[$component->id] as $recommendation)
                        <li>{{ $recommendation }}</li>
                    @endforeach
                @else
                    <p></p>
                @endif
            
            
            </ul>
        </div>
    @endforeach

    <footer>
        Carrera 5 # 14-36, Barrio San Nicolás, Fundación, Magdalena.<br>
        Móvil: 316 4048830. E-mail: <a href="mailto:alvaro.sanchezm1@gmail.com">alvaro.sanchezm1@gmail.com</a>
    </footer>
</body>
</html>
