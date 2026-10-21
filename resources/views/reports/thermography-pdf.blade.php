<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Informe de Termografía</title>
    <style>
        @include('reports.partials.thermography-styles')
    </style>
</head>

<body>
    @include('reports.partials.thermography-header-footer')

    <div class="report-content">
        <table class="report-meta" cellpadding="0" cellspacing="0">
            <tr>
                <td class="data-cell">
                    <div class="data-line"><span class="label">INFORME:</span> {{ utf8_sanitize($reportData->consecutivo ?? '—') }}</div>
                    <div class="data-line"><span class="label">PLANTA:</span> {{ utf8_sanitize($reportData->equipment?->company?->name ?? '—') }}</div>
                    <div class="data-line"><span class="label">EQUIPO:</span> {{ utf8_sanitize($reportData->equipment?->name ?? '—') }}</div>
                    <div class="data-line"><span class="label">PLACA/ID:</span> {{ utf8_sanitize($reportData->equipment?->code ?? $reportData->equipment?->tag ?? '—') }}</div>
                    <div class="data-line"><span class="label">UBICACIÓN:</span> {{ utf8_sanitize($reportData->equipment?->ubication?->name ?? '—') }}</div>
                    <div class="data-line"><span class="label">ÁREA:</span> {{ utf8_sanitize($reportData->equipment?->process?->name ?? '—') }}</div>
                    <div class="data-line"><span class="label">FECHA MEDICIÓN:</span> {{ $reportData->measurement_date?->format('d/m/Y') ?? '—' }}</div>
                    <div class="data-line"><span class="label">ANALISTA:</span> {{ utf8_sanitize($reportData->analist?->name ?? '—') }}</div>
                </td>
                <td class="image-cell">
                    @if (!empty($equipmentImage))
                        <img src="{{ $equipmentImage }}" alt="Equipo" class="equipment-image" />
                    @endif
                </td>
            </tr>
        </table>

        @foreach ($components as $component)
            @include('reports.partials.thermography-card')
        @endforeach

        @php
            $generalRecs = $reportData->general_recommendations ?? [];
            $generalRecs = is_array($generalRecs) ? array_values(array_filter($generalRecs, fn ($r) => trim((string) $r) !== '')) : [];
        @endphp
        @if (!empty($generalRecs))
            <div class="card">
                <div class="card-body">
                    <p class="section-title">RECOMENDACIONES GENERALES:</p>
                    <ul class="rec-list">
                        @foreach ($generalRecs as $rec)
                            @php
                                $recHtml = strip_tags((string) $rec, '<b><strong><i><em><u><br><p><div>');
                                $recHtml = iconv('UTF-8', 'UTF-8//IGNORE', $recHtml);
                                $recHtml = nl2br($recHtml, false);
                                $recHtml = preg_replace('/(?:<br\s*\/?>\s*){2,}/i', '<br>', $recHtml);
                            @endphp
                            <li>{!! $recHtml !!}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
    </div>
</body>

</html>
