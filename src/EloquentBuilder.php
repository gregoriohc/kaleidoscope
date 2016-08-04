<?php

namespace Gregoriohc\Kaleidoscope;

use Illuminate\Database\Eloquent\Builder as BaseBuilder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class EloquentBuilder extends BaseBuilder
{
    /**
     * @var \Illuminate\Database\Eloquent\Model|Fractalizable $fractalizableModel
     */
    protected $fractalizableModel;

    /**
     * Paginate the given query.
     *
     * @param  int $perPage
     * @param  array $columns
     * @param  string $pageName
     * @param  int|null $page
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     *
     * @throws \InvalidArgumentException
     */
    public function paginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null)
    {
        if (method_exists($this, 'toBase')) { // Laravel Framework 5.2.* or 5.3.*
            $page = $page ?: Paginator::resolveCurrentPage($pageName);

            $perPage = $perPage ?: $this->model->getPerPage();

            $query = $this->toBase();

            $total = $query->getCountForPagination();

            $results = $total ? $this->forPage($page, $perPage)->get($columns) : new Collection;
        } else { // Laravel Framework 5.1.*
            $total = $this->query->getCountForPagination();

            $this->query->forPage(
                $page = $page ?: Paginator::resolveCurrentPage($pageName),
                $perPage = $perPage ?: $this->model->getPerPage()
            );

            $results = $this->get($columns);
        }

        $paginator = new LengthAwarePaginator($results, $total, $perPage, $page, [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => $pageName,
        ]);

        $paginator->setFractalizableModel($this->fractalizableModel);

        $paginator->setPath($this->fractalizableModel->getPaginatorLinksBasePath() . $this->fractalizableModel->getResourceKey());

        return $paginator;
    }

    /**
     * Paginate the given query into a simple paginator.
     *
     * @param  int $perPage
     * @param  array $columns
     * @param  string $pageName
     * @param  int|null $page
     * @return \Illuminate\Contracts\Pagination\Paginator
     */
    public function simplePaginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null)
    {
        $page = $page ?: Paginator::resolveCurrentPage($pageName);

        $perPage = $perPage ?: $this->model->getPerPage();

        $this->skip(($page - 1) * $perPage)->take($perPage + 1);

        $paginator = new Paginator($this->get($columns), $perPage, $page, [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => $pageName,
        ]);

        $paginator->setFractalizableModel($this->fractalizableModel);

        $paginator->setPath($this->fractalizableModel->getPaginatorLinksBasePath() . $this->fractalizableModel->getResourceKey());

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