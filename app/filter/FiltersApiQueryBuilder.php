<?php

namespace App\filter;
use Closure;
use Illuminate\Database\Eloquent\Builder;

class FiltersApiQueryBuilder
{

    public function allowedSorts(): Closure
    {
        return function () {
            /** @var Builder $this */
            if (request()->query('sortBy') && request()->query('sortType')) {
                $this->orderBy(
                    request()->query('sortBy','created_at'),
                    request()->query('sortType', 'desc')
                );
            }
            return $this;
        };
    }

    public function allowedFilters(): Closure
    {
        return function (array $allowedFilters) {
            /** @var Builder $this */
            if ($search = request()->query('search')) {
                $this->where(function ($query) use ($allowedFilters, $search) {
                    foreach ($allowedFilters as $fieldToFilter) {
                        $query->orWhere($fieldToFilter, 'LIKE', "%{$search}%");
                    }
                });
            }
            return $this;
        };
    }

    public function jsonPaginate(): Closure
    {
        return function () {
            /** @var Builder $this */

            return $this->paginate(
                $perPage = request()->query('perPage', 15),
                $columns = ['*'],
                $pageName = 'page',
                $page = request()->query('page', 1)
            );
        };
    }

}
