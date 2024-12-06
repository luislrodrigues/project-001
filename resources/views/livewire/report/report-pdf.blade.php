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
            background-color: #ffffff;
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
            font-size: 18px;
            font-weight: bold;
            margin: 0;
        }

        h4 {
            font-size: 18px;
            margin: 0;
        }

        p{
            font-family: 'Arial', sans-serif;
            font-size: 15px;
        }
        strong{
            font-family: 'Arial', sans-serif;
            font-size: 15px;
        }

        .info {
            background-color: white;
            padding: 0 20px;
            margin-top: 5px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .infoImg {
            background-color: white;
            padding: 20px 20px 0px 20px;
            margin-top: 10px;
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
            /* padding-left: 20px; */
        }
        li{
            font-family: 'Arial', sans-serif;
            font-size: 15px;
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
        <ul  style="list-style: none;margin:0;padding:0;">
            <li ><strong>Planta:</strong> {{ $reportData->equipment->company->name }}</li>
            <li><strong>Equipo:</strong> {{ $reportData->equipment->name }}</li>
            <li><strong>Tipo equipo:</strong> {{ $reportData->equipment->equipmentType->name }}</li>
            <li><strong>Condición:</strong> {{ $reportData->condition }}</li>
            <li><strong>Mecanismo falla:</strong> {{ $reportData->failure_mechanism }}</li>
            <li><strong>Componente en falla:</strong> {{ $reportData->component_failure }}</li>
            <li><strong>Prioridad atención:</strong> {{ $reportData->priority }}</li>
            <li><strong>fecha medición:</strong> {{ $reportData->measurement_date }}</li>
            <li><strong>Proceso:</strong> {{ $reportData->equipment->process->name }}</li>
            <li><strong>Ubicación:</strong> {{ $reportData->equipment->ubication->name }}</li>
        </ul>
    </div>
    <div class="infoImg">
        <div class="image-container">
            <img src="{{ asset('storage/' . $reportData->image_1) }}" alt="Imagen 1">
            <img src="{{ asset('storage/' . $reportData->image_2) }}" alt="Imagen 2">
        </div>
    </div>

    @foreach($components as $component)
        <div class="info">
            <h3>{{$component->componentType->name}}</h3>

            <ul  style="list-style: none;margin:0;padding:0;">
                <li><strong>Marca:</strong> {{ $component->brand->name  }}</li>
                @foreach($component->fields as $field)
                    <li><strong>{{ $field->name }}:</strong> {{ $field->value->value }}</li>
                @endforeach
            </ul>

            @if(isset($reportData->diagnosis[$component->id]) && !empty($reportData->diagnosis[$component->id]))
                <p><strong>Diagnóstico:</strong><br> {{ $reportData->diagnosis[$component->id] }}</p>
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
