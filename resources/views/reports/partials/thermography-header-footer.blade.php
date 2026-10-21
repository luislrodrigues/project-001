@php
    $logoPath = public_path('images/logo-synergy.png');
    $logoSrc = file_exists($logoPath)
        ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
        : '';
@endphp

<htmlpageheader name="page-header">
    <div class="page-header-wrap">
        <div class="header-accent"></div>
        <div class="header-main">
            @if ($logoSrc)
                <img src="{{ $logoSrc }}" alt="SYNERGY SMART" class="header-logo-img" />
            @endif
        </div>
    </div>
</htmlpageheader>

<htmlpagefooter name="page-footer">
    <div class="report-footer">
        Ingeniería de Mantenimiento Predictivo y Confiabilidad
    </div>
</htmlpagefooter>
