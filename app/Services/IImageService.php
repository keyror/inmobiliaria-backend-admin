<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;

interface IImageService
{
    public function upload($file): JsonResponse;
    public function delete(string $id): JsonResponse;
    public function setCover(string $id): JsonResponse;

    public function syncImages(
        Model $model,
        array $images
    ): void;
}
