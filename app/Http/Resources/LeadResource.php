<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LeadResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'nombre'           => $this->nombre,
            'email'            => $this->email,
            'telefono'         => $this->telefono,
            'fuente'           => $this->fuente,
            'producto_interes' => $this->producto_interes,
            'presupuesto'      => $this->presupuesto,
            'created_at'       => $this->created_at->toISOString(),
            'updated_at'       => $this->updated_at->toISOString(),
        ];
    }
}
