@php
    $logoPath = public_path('images/logo-synergy.png');
    $logoSrc = file_exists($logoPath)
        ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
        : '';
@endphp

<htmlpageheader name="page-header">
    <div class="page-header-wrap">
        <div class="header-accent"></div>
        <table class="header-main" cellpadding="0" cellspacing="0">
            <tr>
                <td class="logo-cell">
                    @if ($logoSrc)
                        <img src="{{ $logoSrc }}" alt="SYNERGY SMART" class="header-logo-img" />
                    @else
                        <span class="header-company">SYNERGY SMART</span>
                    @endif
                </td>
                <td class="company-cell">
                    <p class="header-company">SYNERGY SMART</p>
                    <p class="header-company-desc">
                        Ingeniería mecánica, mantenimiento predictivo, fabricación, mecanizado y reparación de
                        maquinaria industrial
                    </p>
                </td>
            </tr>
        </table>
    </div>
</htmlpageheader>

<htmlpagefooter name="page-footer">
    <div class="report-footer">
        Ingeniería de Mantenimiento Predictivo y Confiabilidad
    </div>
</htmlpagefooter>
