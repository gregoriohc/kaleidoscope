<?php

namespace Gregoriohc\Kaleidoscope;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as BaseBuilder;

class QueryBuilder extends BaseBuilder
{
    /**
     * @var \Illuminate\Database\Eloquent\Model|Fractalizable
     */
    protected $fractalizableModel;

    /**
     * Paginate the given query into a simple paginator.
     *
     * @param int      $perPage
     * @param array    $columns
     * @param string   $pageName
     * @param int|null $page
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate($perPage = 15, $columns = ['*'], $pageName = 'page[number]', $page = null)
    {
        $page = $page ?: Paginator::resolveCurrentPage($pageName);

        $total = $this->getCountForPagination($columns);

        $results = $total ? $this->forPage($page, $perPage)->get($columns) : [];

        $paginator = new LengthAwarePaginator($results, $total, $perPage, $page, [
            'path'     => Paginator::resolveCurrentPath(),
            'pageName' => $pageName,
        ]);

        $paginator->setFractalizableModel($this->fractalizableModel);

        $paginator->setPath($this->fractalizableModel->getPaginatorLinksBasePath().$this->fractalizableModel->getResourceKey());

        return $paginator;
    }

    /**
     * Get a paginator only supporting simple next and previous links.
     *
     * This is more efficient on larger data-sets, etc.
     *
     * @param int      $perPage
     * @param array    $columns
     * @param string   $pageName
     * @param int|null $page
     *
     * @return \Illuminate\Contracts\Pagination\Paginator
     */
    public function simplePaginate($perPage = 15, $columns = ['*'], $pageName = 'page[number]', $page = null)
    {
        $page = $page ?: Paginator::resolveCurrentPage($pageName);

        $this->skip(($page - 1) * $perPage)->take($perPage + 1);

        $paginator = new Paginator($this->get($columns), $perPage, $page, [
            'path'     => Paginator::resolveCurrentPath(),
            'pageName' => $pageName,
        ]);

        $paginator->setFractalizableModel($this->fractalizableModel);

        $paginator->setPath($this->fractalizableModel->getPaginatorLinksBasePath().$this->fractalizableModel->getResourceKey());

        return $paginator;
    }

    /**
     * @param Model $model
     */
    public function setFractalizableModel(Model $model)
    {
        $this->fractalizableModel = $model;
    }
}
