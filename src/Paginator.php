<?php

namespace Gregoriohc\Kaleidoscope;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator as BasePaginator;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;

class Paginator extends BasePaginator
{
    /**
     * @var \Illuminate\Database\Eloquent\Model|Fractalizable
     */
    protected $fractalizableModel;

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        $manager = new Manager();

        $manager->setSerializer($this->fractalizableModel->getSerializer());

        $resources = new Collection($this->items, $this->fractalizableModel->getTransformer(), $this->fractalizableModel->getResourceKey());

        return array_merge($manager->createData($resources)->toArray(), $this->getExtras());
    }

    /**
     * @param Model $model
     */
    public function setFractalizableModel(Model $model)
    {
        $this->fractalizableModel = $model;
    }

    /**
     * Get the pagination extra data.
     *
     * @return array
     */
    protected function getExtras()
    {
        return [
            'meta' => [
                'per-page'     => $this->perPage(),
                'current-page' => $this->currentPage(),
                'from'         => $this->firstItem(),
                'to'           => $this->lastItem(),
            ],
            'links' => [
                'self' => $this->selfPageUrl(),
                'next' => $this->nextPageUrl(),
                'prev' => $this->previousPageUrl(),
            ],
        ];
    }

    /**
     * Get the URL for the current page.
     *
     * @return string
     */
    public function selfPageUrl()
    {
        return $this->url($this->currentPage());
    }
}
