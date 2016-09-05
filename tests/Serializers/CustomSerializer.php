<?php

namespace Gregoriohc\Kaleidoscope\Tests\Serializers;

use League\Fractal;

class CustomSerializer extends Fractal\Serializer\JsonApiSerializer
{
    public function item($resourceKey, array $data)
    {
        $id = $this->getIdFromData($data);

        $resource = [
            'data' => [
                'type'             => $resourceKey,
                'id'               => "$id",
                'attributes_count' => count($data),
                'attributes'       => $data,
            ],
        ];

        unset($resource['data']['attributes']['id']);

        if ($this->shouldIncludeLinks()) {
            $resource['data']['links'] = [
                'self' => "{$this->baseUrl}/$resourceKey/$id",
            ];
        }

        return $resource;
    }
}
