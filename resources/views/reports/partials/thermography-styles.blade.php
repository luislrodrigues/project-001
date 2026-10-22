* { box-sizing: border-box; }

/* Activa la cabecera y el pie definidos con <htmlpageheader>/<htmlpagefooter> */
@page {
    header: html_page-header;
    footer: html_page-footer;
    margin-top: 32mm;
    margin-bottom: 16mm;
    margin-left: 12mm;
    margin-right: 12mm;
}

body {
    font-family: Arial, Helvetica, sans-serif;
    margin: 0;
    padding: 0;
    color: #1a1a1a;
    font-size: 10pt;
    line-height: 1.35;
}

/* Todo el documento (cabecera y cuerpo) usa el mismo ancho útil: 186 mm = 210 - 12 - 12 */
.page-header-wrap { width: 186mm; margin: 0; padding: 0; }
.header-accent { height: 4px; background-color: #f43f5e; margin-bottom: 6px; }
.header-main { width: 186mm; border-collapse: collapse; }
.header-main td { border: 1px solid #94a3b8; padding: 8px 10px; vertical-align: middle; }
.header-main .logo-cell { width: 30%; text-align: center; }
.header-logo-img { height: 40px; width: auto; max-width: 165px; }
.header-main .company-cell { width: 70%; }
.header-company { font-size: 11pt; font-weight: bold; letter-spacing: 0.6px; margin: 0 0 3px 0; }
.header-company-desc { font-size: 8pt; color: #4b5563; margin: 0; line-height: 1.25; }

/* Sin padding: el contenido usa exactamente los márgenes de @page,
   así la cabecera y el cuerpo quedan alineados al mismo ancho. */
.report-content { width: 186mm; padding: 0; margin: 0; }

.report-meta { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
.report-meta .data-cell { width: 62%; vertical-align: top; padding: 0 16px 0 0; }
.report-meta .image-cell { width: 38%; vertical-align: top; text-align: right; }
.report-meta .data-line { margin: 0 0 6px 0; font-size: 9.5pt; }
.report-meta .data-line .label { font-weight: bold; color: #374151; }
.equipment-image { max-width: 100%; max-height: 150px; border: 1px solid #e5e7eb; padding: 4px; }

/* La hoja no lleva borde; los cuadros internos sí. */
.card { border: none; margin: 0 0 16px 0; page-break-inside: avoid; }
.card-head { background-color: #f8fafc; border-bottom: 1px solid #e5e7eb; padding: 10px 12px; }
.card-head table { width: 100%; border-collapse: collapse; }
.card-head td { vertical-align: top; padding: 2px 0; font-size: 9.5pt; }
.card-head .col-left { width: 62%; padding-right: 12px; }
.card-head .col-right { width: 38%; }
.card-head .line { margin: 0 0 3px 0; }
.card-head .label { font-weight: bold; color: #374151; }
.estado {
    display: inline-block;
    padding: 2px 10px;
    font-weight: bold;
    border: 1px solid #d1d5db;
}

/* Cuadros de datos (generales y del componente) */
.info-grid { width: 100%; border-collapse: collapse; table-layout: fixed; margin-bottom: 14px; }
.info-grid td {
    border: 1px solid #94a3b8;
    padding: 5px 8px;
    font-size: 8.8pt;
    vertical-align: middle;
    text-transform: uppercase;
}
.info-grid .g-title {
    background-color: #e2e8f0;
    font-weight: bold;
    letter-spacing: 0.5px;
    font-size: 8.5pt;
    color: #1f2937;
}
.info-grid .g-label {
    background-color: #f1f5f9;
    font-weight: bold;
    color: #374151;
    width: 15%;
    white-space: nowrap;
}
.info-grid .g-value { font-weight: bold; width: 35%; }
.info-grid .g-status { width: 14%; text-align: center; font-weight: bold; border: 1px solid #94a3b8; }
.info-grid .g-empty { background-color: #ffffff; }

/* Fila con los datos generales del informe (pares etiqueta/valor) */
.report-meta-row { margin-bottom: 12px; }
.report-meta-row .g-label { width: 16%; font-size: 8pt; }
.report-meta-row .g-value { width: 34%; font-size: 8pt; }

/* Cuadros de Parámetros / Medidas y notas */
.boxes-row { width: 100%; border-collapse: collapse; table-layout: fixed; margin-bottom: 12px; }
.boxes-row > tbody > tr > td { vertical-align: top; padding: 0; }
.boxes-row .box-side { width: 50%; }
.boxes-row .box-left { padding-right: 6px; }
.boxes-row .box-right { padding-left: 6px; }

.panel { width: 100%; border-collapse: collapse; }
.panel td { border: 1px solid #94a3b8; padding: 3px 8px; font-size: 8.5pt; }
.panel .panel-title {
    background-color: #f1f5f9;
    font-weight: bold;
    font-size: 8.5pt;
    text-align: center;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    color: #374151;
}
.panel .p-label { color: #4b5563; width: 55%; }
.panel .p-value { font-weight: bold; text-align: right; width: 45%; }
.panel .a-label { color: #4b5563; width: 25%; }
.panel .a-value { font-weight: bold; text-align: left; width: 25%; }

.note-box { border: 1px solid #94a3b8; margin-bottom: 10px; }
.note-title {
    background-color: #f1f5f9;
    font-weight: bold;
    font-size: 8.5pt;
    padding: 4px 8px 0 8px;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    color: #374151;
}
.note-body { padding: 2px 8px 6px 8px; font-size: 9pt; }

/* Sin padding lateral: los cuadros del punto deben medir lo mismo que el resto */
.card-body { padding: 0; }

.params { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
.params td { border: 1px solid #e5e7eb; padding: 4px 8px; font-size: 9pt; vertical-align: top; }
.params .params-title { background-color: #f8fafc; font-weight: bold; color: #374151; font-size: 9.5pt; }
.params .plabel { color: #6b7280; width: 17%; }
.params .pvalue { font-weight: bold; width: 33%; }

.section-title { font-weight: bold; color: #374151; font-size: 9.5pt; margin: 0 0 3px 0; }
.diagnosis-text { margin: 0 0 8px 0; }
.rec-list { margin: 0 0 8px 16px; padding: 0; }
.rec-list li { margin-bottom: 2px; }

.images-table { width: 100%; border-collapse: collapse; margin-top: 6px; }
.images-table td { padding: 4px; text-align: center; vertical-align: top; }
.images-table img { max-width: 100%; }

.norm-title { font-size: 8.5pt; font-weight: bold; color: #374151; margin: 10px 0 4px 0; }
.norm-table { width: 100%; border-collapse: collapse; font-size: 8.5pt; }
.norm-table th, .norm-table td { border: 1px solid #e5e7eb; padding: 4px 6px; text-align: left; }
.norm-table th { background-color: #f8fafc; }

.report-footer { text-align: center; font-size: 8pt; color: #6b7280; }
