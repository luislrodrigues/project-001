<div class="norm-block">
    <p class="norm-title">ANSI/NETA AST-2013 – Tabla 100.18 Acción Sugerida Basada en el Aumento de la Temperatura</p>
    <table class="norm-table" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th class="norm-col-range">Diferencia de temperatura (ΔT) entre componentes similares bajo cargas similares</th>
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
                    <td class="norm-col-range">{{ $range['range'] }}</td>
                    <td style="text-align: center; font-weight: bold;@if ($rangeStyle) color: {{ $rangeStyle['text_color'] }}; background: {{ $rangeStyle['color'] }};@endif">{{ $range['condition'] }}</td>
                    <td>{{ $range['action'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
