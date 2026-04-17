<?php

namespace App\Services\Implements;

use App\Models\Image;
use App\Repositories\IImageRepository;
use App\Services\IImageService;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class ImageService implements IImageService
{
    public function __construct(
        private readonly IImageRepository $imageRepository
    ){}

    /**
     * @throws Throwable
     */
    public function upload($file): JsonResponse
    {
        DB::beginTransaction();
        try {
            $id = Str::uuid()->toString();

            $extension = $file->getClientOriginalExtension();
            $mime = $file->getMimeType();
            $size = $file->getSize();

            $fileName = $id . '.' . $extension;
            $path = $file->storeAs('images', $fileName, 'public');

            $image = $this->imageRepository->create([
                'id' => $id,
                'file_name' => $fileName,
                'file_path' => $path,
                'file_extension' => $extension,
                'mime_type' => $mime,
                'file_size' => $size,
                'sort_order' => 0,
                'is_cover' => false
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => ['Imagen cargada correctamente.'],
                'data' => [
                    'id' => $image->id,
                    'url' => $image->url,
                ]
            ]);

        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * @throws Throwable
     */
    public function delete(string $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $image = $this->imageRepository->find($id);

            if ($image) {
                Storage::disk($image->disk)->delete($image->file_path);
                $this->imageRepository->delete($image);
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => ['Imagen eliminada correctamente.']
            ]);

        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * @throws Throwable
     */
    public function setCover(string $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $image = $this->imageRepository->find($id);

            if (!$image) {
                throw new Exception('Imagen no encontrada');
            }

            if ($image->imageable_id && $image->imageable_type) {
                $this->imageRepository->clearCover(
                    $image->imageable_id,
                    $image->imageable_type
                );
            }

            $image->update(['is_cover' => true]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => ['Imagen marcada como portada correctamente.']
            ]);

        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function syncImages(
        Model $model,
        array $images
    ): void {

        $imageableId = $model->id;
        $imageableType = get_class($model);

        if (empty($images)) return;

        //  limpiar portadas
        $this->imageRepository->clearCover($imageableId, $imageableType);

        $imagesCollection = collect($images);

        //  eliminar las que ya no vienen
        Image::query()->where('imageable_id', $imageableId)
            ->where('imageable_type', $imageableType)
            ->whereNotIn('id', $imagesCollection->pluck('id'))
            ->delete();

        //  cargar en una sola query
        $dbImages = Image::query()->whereIn('id', $imagesCollection->pluck('id'))->get();

        foreach ($dbImages as $image) {

            $imgData = $imagesCollection->firstWhere('id', $image->id);

            if (!$imgData) continue;

            $image->update([
                'imageable_id' => $imageableId,
                'imageable_type' => $imageableType,
                'is_cover' => $imgData['is_cover'] ?? false,
                'sort_order' => $imgData['sort_order'] ?? 0
            ]);
        }

        // asegurar portada
        if (!$imagesCollection->contains('is_cover', true)) {
            $first = $dbImages->first();
            if ($first) {
                $first->update(['is_cover' => true]);
            }
        }
    }
}
