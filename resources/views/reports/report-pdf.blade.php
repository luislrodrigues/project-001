<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reporte de Mantenimiento Predictivo</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            margin: 0;
            padding: 0;
            color: #1a1a1a;
            font-size: 11pt;
            line-height: 1.5;
        }

        .report-content {
            margin: 20px 24px;
            padding: 20px 24px;
        }

        /* Header 2026: sin margen ni padding */
        .page-header-wrap {
            width: 100%;
            margin: 0;
            padding: 0;
        }

        .header-accent {
            height: 5px;
            background-color: #0ea5e9;
            margin: 0;
            padding: 0;
        }

        .header-main {
            background-color: #f8fafc;
            padding: 20px 24px;
            margin: 0;
        }

        .header-logo-cell {
            width: 100%;
            text-align: left;
            vertical-align: middle;
        }

        .header-logo-img {
            height: 48px;
            width: auto;
            max-width: 180px;
            display: inline-block;
            vertical-align: middle;
        }

        .body-wrap {
            margin-top: 0;
        }

        .block {
            margin-bottom: 28px;
        }

        .block:last-child {
            margin-bottom: 0;
        }

        .equipment-data {
            border-collapse: collapse;
            width: 100%;
            font-size: 11pt;
            margin-bottom: 28px;
            margin-left: 0;
            margin-right: 0;
        }

        .equipment-data td {
            padding: 10px 14px 10px 0;
            vertical-align: middle;
        }

        .equipment-data td.value-cell {
            padding: 10px 0 10px 14px;
        }

        .equipment-data .label-cell {
            font-weight: bold;
            width: 220px;
            color: #1a1a1a;
            font-size: 11pt;
        }

        .equipment-data .value-cell {
            color: #1a1a1a;
            font-size: 11pt;
        }

        .equipment-data .value-equipo {
            font-weight: bold;
        }

        .equipment-image-block {
            margin-bottom: 28px;
            text-align: center;
        }

        .equipment-image {
            max-width: 100%;
            height: auto;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }

        .condition-alarma {
            background: #fef3c7;
            color: #92400e;
            font-weight: bold;
            padding: 3px 8px;
            font-size: 11pt;
        }

        .section-title {
            font-size: 13pt;
            font-weight: bold;
            color: #1a1a1a;
            margin: 0 0 12px 0;
        }

        .section .spec-list {
            list-style: none;
            margin: 0 0 12px 0;
            padding: 0;
            font-size: 11pt;
        }

        .section .spec-list li {
            padding: 4px 0;
            line-height: 1.5;
            color: #1a1a1a;
        }

        .section p {
            margin: 0 0 12px 0;
            text-align: justify;
            font-size: 11pt;
            line-height: 1.5;
            color: #1a1a1a;
        }

        .section p:last-of-type {
            margin-bottom: 0;
        }

        .spectrum-section {
            margin: 16px 0 0;
            padding: 16px;
        }

        .spectrum-title {
            font-size: 12pt;
            font-weight: bold;
            text-align: center;
            margin: 0 0 4px 0;
            color: #1a1a1a;
        }

        .spectrum-subtitle {
            font-size: 9pt;
            color: #6b7280;
            text-align: center;
            margin: 0 0 12px 0;
        }

        .image-container {
            margin-top: 12px;
        }

        .image-container img {
            max-width: 100%;
            height: auto;
            display: block;
            margin: 0 auto 12px;
        }

        .image-container img:last-child {
            margin-bottom: 0;
        }

        .section .rec-label {
            font-weight: bold;
            margin: 12px 0 8px 0;
            font-size: 11pt;
            color: #1a1a1a;
        }

        .recommendations-list {
            margin: 0;
            padding-left: 20px;
            list-style-type: disc;
            font-size: 11pt;
        }

        .recommendations-list li {
            margin-bottom: 6px;
            text-align: justify;
            line-height: 1.5;
            color: #1a1a1a;
        }

        /* Recomendación general: título + subsecciones con viñeta de punto */
        .recommendation-general {
            margin-top: 28px;
            margin-bottom: 28px;
        }

        .recommendation-general h3 {
            font-size: 12pt;
            font-weight: bold;
            color: #1a1a1a;
            margin: 0 0 14px 0;
        }

        .recommendation-general .rec-sub {
            font-weight: bold;
            font-size: 11pt;
            color: #1a1a1a;
            margin: 14px 0 6px 0;
        }

        .recommendation-general .rec-sub:first-of-type {
            margin-top: 0;
        }

        .recommendation-general .rec-list {
            margin: 0 0 12px 0;
            padding-left: 24px;
            list-style-type: disc;
            font-size: 11pt;
        }

        .recommendation-general .rec-list li {
            margin-bottom: 6px;
            line-height: 1.5;
            color: #1a1a1a;
            text-align: justify;
        }

        /* Firmas */
        .signature-block {
            margin-top: 32px;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
        }

        .signature-table td {
            vertical-align: top;
            width: 50%;
            padding: 0 16px 0 0;
        }

        .signature-table td:last-child {
            padding: 0 0 0 16px;
        }

        .signature-label {
            font-size: 11pt;
            color: #1a1a1a;
            margin-bottom: 6px;
        }

        .signature-name {
            font-size: 11pt;
            font-weight: bold;
            color: #1a1a1a;
            margin: 0 0 2px 0;
        }

        .signature-title {
            font-size: 10pt;
            color: #4b5563;
            margin: 0;
        }

        .report-footer {
            text-align: center;
            font-size: 10pt;
            color: #4b5563;
            font-style: italic;
        }

        @page {
            header: page-header;
            footer: page-footer;
        }
    </style>
