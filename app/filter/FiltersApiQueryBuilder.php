<?php

namespace App\filter;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Str;

class FiltersApiQueryBuilder
{

    public function allowedSorts(): Closure
    {
        return function () {
            /** @var Builder $this */

            $sortBy = request()->query('sortBy', 'created_at');
            $sortType = request()->query('sortType', 'desc');

            if (!$sortBy) return $this;

            if (str_contains($sortBy, '.')) {
                $parts = explode('.', $sortBy);
                $column = array_pop($parts);

                // Convertir snake_case a camelCase: document_type → documentType
                $relation = implode('.', array_map([Str::class, 'camel'], $parts));

                // Crear columna virtual y ordenar
                $virtualColumn = str_replace('.', '_', Str::snake($relation)) . '_' . $column;

                return $this->withAggregate($relation, $column)->orderBy($virtualColumn, $sortType);
            }

            // Campo directo
            return $this->orderBy($sortBy, $sortType);
        };
    }


    public function allowedFilters(): Closure
    {
        return function (array $allowedFilters) {
            /** @var Builder $this */
            if ($search = request()->query('search')) {
                $this->where(function ($query) use ($allowedFilters, $search) {
                    foreach ($allowedFilters as $field) {
                        // Detectar si es una relación (contiene punto)
                        if (str_contains($field, '.')) {
                            $parts = explode('.', $field);
                            $column = array_pop($parts); // Última parte es la columna
                            $relations = implode('.', $parts); // El resto es la relación

                            // Filtrar por relación
                            $query->orWhereHas($relations, function ($subQuery) use ($column, $search) {
                                $subQuery->where($column, 'LIKE', "%{$search}%");
                            });
                        } else {
                            // Filtrar por columna directa
                            $query->orWhere($field, 'LIKE', "%{$search}%");
                        }
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
