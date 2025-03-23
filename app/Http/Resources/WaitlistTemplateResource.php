<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WaitlistTemplateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $customizations = null;

        if ($this->whenLoaded('pivot')) {
            $customizations = $this->pivot->customizations ? json_decode($this->pivot->customizations, true) : [];
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'default_customizations' => $this->default_customizations ? json_decode($this->default_customizations, true) : [],
            'customizations' => $customizations,
            'is_active' => $this->whenPivotLoaded('project_waitlist_template', function () {
                return (bool) $this->pivot->is_active;
            }),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
