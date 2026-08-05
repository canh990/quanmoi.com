<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CurrentUserResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'ho_ten' => $this->ho_ten,
            'email' => $this->email,
            'so_dien_thoai' => $this->so_dien_thoai,
            'dia_chi' => $this->dia_chi,
            'anh_dai_dien' => $this->anh_dai_dien,
        ];
    }
}
