@php
    $componentIdStr = (string) $component->id;

    // Formato es: decimales con coma, sin separador de miles
    $formatParam = function ($value, $unit = '') {
        $raw = trim((string) $value);
        if ($raw === '') {
            return '';
        }
        if (preg_match('/^-?\d+(?:[.,]\d+)?$/', $raw) === 1) {
            $number = (float) str_replace(',', '.', $raw);
            $raw = number_format($number, 2, ',', '');
            $raw = rtrim(rtrim($raw, '0'), ',');
        }

        $unit = trim((string) $unit);

        return $unit !== '' ? $raw . ' ' . $unit : $raw;
    };

    // Valores capturados en este punto, indexados por slug del campo
    $pointValues = [];
    foreach (($measurementValues[$componentIdStr] ?? []) as $measurement) {
        $pointValues[$measurement['slug'] ?? ''] = $measurement;
    }

    $estado = utf8_sanitize((string) ($pointValues['condicion']['value'] ?? ''));
    $estadoStyle = ($conditionStyles ?? [])[$estado] ?? ['color' => '#e5e7eb', 'text_color' => '#111827'];
    $delta = isset($pointValues['delta-t'])
        ? $formatParam($pointValues['delta-t']['value'], $pointValues['delta-t']['unit'] ?? '')
        : '';
    $mecanismo = utf8_sanitize((string) ($pointValues['mecanismo-falla']['value'] ?? ''));
    $prioridad = utf8_sanitize((string) ($pointValues['prioridad']['value'] ?? ''));
    $imagenTermica = isset($pointValues['imagen-termica'])
        ? $formatParam($pointValues['imagen-termica']['value'], $pointValues['imagen-termica']['unit'] ?? '')
        : '';

    // Características propias del componente (no son datos de la medición)
    $attributes = [];
    foreach (($component->fields ?? collect())->sortBy('display_order') as $field) {
        if (($field->scope ?? 'component') !== 'component') {
            continue;
        }
        $fieldVal = $component->fieldValues ? $component->fieldValues->where('component_type_field_id', $field->id)->first() : null;
        $val = utf8_sanitize($fieldVal?->value ?? '');
        if (trim((string) $val) !== '') {
            $attributes[] = [
                'label' => utf8_sanitize($field->name),
                'value' => $formatParam($val, utf8_sanitize($field->unit ?? '')),
            ];
        }
    }

    $diagnosisText = ($diagnosis ?? [])[$componentIdStr] ?? '';
    $diagnosisText = is_array($diagnosisText) ? ($diagnosisText['text'] ?? '') : (string) $diagnosisText;

    $recs = ($recommendations ?? [])[$componentIdStr] ?? [];
    $recs = is_array($recs) ? array_values(array_filter($recs, fn ($r) => trim((string) $r) !== '')) : [];

    $images = ($componentImages ?? [])[$componentIdStr] ?? [];
    $images = is_array($images) ? array_values(array_filter($images, function ($image) {
        return is_array($image) ? !empty($image['url']) : !empty($image);
    })) : [];
    $images = array_map(function ($image) {
        if (is_array($image)) {
            return [
                'url' => $image['url'],
                'kind' => $image['kind'] ?? '',
                'caption' => $image['caption'] ?? '',
                'width' => (int) ($image['dimensions']['width'] ?? 300),
                'height' => (int) ($image['dimensions']['height'] ?? 195),
            ];
        }

        return ['url' => $image, 'kind' => '', 'caption' => '', 'width' => 300, 'height' => 195];
    }, $images);
@endphp

