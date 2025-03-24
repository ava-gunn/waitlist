<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    /**
     * Disable wrapping of the resource in a 'data' object.
     *
     * @var bool
     */
    public static $wrap = null;

    /**
     * Transform the resource into an array.
     */
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
            'waitlist_template' => new WaitlistTemplateResource($this->whenLoaded('waitlistTemplate')),
            'waitlist_template_id' => $this->waitlist_template_id,
            'template_customizations' => $this->template_customizations ?: [],
            'signups' => SignupResource::collection($this->whenLoaded('signups')),
            'signups_count' => $this->whenCounted('signups'),
        ];
    }
}
