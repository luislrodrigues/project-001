<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'workstation' => $this->workstation,
            'status' => $this->status,
            'company' => [
                'id' => $this->company->id ?? null,
                'name' => $this->company->name ?? null,
            ],
            'role' => $this->roles->first() ? [
                'id' => $this->roles->first()->id,
                'name' => $this->roles->first()->name,
            ] : null,
            'raw_password' => $this->when(
                $request->boolean('with_password') && $request->user()?->can('detalle user'),
                fn () => $this->raw_password ?: null
            ),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
