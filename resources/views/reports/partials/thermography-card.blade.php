@php
    $componentIdStr = (string) $component->id;

    $condicion = utf8_sanitize($reportData->condition?->name ?? '');
    $condTextColor = $reportData->condition?->text_color ?? '#000000';
    $condBgColor = $reportData->condition?->color ?? '#ffffff';

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

    // Parámetros: fecha de medición + valores capturados en la medición + campos del componente
    $params = [[
        'label' => 'Fecha medición',
        'value' => $reportData->measurement_date?->format('d/m/Y') ?? '—',
    ]];

    foreach (($measurementValues[$componentIdStr] ?? []) as $measurement) {
        $params[] = [
            'label' => $measurement['name'],
            'value' => $formatParam($measurement['value'], $measurement['unit'] ?? ''),
        ];
    }

    foreach (($component->fields ?? collect())->sortBy('display_order') as $field) {
        $fieldVal = $component->fieldValues ? $component->fieldValues->where('component_type_field_id', $field->id)->first() : null;
        $val = utf8_sanitize($fieldVal?->value ?? '');
        if (trim((string) $val) !== '') {
            $params[] = [
                'label' => utf8_sanitize($field->name),
                'value' => $formatParam($val, utf8_sanitize($field->unit ?? '')),
            ];
        }
    }

    $paramRows = array_chunk($params, 2);

    $diagnosisText = ($diagnosis ?? [])[$componentIdStr] ?? '';
    $diagnosisText = is_array($diagnosisText) ? ($diagnosisText['text'] ?? '') : (string) $diagnosisText;

    $recs = ($recommendations ?? [])[$componentIdStr] ?? [];
    $recs = is_array($recs) ? array_filter($recs, fn ($r) => trim((string) $r) !== '') : [];

    $images = ($componentImages ?? [])[$componentIdStr] ?? [];
    $images = is_array($images) ? array_values(array_filter($images, function ($image) {
        return is_array($image) ? !empty($image['url']) : !empty($image);
    })) : [];
    $images = array_map(function ($image) {
        return is_array($image)
            ? ['url' => $image['url'], 'kind' => $image['kind'] ?? '', 'caption' => $image['caption'] ?? '']
            : ['url' => $image, 'kind' => '', 'caption' => ''];
    }, $images);
@endphp

<div class="card">
    <div class="card-head">
        <table cellpadding="0" cellspacing="0">
            <tr>
                <td class="col-left">
                    <p class="line"><span class="label">EQUIPO:</span> {{ utf8_sanitize($reportData->equipment?->name ?? '—') }}</p>
                    <p class="line"><span class="label">ID:</span> {{ utf8_sanitize($reportData->equipment?->code ?? $reportData->equipment?->tag ?? '—') }}</p>
                    <p class="line"><span class="label">COMPONENTE:</span> {{ utf8_sanitize($component->componentType->name ?? '—') }}</p>
                    <p class="line"><span class="label">MARCA:</span> {{ utf8_sanitize($component->brand->name ?? '—') }}</p>
                </td>
                <td class="col-right">
                    <p class="line">
                        <span class="label">ESTADO:</span>
                        <span class="estado" style="color: {{ $condTextColor }}; background: {{ $condBgColor }};">{{ $condicion !== '' ? $condicion : '—' }}</span>
                    </p>
                    <p class="line"><span class="label">MECANISMO FALLA:</span> {{ utf8_sanitize($reportData->mechanismFail?->name ?? '—') }}</p>
                    <p class="line"><span class="label">COMPONENTE EN FALLA:</span> {{ utf8_sanitize($reportData->componentFail?->name ?? '—') }}</p>
                    <p class="line"><span class="label">PRIORIDAD ATENCIÓN:</span> {{ utf8_sanitize($reportData->priority?->name ?? '—') }}</p>
                </td>
            </tr>
        </table>
    </div>

    <div class="card-body">
        <table class="params" cellpadding="0" cellspacing="0">
            <tr>
                <td class="params-title" colspan="4">Parámetros</td>
            </tr>
            @foreach ($paramRows as $row)
                <tr>
                    @foreach ($row as $param)
                        <td class="plabel">{{ $param['label'] }}:</td>
                        <td class="pvalue">{{ $param['value'] !== '' ? $param['value'] : '—' }}</td>
                    @endforeach
                    @for ($i = count($row); $i < 2; $i++)
                        <td class="plabel"></td>
                        <td class="pvalue"></td>
                    @endfor
                </tr>
            @endforeach
        </table>

        @if (!empty(trim($diagnosisText)))
            <p class="section-title">DIAGNÓSTICO:</p>
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
        @endif

        @if (!empty($recs))
            <p class="section-title">RECOMENDACIÓN:</p>
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
        @endif

        @if (!empty($images))
            <table class="images-table" cellpadding="0" cellspacing="0">
                @foreach (array_chunk($images, 2) as $imageRow)
                    <tr>
                        @foreach ($imageRow as $image)
                            <td @if (count($imageRow) === 1) colspan="2" @endif>
                                <img src="{{ $image['url'] }}" alt="Imagen {{ $image['kind'] ?: 'térmica' }}" />
                                @if ($image['kind'] !== '' || $image['caption'] !== '')
                                    <div style="font-size: 9px; color: #4b5563; padding-top: 2px;">
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
                    <tr>
                        <td>{{ $range['range'] }}</td>
                        <td>{{ $range['condition'] }}</td>
                        <td>{{ $range['action'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
