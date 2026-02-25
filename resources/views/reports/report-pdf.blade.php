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
            font-size: 12pt;
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

        .component-section {
            /* Permitir que la sección se parta entre páginas para no dejar espacio en blanco */
        }

        .equipment-top-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 28px;
        }

        .equipment-top-table .equipment-data-cell {
            width: 55%;
            vertical-align: middle;
            padding: 0 20px 0 0;
        }

        .equipment-top-table .equipment-image-cell {
            width: 45%;
            vertical-align: middle;
            padding: 0 0 0 12px;
            text-align: right;
        }

        .equipment-data-div {
            font-size: 12pt;
            color: #1a1a1a;
        }

        .equipment-data-div .data-line {
            margin: 0 0 14px 0;
            line-height: 1.6;
        }

        .equipment-data-div .data-line:last-child {
            margin-bottom: 0;
        }

        .equipment-data-div .data-line .label {
            font-weight: bold;
        }

        .equipment-data-div .data-line .value-equipo {
            font-weight: bold;
        }

        .equipment-image-div {
            display: block;
            border: 1px solid #e5e7eb;
            padding: 8px;
            background: #fafafa;
        }

        .equipment-image {
            max-width: 100%;
            height: 220px;
            object-fit: contain;
        }

        .condition-alarma,
        .condition-value {
            color: #000;
            background: #fef3c7;
            font-weight: bold;
            padding: 3px 8px;
            font-size: 12pt;
        }

        .section-title {
            font-size: 14pt;
            font-weight: bold;
            color: #1a1a1a;
            margin: 0 0 10px 0;
        }

        .section-title-line {
            font-size: 12pt;
            color: #1a1a1a;
            margin: 0 0 8px 0;
            line-height: 1.5;
        }

        /* Especificidad mayor que .section p para que los valores queden en 8pt */
        .section p.component-spec-paragraph {
            font-size: 8pt;
            color: #1a1a1a;
            margin: 0 0 12px 0;
            line-height: 1.5;
            text-align: justify;
        }

        .section p.component-spec-paragraph strong {
            font-size: 9pt;
            font-weight: bold;
        }

        .section .spec-list {
            list-style: none;
            margin: 0 0 12px 0;
            padding: 0;
            font-size: 12pt;
        }

        .section .spec-list li {
            padding: 4px 0;
            line-height: 1.5;
            color: #1a1a1a;
        }

        .section p {
            margin: 0 0 12px 0;
            text-align: justify;
            font-size: 12pt;
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
            font-size: 13pt;
            font-weight: bold;
            text-align: center;
            margin: 0 0 4px 0;
            color: #1a1a1a;
        }

        .spectrum-subtitle {
            font-size: 12pt;
            color: #6b7280;
            text-align: center;
            margin: 0 0 12px 0;
        }

        .image-container {
            margin-top: 12px;
            width: 100%;
            border-collapse: collapse;
        }

        .image-container td {
            width: 50%;
            padding: 6px;
            vertical-align: top;
            text-align: center;
        }

        .image-container img {
            max-width: 100%;
            width: auto;
            height: auto;
            max-height: 100px;
            display: inline-block;
            margin-left: auto;
            margin-right: auto;
        }

        .section .diagnosis-label {
            font-weight: bold;
            margin: 12px 0 6px 0;
            font-size: 9pt;
            color: #1a1a1a;
        }

        .section .diagnosis-text {
            font-size: 8pt;
            white-space: pre-line;
            margin: 0 0 10px 0;
            text-align: justify;
        }

        .section .rec-label {
            font-weight: bold;
            margin: 12px 0 8px 0;
            font-size: 9pt;
            color: #1a1a1a;
        }

        .section .rec-list {
            font-size: 8pt;
        }

        .recommendations-list {
            margin: 0;
            padding-left: 20px;
            list-style-type: disc;
            font-size: 8pt;
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
            font-size: 9pt;
            font-weight: bold;
            color: #1a1a1a;
            margin: 0 0 14px 0;
        }

        .recommendation-general-text {
            margin: 0;
            font-size: 8pt;
            line-height: 1.5;
            color: #1a1a1a;
            text-align: justify;
        }

        .recommendation-general .rec-sub {
            font-weight: bold;
            font-size: 12pt;
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
            font-size: 8pt;
        }

        .recommendation-general .rec-list li {
            margin-bottom: 6px;
            line-height: 1.5;
            color: #1a1a1a;
            text-align: justify;
        }

        .elaboro-line {
            margin: 0;
            font-size: 9pt;
            color: #1a1a1a;
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
            font-size: 12pt;
            color: #1a1a1a;
            margin-bottom: 6px;
        }

        .signature-name {
            font-size: 12pt;
            font-weight: bold;
            color: #1a1a1a;
            margin: 0 0 2px 0;
        }

        .signature-title {
            font-size: 12pt;
            color: #4b5563;
            margin: 0;
        }

        .report-footer {
            text-align: center;
            font-size: 8pt;
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
        {{-- Tabla: datos izquierda, imagen derecha --}}
        <table class="equipment-top-table" cellpadding="0" cellspacing="0">
            <tr style="height: 260px;">
                <td class="equipment-data-cell">
                    <div class="equipment-data-div">
                        <div class="data-line"><strong>INFORME:</strong> {{ $reportData->consecutivo ?? '—' }}</div>
                        <div class="data-line" ><strong>PLANTA:</strong> {{ $reportData->equipment?->company?->name ?? '—' }}</div>
                        <div class="data-line"><strong>EQUIPO:</strong> <span class="value-equipo">{{ $reportData->equipment?->name ?? '—' }}</span></div>
                        <div class="data-line"><strong>CONDICIÓN:</strong>
                            @php
                                $condicion = $reportData->condition?->name ?? '';
                                $esAlarma = stripos($condicion, 'alarma') !== false;
                                $condTextColor = $reportData->condition?->text_color ?? '#000';
                                $condBgColor = $reportData->condition?->color;
                            @endphp
                            @if ($esAlarma)
                                <span class="condition-alarma" style="color: {{ $condTextColor }};{{ $condBgColor ? ' background: ' . $condBgColor . ';' : '' }}">{{ $condicion }}</span>
                            @else
                                <span class="condition-value" style="color: {{ $condTextColor }};{{ $condBgColor ? ' background: ' . $condBgColor . ';' : '' }}">{{ $condicion }}</span>
                            @endif
                        </div>
                        <div class="data-line"><strong>MECANISMO FALLA:</strong> {{ $reportData->mechanismFail->name ?? '—' }}</div>
                        <div class="data-line"><strong>COMPONENTE EN FALLA:</strong> {{ $reportData->componentFail->name ?? '—' }}</div>
                        <div class="data-line"><strong>PRIORIDAD ATENCIÓN:</strong> {{ $reportData->priority?->name ?? '—' }}</div>
                        <div class="data-line"><strong>FECHA MEDICIÓN:</strong> {{ $reportData->measurement_date?->format('d/m/Y') ?? '—' }}</div>
                    </div>
                </td>
                <td class="equipment-image-cell">
                    <div class="equipment-image-div">
                        @if(!empty($equipmentImage))
                            <img src="{{ $equipmentImage }}" alt="Equipo" class="equipment-image" />
                        @endif
                    </div>
                </td>
            </tr>
        </table>

        @foreach ($components as $component)
            <div class="section block component-section">
                <h3 class="section-title">{{ $component->componentType->name }}</h3>
                <p class="component-spec-paragraph">
                    <strong>Marca:</strong> {{ $component->brand->name ?? '—' }}.
                    @foreach (($component->fields ?? collect())->sortBy('display_order') as $field)
                        @php
                            $fieldVal = $component->fieldValues ? $component->fieldValues->where('component_type_field_id', $field->id)->first() : null;
                            $val = $fieldVal?->value ?? '';
                        @endphp
                        @if (trim((string) $val) !== '')
                            <strong>{{ $field->name }}:</strong> {{ $val }}{{ !empty($field->unit) ? ' ' . $field->unit : '' }}.
                        @endif
                    @endforeach
                </p>

                @php
                    $componentIdStr = (string) $component->id;
                    $diagnosisText = ($diagnosis ?? [])[$componentIdStr] ?? '';
                    $diagnosisText = is_array($diagnosisText) ? ($diagnosisText['text'] ?? '') : (string) $diagnosisText;
                @endphp
                @if (!empty(trim($diagnosisText)))
                    <p class="diagnosis-label">Diagnóstico:</p>
                    @php
                        $diagnosisHtml = strip_tags((string) $diagnosisText, '<b><strong><i><em><u><ul><ol><li><br><p>');
                    @endphp
                    <div class="diagnosis-text">{!! $diagnosisHtml !!}</div>
                @endif

                @php
                    $recs = ($recommendations ?? [])[$componentIdStr] ?? [];
                    $recs = is_array($recs) ? array_filter($recs, fn($r) => trim((string) $r) !== '') : [];
                @endphp
                @if (!empty($recs))
                    <p class="diagnosis-label">Recomendaciones:</p>
                    <ul class="rec-list">
                        @foreach ($recs as $rec)
                            @php
                                $recHtml = strip_tags((string) $rec, '<b><strong><i><em><u><br>');
                            @endphp
                            <li>{!! $recHtml !!}</li>
                        @endforeach
                    </ul>
                @endif

                @php
                    $images = ($componentImages ?? [])[$componentIdStr] ?? [];
                    $images = is_array($images) ? array_values(array_filter($images, fn($url) => !empty($url))) : [];
                @endphp
                @if (!empty($images))
                    @php
                        $rows = array_chunk($images, 2);
                    @endphp
                    <div class="spectrum-section">
                        <table class="image-container" cellpadding="0" cellspacing="0">
                            @foreach ($rows as $row)
                                <tr>
                                    @foreach ($row as $imageUrl)
                                        <td @if (count($row) === 1) colspan="2" @endif>
                                            <img src="{{ $imageUrl }}" alt="Espectro" height="100" style="max-height: 100px; width: auto;" />
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </table>
                    </div>
                @endif
            </div>
        @endforeach

        @php
            $generalRecs = $reportData->general_recommendations ?? [];
            $generalRecs = is_array($generalRecs) ? array_values(array_filter($generalRecs, fn($r) => trim((string)$r) !== '')) : [];
        @endphp
        @if (!empty($generalRecs))
            <div class="recommendation-general">
                <h3 class="recommendation-general-title">RECOMENDACIONES GENERALES:</h3>
                <ul class="recommendations-list">
                    @foreach ($generalRecs as $rec)
                        @php
                            $recHtml = strip_tags((string) $rec, '<b><strong><i><em><u><br>');
                        @endphp
                        <li>{!! $recHtml !!}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <p class="elaboro-line">Elaboró:</p>
        <p class="elaboro-line"><strong> {{ $reportData->analist?->name ?? '—' }}</strong></p>
        <p class="elaboro-line">{{ $reportData->analist?->workstation ?? '—' }}</p>
    </div>
</body>
</html>
