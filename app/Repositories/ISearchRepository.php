<?php

namespace App\Repositories;

interface ISearchRepository
{
    public function search(string $term, int $limit = 5): array;
}
