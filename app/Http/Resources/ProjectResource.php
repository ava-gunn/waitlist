<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'subdomain' => $this->subdomain,
            'description' => $this->description,
            'logo_path' => $this->logo_path,
            'settings' => $this->settings,
            'is_active' => $this->is_active,
            'full_url' => $this->full_url,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'user' => new UserResource($this->whenLoaded('user')),
            'waitlist_templates' => WaitlistTemplateResource::collection($this->whenLoaded('waitlistTemplates')),
            'signups' => SignupResource::collection($this->whenLoaded('signups')),
            'signups_count' => $this->whenCounted('signups'),
        ];
    }
}
