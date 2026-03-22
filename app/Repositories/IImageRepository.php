<?php

namespace App\Repositories;

use App\Models\Image;

interface IImageRepository
{
    public function create(array $data): Image;
    public function find(string $id): ?Image;
    public function delete(Image $image): void;
    public function clearCover(string $imageableId, string $imageableType): void;
}
