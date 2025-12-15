<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BaseResource extends JsonResource
{
    protected function toCamelCaseArray(array $array): array
    {
        $camelArray = [];
        foreach ($array as $key => $value) {
            $camelKey = lcfirst(str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $key))));
            $camelArray[$camelKey] = is_array($value)
                ? $this->toCamelCaseArray($value)
                : $value;
        }
        return $camelArray;
    }

    public function toArray($request)
    {
        return $this->toCamelCaseArray(parent::toArray($request));
    }
}
