<?php

namespace Gregoriohc\Kaleidoscope\Tests;

use Gregoriohc\Kaleidoscope\Tests\Models\User;
use Gregoriohc\Kaleidoscope\Tests\Serializers\CustomSerializer;
use Gregoriohc\Kaleidoscope\Tests\Transformers\CustomUserTransformer;

class FractalizableTest extends TestCase
{
    public function testModelToArray()
    {
        User::setTransformer(null);

        $userStructure = ['id', 'name', 'email', 'created_at', 'updated_at'];

        $originalData = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
        ];

        /** @var User $user */
        $user = User::create($originalData);

        $toArrayData = $user->toArray();

        $this->seeArrayStructure($userStructure, $toArrayData);

        foreach ($originalData as $key => $value) {
            if (in_array($key, $userStructure)) {
                $this->assertEquals($toArrayData[$key], $originalData[$key]);
            }
        }
    }

    public function testModelCustomTransformerToArray()
    {
        User::setTransformer(new CustomUserTransformer());

        $userStructure = ['id', 'name'];

        $originalData = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
        ];

        /** @var User $user */
        $user = User::create($originalData);

        $toArrayData = $user->toArray();

        $this->seeArrayStructure($userStructure, $toArrayData);

        foreach ($originalData as $key => $value) {
            if (in_array($key, $userStructure)) {
                $this->assertEquals($toArrayData[$key], $originalData[$key]);
            }
        }
    }

    public function testModelPagination()
    {
        User::setTransformer(null);

        $paginatedStructure = [
            'data' => [
                '*' => [
                    'type',
                    'id',
                    'attributes' => ['name', 'email', 'created_at', 'updated_at'],
                ]
            ],
            'meta' => ['total-pages', 'per-page', 'current-page', 'last-page', 'from', 'to'],
            'links' => ['self', 'first', 'next', 'prev', 'last'],
        ];

        $originalData = [
            [
                'name' => 'John Doe',
                'email' => 'john.doe@example.com',
            ],
            [
                'name' => 'Jane Roe',
                'email' => 'jane.roe@example.com',
            ]
        ];

        foreach ($originalData as $item) {
            User::create($item);
        }

        $paginateData = User::paginate()->toArray();

        $this->seeArrayStructure($paginatedStructure, $paginateData);
    }

    public function testModelSimplePagination()
    {
        User::setTransformer(null);

        $paginatedStructure = [
            'data' => [
                '*' => [
                    'type',
                    'id',
                    'attributes' => ['name', 'email', 'created_at', 'updated_at'],
                ]
            ],
            'meta' => ['per-page', 'current-page', 'from', 'to'],
            'links' => ['self', 'next', 'prev'],
        ];

        $originalData = [
            [
                'name' => 'John Doe',
                'email' => 'john.doe@example.com',
            ],
            [
                'name' => 'Jane Roe',
                'email' => 'jane.roe@example.com',
            ]
        ];

        foreach ($originalData as $item) {
            User::create($item);
        }

        $paginateData = User::simplePaginate()->toArray();

        $this->seeArrayStructure($paginatedStructure, $paginateData);
    }

    public function testModelCustomTransformerPagination()
    {
        User::setTransformer(new CustomUserTransformer());

        $paginatedStructure = [
            'data' => [
                '*' => [
                    'type',
                    'id',
                    'attributes' => ['name'],
                ]
            ],
            'meta' => ['total-pages', 'per-page', 'current-page', 'last-page', 'from', 'to'],
            'links' => ['self', 'first', 'next', 'prev', 'last'],
        ];

        $originalData = [
            [
                'name' => 'John Doe',
                'email' => 'john.doe@example.com',
            ],
            [
                'name' => 'Jane Roe',
                'email' => 'jane.roe@example.com',
            ]
        ];

        foreach ($originalData as $item) {
            User::create($item);
        }

        $paginateData = User::paginate()->toArray();

        $this->seeArrayStructure($paginatedStructure, $paginateData);
    }

    public function testModelCustomSerializerPagination()
    {
        User::setSerializer(new CustomSerializer());

        $paginatedStructure = [
            'data' => [
                '*' => [
                    'type',
                    'id',
                    'attributes_count',
                    'attributes' => ['name'],
                ]
            ],
            'meta' => ['total-pages', 'per-page', 'current-page', 'last-page', 'from', 'to'],
            'links' => ['self', 'first', 'next', 'prev', 'last'],
        ];

        $originalData = [
            [
                'name' => 'John Doe',
                'email' => 'john.doe@example.com',
            ],
            [
                'name' => 'Jane Roe',
                'email' => 'jane.roe@example.com',
            ]
        ];

        foreach ($originalData as $item) {
            User::create($item);
        }

        $paginateData = User::paginate()->toArray();

        $this->seeArrayStructure($paginatedStructure, $paginateData);
    }
}