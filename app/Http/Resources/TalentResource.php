<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

/**
 * Class TalentResource
 *
 * @package App\Http\Resources
 * @mixin \App\Talent
 */
class TalentResource extends \Illuminate\Http\Resources\Json\JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $result = [
            'name' => $this->name,
            'title' => $this->title,
            'description' => $this->description,
            'icon' => $this->icon,
            'icon_url' => $this->icon_url,
            'ability' => $this->ability_id,
            'sort' => $this->sort,
            'cooldown' => $this->cooldown,
            'mana_cost' => $this->mana_cost,
            'level' => $this->level,
        ];
        if ($this->relationLoaded('heroes')) {
            $result['heroes'] = $this->heroes->pluck('name');
        }
        return $result;
    }
}
