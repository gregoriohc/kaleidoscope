<?php

namespace Gregoriohc\Kaleidoscope;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator as BaseLengthAwarePaginator;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;

class LengthAwarePaginator extends BaseLengthAwarePaginator
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
                'total-pages'  => $this->total(),
                'per-page'     => $this->perPage(),
                'current-page' => $this->currentPage(),
                'last-page'    => $this->lastPage(),
                'from'         => $this->firstItem(),
                'to'           => $this->lastItem(),
            ],
            'links' => [
                'self'  => $this->selfPageUrl(),
                'first' => $this->firstPageUrl(),
                'next'  => $this->nextPageUrl(),
                'prev'  => $this->previousPageUrl(),
                'last'  => $this->lastPageUrl(),
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

    /**
     * Get the URL for the first page.
     *
     * @return string
     */
    public function firstPageUrl()
    {
        return $this->url(1);
    }

    /**
     * Get the URL for the last page.
     *
     * @return string
     */
    public function lastPageUrl()
    {
        return $this->url($this->lastPage());
    }
}
