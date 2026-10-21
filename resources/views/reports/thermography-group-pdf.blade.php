<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>{{ utf8_sanitize($group->title) }}</title>
    <style>
        @include('reports.partials.thermography-styles')

        .page-break {
            page-break-after: always;
        }

        .page-break-before {
            page-break-before: always;
        }

        /* Portada */
        .cover {
            padding: 10px 6px 0;
            text-align: center;
        }

        .cover-accent {
            height: 6px;
            background-color: #f43f5e;
            margin-bottom: 36px;
        }

        .cover-logo {
            height: 70px;
            width: auto;
            margin-bottom: 34px;
        }

        .cover-kicker {
            margin: 0;
            font-size: 10pt;
            letter-spacing: 6px;
            color: #6b7280;
        }

        .cover-title {
            margin: 6px 0 0;
            font-size: 28pt;
            line-height: 1.15;
            color: #0f172a;
            letter-spacing: 1px;
        }

        .cover-subject {
            margin: 30px 0 4px;
            font-size: 15pt;
            font-weight: bold;
            color: #111827;
        }

        .cover-area {
            margin: 0;
            font-size: 11.5pt;
            color: #374151;
            letter-spacing: 1px;
        }

        .cover-period {
            margin: 22px 0 0;
            font-size: 12pt;
            font-weight: bold;
            letter-spacing: 3px;
            color: #f43f5e;
        }

        .cover-summary {
            margin: 34px auto 0;
            width: 78%;
            text-align: left;
            border-left: 3px solid #f43f5e;
            background-color: #f8fafc;
            padding: 10px 14px;
        }

        .cover-summary-title {
            margin: 0 0 4px;
            font-size: 9.5pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #374151;
        }

        .cover-summary-text {
            margin: 0;
            font-size: 9.5pt;
            color: #1f2937;
        }

        .cover-responsible {
            margin-top: 40px;
        }

        .cover-brand {
            margin: 0 0 6px;
            font-size: 11pt;
            font-weight: bold;
            letter-spacing: 3px;
            color: #0f172a;
        }

        .cover-responsible-name {
            margin: 0;
            font-size: 10pt;
            font-weight: bold;
        }

        .cover-responsible-line {
            margin: 1px 0;
            font-size: 9pt;
            color: #4b5563;
        }

        /* Índice */
        .section-heading {
            margin: 0 0 4px;
            font-size: 16pt;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .index-meta {
            margin: 0 0 16px;
            font-size: 9pt;
            color: #6b7280;
        }

        .index-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
        }

        .index-table th {
            background-color: #f8fafc;
            border-bottom: 1px solid #d1d5db;
            padding: 7px 8px;
            text-align: left;
            color: #374151;
            font-size: 8.5pt;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .index-table td {
            border-bottom: 1px solid #e5e7eb;
            padding: 7px 8px;
            vertical-align: top;
        }

        .index-table .muted {
            color: #6b7280;
        }

        /* Encabezado de cada punto */
        .point-head {
            border: 1px solid #d1d5db;
            background-color: #f8fafc;
            padding: 10px 12px;
            margin-bottom: 12px;
        }

        .point-number {
            display: inline-block;
            font-size: 8pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #f43f5e;
            margin-bottom: 6px;
        }

        .point-meta {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5pt;
        }

        .point-meta td {
            padding: 2px 10px 2px 0;
            vertical-align: top;
        }

        .point-meta .label {
            font-weight: bold;
            color: #374151;
        }

        /* Firmas */
        .signature-page {
            padding-top: 8px;
        }

        .signature-title {
            font-size: 14pt;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0 0 40px;
        }

        .signature-grid {
            width: 100%;
            border-collapse: collapse;
        }

        .signature-grid td {
            width: 50%;
            padding: 0 18px;
            vertical-align: bottom;
            text-align: center;
        }

        .signature-line {
            border-top: 1px solid #374151;
            padding-top: 6px;
            margin-top: 60px;
        }

        .signature-name {
            font-size: 9.5pt;
            font-weight: bold;
            margin: 0;
        }

        .signature-role,
        .signature-cert {
            font-size: 8.5pt;
            color: #4b5563;
            margin: 1px 0;
        }

        .signature-issued {
            margin-top: 46px;
            text-align: center;
            font-size: 9pt;
            color: #6b7280;
        }
    </style>
</head>

<body>
    @include('reports.partials.thermography-header-footer')

    @php
        $logoPath = public_path('images/logo-synergy.png');
        $logoSrc = file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : '';
        $period = $group->period;
        if (($period === null || trim((string) $period) === '') && $group->service_date) {
            $period = ucfirst($group->service_date->locale('es')->translatedFormat('F Y'));
        }
    @endphp

    <div class="report-content">
        {{-- Portada --}}
        <div class="cover">
            <div class="cover-accent"></div>

            @if ($logoSrc)
                <img src="{{ $logoSrc }}" alt="SYNERGY SMART" class="cover-logo" />
            @endif

            <p class="cover-kicker">INFORME DE</p>
            <h1 class="cover-title">{{ utf8_sanitize($group->title) }}</h1>

            @if (!empty($group->subject))
                <p class="cover-subject">{{ utf8_sanitize($group->subject) }}</p>
            @endif

            @if ($group->process)
                <p class="cover-area">ÁREA {{ mb_strtoupper(utf8_sanitize($group->process->name), 'UTF-8') }}</p>
            @endif

            @if ($period)
                <p class="cover-period">{{ mb_strtoupper(utf8_sanitize($period), 'UTF-8') }}</p>
            @endif

            @if (!empty($group->summary))
                <div class="cover-summary">
                    <p class="cover-summary-title">Resumen</p>
                    <p class="cover-summary-text">{{ utf8_sanitize($group->summary) }}</p>
                </div>
            @endif

            <div class="cover-responsible">
                <p class="cover-brand">SYNERGY SMART</p>
                <p class="cover-responsible-name">{{ utf8_sanitize($group->responsible_name) }}</p>
                @if (!empty($group->responsible_position))
                    <p class="cover-responsible-line">{{ utf8_sanitize($group->responsible_position) }}</p>
                @endif
                @if (!empty($group->responsible_certification))
                    <p class="cover-responsible-line">{{ utf8_sanitize($group->responsible_certification) }}</p>
                @endif
            </div>
        </div>

        <div class="page-break"></div>

        {{-- Índice --}}
        <h2 class="section-heading">Contenido</h2>
        <p class="index-meta">
            Informe {{ utf8_sanitize($group->display_code) }}
            @if ($group->service_date)
                · {{ $group->service_date->format('d/m/Y') }}
            @endif
            @if ($group->ubication)
                · {{ utf8_sanitize($group->ubication->name) }}
            @endif
        </p>

        <table class="index-table" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 32%;">Equipo</th>
                    <th style="width: 30%;">Componentes</th>
                    <th style="width: 15%;">Informe</th>
                    <th style="width: 18%;">Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($points as $index => $point)
                    @php
                        $pointReport = $point['report'];
                        $pointComponents = $point['components'];
                    @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            {{ utf8_sanitize($pointReport->equipment?->name ?? '—') }}
                            <br /><span class="muted">{{ utf8_sanitize($pointReport->equipment?->code ?? $pointReport->equipment?->tag ?? '') }}</span>
                        </td>
                        <td>{{ utf8_sanitize($pointComponents->pluck('componentType.name')->filter()->implode(', ') ?: '—') }}</td>
                        <td>{{ utf8_sanitize($pointReport->consecutivo) }}</td>
                        <td>{{ utf8_sanitize($pointReport->condition?->name ?? '—') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Puntos --}}
        @foreach ($points as $index => $point)
            @php
                $reportData = $point['reportData'];
                $components = $point['components'];
                $diagnosis = $point['diagnosis'];
                $recommendations = $point['recommendations'];
                $componentImages = $point['componentImages'];
                $measurementValues = $point['measurementValues'];
                $pointReport = $point['report'];
                $equipment = $pointReport->equipment;
            @endphp

            <div class="page-break-before">
                <div class="point-head">
                    <span class="point-number">Punto {{ $index + 1 }} de {{ count($points) }}</span>
                    <table class="point-meta" cellpadding="0" cellspacing="0">
                        <tr>
                            <td><span class="label">ÁREA:</span> {{ utf8_sanitize($equipment?->process?->name ?? '—') }}</td>
                            <td><span class="label">LOCALIZACIÓN:</span> {{ utf8_sanitize($equipment?->ubication?->name ?? '—') }}</td>
                        </tr>
                        <tr>
                            <td><span class="label">EQUIPO:</span> {{ utf8_sanitize($equipment?->name ?? '—') }}</td>
                            <td><span class="label">INFORME:</span> {{ utf8_sanitize($pointReport->consecutivo) }}</td>
                        </tr>
                        <tr>
                            <td><span class="label">ID:</span> {{ utf8_sanitize($equipment?->code ?? $equipment?->tag ?? '—') }}</td>
                            <td><span class="label">FECHA MEDICIÓN:</span> {{ $pointReport->measurement_date?->format('d/m/Y') ?? '—' }}</td>
                        </tr>
                    </table>
                </div>

                @foreach ($components as $component)
                    @include('reports.partials.thermography-card')
                @endforeach
            </div>
        @endforeach

        {{-- Firmas --}}
        <div class="page-break-before signature-page">
            <h2 class="signature-title">Firmas</h2>

            <table class="signature-grid" cellpadding="0" cellspacing="0">
                <tr>
                    <td>
                        <div class="signature-line">
                            <p class="signature-name">{{ utf8_sanitize($group->responsible_name) }}</p>
                            @if (!empty($group->responsible_position))
                                <p class="signature-role">{{ utf8_sanitize($group->responsible_position) }}</p>
                            @endif
                            @if (!empty($group->responsible_certification))
                                <p class="signature-cert">{{ utf8_sanitize($group->responsible_certification) }}</p>
                            @endif
                            <p class="signature-cert">Elaboró</p>
                        </div>
                    </td>
                    <td>
                        <div class="signature-line">
                            <p class="signature-name">&nbsp;</p>
                            <p class="signature-role">&nbsp;</p>
                            <p class="signature-cert">Revisó / Aprobó</p>
                        </div>
                    </td>
                </tr>
            </table>

            @if ($group->service_date)
                <p class="signature-issued">Emitido el {{ $group->service_date->format('d/m/Y') }}</p>
            @endif
        </div>
    </div>
</body>

</html>
