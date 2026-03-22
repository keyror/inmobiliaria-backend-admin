<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImageUploadRequest;
use App\Services\IImageService;

class ImageController extends Controller
{
    public function __construct(
        private readonly IImageService $imageService
    ) {}

    public function upload(ImageUploadRequest $request)
    {
        return $this->imageService->upload($request->file('image'));
    }

    public function delete(string $id)
    {
        return $this->imageService->delete($id);
    }

    public function setCover(string $id)
    {
        return $this->imageService->setCover($id);
    }
}
