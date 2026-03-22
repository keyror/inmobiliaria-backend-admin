<?php

namespace App\Repositories\Implements;

use App\Models\Image;
use App\Repositories\IImageRepository;

class ImageRepository implements IImageRepository
{
    public function create(array $data): Image
    {
        return Image::create($data);
    }

    public function find(string $id): ?Image
    {
        return Image::find($id);
    }

    public function delete(Image $image): void
    {
        $image->delete();
    }

    public function clearCover(string $imageableId, string $imageableType): void
    {
        Image::query()->where('imageable_id', $imageableId)
            ->where('imageable_type', $imageableType)
            ->update(['is_cover' => false]);
    }
}
