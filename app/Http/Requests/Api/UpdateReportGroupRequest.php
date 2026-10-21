<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReportGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('edit report');
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'report_type' => 'required|string|in:thermography',
            'company_id' => 'required|integer|exists:companies,id',
            'process_id' => 'nullable|integer|exists:processes,id',
            'ubication_id' => 'nullable|integer|exists:ubications,id',
            'subject' => 'nullable|string|max:255',
            'period' => 'nullable|string|max:100',
            'service_date' => 'required|date',
            'summary' => 'nullable|string|max:5000',
            'responsible_name' => 'required|string|max:255',
            'responsible_position' => 'nullable|string|max:255',
            'responsible_certification' => 'nullable|string|max:255',
            'report_ids' => 'required|array|min:1',
            'report_ids.*' => 'integer|exists:reports,id',
        ];
    }
}