</head>

<body class="body-wrap">
    <htmlpageheader name="page-header">
        @php
            $logoPath = public_path('images/logo-synergy.png');
            $logoSrc = file_exists($logoPath)
                ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
                : '';
        @endphp
        <div class="page-header-wrap">
            <div class="header-accent"></div>
            <div class="header-main">
                <table cellpadding="0" cellspacing="0" border="0" style="width:100%;">
                    <tr>
                        <td class="header-logo-cell">
                            @if ($logoSrc)
                                <img src="{{ $logoSrc }}" alt="SYNERGY SMART" class="header-logo-img" />
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </htmlpageheader>

    <htmlpagefooter name="page-footer">
        <div class="report-footer">
            Ingeniería de Mantenimiento Predictivo y Confiabilidad
        </div>
    </htmlpagefooter>

    <div class="report-content">
        {{-- Datos del equipo --}}
        <table class="equipment-data">
            <tr>
                <td class="label-cell">PLANTA:</td>
                <td class="value-cell">{{ $reportData->equipment?->company?->name ?? '—' }}</td>
            </tr>
            <tr>
                <td class="label-cell">EQUIPO:</td>
                <td class="value-cell value-equipo">{{ $reportData->equipment?->name ?? '—' }}</td>
            </tr>
            <tr>
                <td class="label-cell">CONDICIÓN:</td>
                <td class="value-cell">
                    @php
                        $condicion = $reportData->condition?->name ?? '';
                        $esAlarma = stripos($condicion, 'alarma') !== false;
                    @endphp
                    @if ($esAlarma)
                        <span class="condition-alarma">{{ $condicion }}</span>
                    @else
                        {{ $condicion }}
                    @endif
                </td>
            </tr>
            <tr>
                <td class="label-cell">MECANISMO FALLA:</td>
                <td class="value-cell">{{ $reportData->mechanismFail->name ?? '—' }}</td>
            </tr>
            <tr>
                <td class="label-cell">COMPONENTE EN FALLA:</td>
                <td class="value-cell">{{ $reportData->componentFail->name ?? '—' }}</td>
            </tr>
            <tr>
                <td class="label-cell">PRIORIDAD ATENCIÓN:</td>
                <td class="value-cell">{{ $reportData->priority ?? '—' }}</td>
            </tr>
            <tr>
                <td class="label-cell">FECHA MEDICIÓN:</td>
                <td class="value-cell">{{ $reportData->measurement_date ?? '—' }}</td>
            </tr>
        </table>

        @if(!empty($equipmentImage))
        <div class="equipment-image-block">
            <img src="{{ $equipmentImage }}" alt="Equipo" class="equipment-image" />
        </div>
        @endif

        @foreach ($components as $component)
            <div class="section block">
                <h3 class="section-title">{{ $component->componentType->name }}</h3>
                <ul class="spec-list">
                    <li><strong>Marca:</strong> {{ $component->brand->name ?? '—' }}</li>
                    @foreach ($component->fields ?? [] as $field)
                        <li><strong>{{ $field->name }}:</strong> {{ $field->value->value ?? '—' }}</li>
                    @endforeach
                </ul>

                @php
                    $diagnosisText = '';
                    if (isset($diagnosis) && isset($diagnosis[$component->id])) {
                        $d = $diagnosis[$component->id];
                        $diagnosisText = is_array($d) ? $d['text'] ?? '' : $d;
                    }
                @endphp
                @if (!empty($diagnosisText))
                    <p class="diagnosis-text">{{ $diagnosisText }}</p>
                @endif

                @if (isset($componentImages) && isset($componentImages[$component->id]) && !empty($componentImages[$component->id]))
                    <div class="spectrum-section">
                        <p class="spectrum-title">Espectro</p>
                        <p class="spectrum-subtitle">{{ $component->componentType->name }},
                            {{ $reportData->measurement_date ?? '' }}</p>
                        <div class="image-container">
                            @foreach ($componentImages[$component->id] as $imageUrl)
                                @if (!empty($imageUrl))
                                    <img src="{{ $imageUrl }}" alt="Espectro" />
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

                <p class="rec-label">Recomendaciones:</p>
                <ul class="recommendations-list">
                    @if (isset($recommendations) &&
                            isset($recommendations[$component->id]) &&
                            is_array($recommendations[$component->id]) &&
                            !empty($recommendations[$component->id]))
                        @foreach ($recommendations[$component->id] as $recommendation)
                            <li>{{ $recommendation }}</li>
                        @endforeach
                    @endif
                </ul>
            </div>
        @endforeach

        @php
            $hasAnyRecommendations =
                isset($recommendations) && is_array($recommendations) && count(array_filter($recommendations)) > 0;
        @endphp
        @if ($hasAnyRecommendations)
            <div class="recommendation-general">
                <h3>RECOMENDACIÓN GENERAL:</h3>
                @foreach ($components as $component)
                    @if (isset($recommendations) &&
                            isset($recommendations[$component->id]) &&
                            is_array($recommendations[$component->id]) &&
                            !empty($recommendations[$component->id]))
                        <p class="rec-sub">{{ $component->componentType->name }}:</p>
                        <ul class="rec-list">
                            @foreach ($recommendations[$component->id] as $rec)
                                <li>{{ $rec }}</li>
                            @endforeach
                        </ul>
                    @endif
                @endforeach
            </div>
        @endif
    </div>
</body>

</html>
