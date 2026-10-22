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
        <table class="info-grid report-meta-row" cellpadding="0" cellspacing="0">
            <tr>
                <td class="g-title" colspan="4">Datos generales del informe</td>
            </tr>
            <tr>
                <td class="g-label">Informe</td>
                <td class="g-value">{{ utf8_sanitize($reportData->consecutivo ?? '—') }}</td>
                <td class="g-label">Planta</td>
                <td class="g-value">{{ utf8_sanitize($reportData->equipment?->company?->name ?? '—') }}</td>
            </tr>
            <tr>
                <td class="g-label">Analista</td>
                <td class="g-value">{{ utf8_sanitize($reportData->analist?->name ?? '—') }}</td>
                <td class="g-label">OT / Estado</td>
                <td class="g-value">
                    {{ utf8_sanitize(trim(($reportData->ot_created ?? '') . ' ' . ($reportData->status_ot ?? '')) ?: '—') }}
                </td>
            </tr>
        </table>

        @if (!empty($equipmentImage))
            <div style="text-align: right; margin-bottom: 12px;">
                <img src="{{ $equipmentImage }}" alt="Equipo" class="equipment-image" />
            </div>
        @endif

        @foreach ($components as $component)
            @include('reports.partials.thermography-card')
        @endforeach

        @php
            $generalRecs = $reportData->general_recommendations ?? [];
            $generalRecs = is_array($generalRecs) ? array_values(array_filter($generalRecs, fn ($r) => trim((string) $r) !== '')) : [];
        @endphp
        @if (!empty($generalRecs))
            <div class="note-box">
                <div class="note-title">Recomendaciones generales:</div>
                <div class="note-body">
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
