* { box-sizing: border-box; }

body {
    font-family: Arial, Helvetica, sans-serif;
    margin: 0;
    padding: 0;
    color: #1a1a1a;
    font-size: 10pt;
    line-height: 1.35;
}

.page-header-wrap { width: 100%; margin: 0; padding: 0; }
.header-accent { height: 5px; background-color: #f43f5e; }
.header-main { background-color: #f8fafc; padding: 14px 24px; }
.header-logo-img { height: 44px; width: auto; max-width: 180px; }

.report-content { padding: 18px 24px; }

.report-meta { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
.report-meta .data-cell { width: 62%; vertical-align: top; padding: 0 16px 0 0; }
.report-meta .image-cell { width: 38%; vertical-align: top; text-align: right; }
.report-meta .data-line { margin: 0 0 6px 0; font-size: 9.5pt; }
.report-meta .data-line .label { font-weight: bold; color: #374151; }
.equipment-image { max-width: 100%; max-height: 150px; border: 1px solid #e5e7eb; padding: 4px; }

.card { border: 1px solid #d1d5db; margin-bottom: 18px; page-break-inside: avoid; }
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

.card-body { padding: 10px 12px; }

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
.images-table td { padding: 4px; text-align: center; }
.images-table img { width: 100%; max-height: 220px; }

.norm-title { font-size: 8.5pt; font-weight: bold; color: #374151; margin: 10px 0 4px 0; }
.norm-table { width: 100%; border-collapse: collapse; font-size: 8.5pt; }
.norm-table th, .norm-table td { border: 1px solid #e5e7eb; padding: 4px 6px; text-align: left; }
.norm-table th { background-color: #f8fafc; }

.report-footer { text-align: center; font-size: 8pt; color: #6b7280; }