<div class="card">
    <div class="card-body">
        <table class="info-grid" cellpadding="0" cellspacing="0">
            <tr>
                <td class="g-label">Proceso</td>
                <td class="g-value">{{ utf8_sanitize($reportData->equipment?->process?->name ?? '—') }}</td>
                <td class="g-label">Condición</td>
                <td class="g-status" style="color: {{ $estadoStyle['text_color'] }}; background: {{ $estadoStyle['color'] }};">
                    {{ $estado !== '' ? $estado : 'NA' }}
                </td>
            </tr>
            <tr>
                <td class="g-label">Ubicación</td>
                <td class="g-value">{{ utf8_sanitize($reportData->equipment?->ubication?->name ?? '—') }}</td>
                <td class="g-label">Prioridad</td>
                <td class="g-value" style="text-align: center;">{{ $prioridad !== '' ? $prioridad : 'NA' }}</td>
            </tr>
            <tr>
                <td class="g-label">Equipo</td>
                <td class="g-value">{{ utf8_sanitize($reportData->equipment?->name ?? '—') }}</td>
                <td class="g-label">Delta T°</td>
                <td class="g-value" style="text-align: center;">{{ $delta !== '' ? $delta : 'NA' }}</td>
            </tr>
            <tr>
                <td class="g-label">ID</td>
                <td class="g-value">{{ utf8_sanitize($reportData->equipment?->code ?? $reportData->equipment?->tag ?? '—') }}</td>
                <td class="g-label" rowspan="2" style="vertical-align: middle;">Mecanismo falla</td>
                <td class="g-value" rowspan="2" style="vertical-align: middle; text-align: center;">
                    {{ $mecanismo !== '' ? $mecanismo : 'NA' }}
                </td>
            </tr>
            <tr>
                <td class="g-label">Componente</td>
                <td class="g-value">{{ utf8_sanitize($component->componentType->name ?? '—') }}</td>
            </tr>
        </table>

        @if (!empty($images))
            <table class="images-table" cellpadding="0" cellspacing="0">
                @foreach (array_chunk($images, 2) as $imageRow)
                    <tr>
                        @foreach ($imageRow as $image)
                            <td @if (count($imageRow) === 1) colspan="2" @endif>
                                <img src="{{ $image['url'] }}" width="{{ $image['width'] }}" height="{{ $image['height'] }}" alt="Imagen {{ $image['kind'] ?: 'térmica' }}" />
                                @if ($image['kind'] !== '' || $image['caption'] !== '')
                                    <div style="font-size: 8.5px; color: #4b5563; padding-top: 2px;">
                                        @if ($image['kind'] !== '')
                                            <span style="display: inline-block; font-weight: bold; text-transform: uppercase; letter-spacing: 0.3px;">{{ $image['kind'] }}</span>
                                        @endif
                                        @if ($image['kind'] !== '' && $image['caption'] !== '')
                                            <span> — </span>
                                        @endif
                                        @if ($image['caption'] !== '')
                                            <span>{{ $image['caption'] }}</span>
                                        @endif
                                    </div>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </table>
        @endif

        <table class="panel" cellpadding="0" cellspacing="0" style="margin-bottom: 12px;">
            <tr>
                <td class="panel-title" colspan="4">Parámetros</td>
            </tr>
            <tr>
                <td class="a-label">Fecha medición:</td>
                <td class="a-value">{{ $reportData->measurement_date?->format('d/m/Y') ?? '—' }}</td>
                <td class="a-label">Emisividad:</td>
                <td class="a-value">{{ $reportData->emissivity !== null ? number_format((float) $reportData->emissivity, 2, ',', '') : '—' }}</td>
            </tr>
            <tr>
                <td class="a-label">Temp. ambiente:</td>
                <td class="a-value">{{ $reportData->ambient_temp !== null ? number_format((float) $reportData->ambient_temp, 2, ',', '') . ' °C' : '—' }}</td>
                <td class="a-label">Distancia:</td>
                <td class="a-value">{{ $reportData->distance_m !== null ? number_format((float) $reportData->distance_m, 2, ',', '') . ' m' : '—' }}</td>
            </tr>
            <tr>
                <td class="a-label">Imagen térmica:</td>
                <td class="a-value">{{ $imagenTermica !== '' ? $imagenTermica : 'NA' }}</td>
                <td class="a-label"></td>
                <td class="a-value"></td>
            </tr>
        </table>

        @if (!empty($attributes))
            <table class="panel" cellpadding="0" cellspacing="0" style="margin-bottom: 12px;">
                <tr>
                    <td class="panel-title" colspan="4">Características del componente</td>
                </tr>
                @foreach (array_chunk($attributes, 2) as $attributeRow)
                    <tr>
                        @foreach ($attributeRow as $attribute)
                            <td class="a-label">{{ $attribute['label'] }}:</td>
                            <td class="a-value">{{ $attribute['value'] !== '' ? $attribute['value'] : '—' }}</td>
                        @endforeach
                        @for ($i = count($attributeRow); $i < 2; $i++)
                            <td class="a-label"></td>
                            <td class="a-value"></td>
                        @endfor
                    </tr>
                @endforeach
            </table>
        @endif

        <div class="note-box">
            <div class="note-title">Diagnóstico:</div>
            <div class="note-body">
                @if (!empty(trim($diagnosisText)))
                    @php
                        $diagnosisHtml = strip_tags((string) $diagnosisText, '<b><strong><i><em><u><ul><ol><li><br><p><div>');
                        $diagnosisHtml = iconv('UTF-8', 'UTF-8//IGNORE', $diagnosisHtml);
                        $diagnosisHtml = preg_replace("/\r\n|\r/", "\n", $diagnosisHtml);
                        $diagnosisHtml = str_replace("\n", "<br>", $diagnosisHtml);
                        $diagnosisHtml = preg_replace('/>\s+</', '><', $diagnosisHtml);
                        $diagnosisHtml = preg_replace('/(?:<br\s*\/?>\s*){2,}/i', '<br>', $diagnosisHtml);
                        $diagnosisHtml = preg_replace('/<(p|div)(\s[^>]*)?>\s*(?:<br\s*\/?>\s*)?<\/\1>/i', '<$1$2>&nbsp;</$1>', $diagnosisHtml);
                    @endphp
                    <div class="diagnosis-text">{!! $diagnosisHtml !!}</div>
                @else
                    <span style="color: #6b7280;">Sin diagnóstico registrado.</span>
                @endif
            </div>
        </div>

        <div class="note-box">
            <div class="note-title">Recomendación:</div>
            <div class="note-body">
                @if (!empty($recs))
                    <ul class="rec-list">
                        @foreach ($recs as $rec)
                            @php
                                $recHtml = strip_tags((string) $rec, '<b><strong><i><em><u><br><p><div>');
                                $recHtml = iconv('UTF-8', 'UTF-8//IGNORE', $recHtml);
                                $recHtml = nl2br($recHtml, false);
                                $recHtml = preg_replace('/(?:<br\s*\/?>\s*){2,}/i', '<br>', $recHtml);
                            @endphp
                            <li>{!! $recHtml !!}</li>
                        @endforeach
                    </ul>
                @else
                    <span style="color: #6b7280;">Sin recomendación registrada.</span>
                @endif
            </div>
        </div>

        <p class="norm-title">ANSI/NETA AST-2013 – Tabla 100.18 Acción Sugerida Basada en el Aumento de la Temperatura</p>
        <table class="norm-table" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th>Diferencia de temperatura (ΔT) entre componentes similares bajo cargas similares</th>
                    <th>Clasificación</th>
                    <th>Acción Recomendada</th>
                </tr>
            </thead>
            <tbody>
                @foreach (array_slice(\App\Support\ThermographyClassifier::SCALE, 1) as $range)
                    @php
                        $rangeStyle = ($conditionStyles ?? [])[$range['condition']] ?? null;
                    @endphp
                    <tr>
                        <td>{{ $range['range'] }}</td>
                        <td style="text-align: center; font-weight: bold;@if ($rangeStyle) color: {{ $rangeStyle['text_color'] }}; background: {{ $rangeStyle['color'] }};@endif">{{ $range['condition'] }}</td>
                        <td>{{ $range['action'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
