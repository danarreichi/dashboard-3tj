<?php

namespace App\Repositories;

use App\Models\Mediafile;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use Intervention\Image\ImageManager;

class MediafileRepository extends BaseRepository
{

    public function __construct(Mediafile $model)
    {
        $this->model = $model;
    }

    public function listByModel(Model $model)
    {
        $query = parent::index([], [])
            ->where('model_type', get_class($model))
            ->where('model_id', $model->getKey());

        return $query->get();
    }

    public function upsertByModel(Model $model, $dir, \Illuminate\Http\File|\Illuminate\Http\UploadedFile $file, $sequence = 0, $note = NULL): Model
    {
        $checkFile = $this->model->where([
            'model_type' => get_class($model),
            'model_id' => $model->getKey(),
            'note' => $note,
        ])->first();

        if ($checkFile) return $this->replaceMedia($checkFile, $dir, $file, $sequence, $note);

        return $this->createByModel($model, $dir, $file, $sequence, $note);
    }

    public function createByModel(Model $model, $dir, \Illuminate\Http\File|\Illuminate\Http\UploadedFile $file, $sequence = 0, $note = NULL): Model
    {
        $manager = ImageManager::gd();
        // Create an image instance
        $img = $manager->read($file->getRealPath());

        // Check the width of the image
        if ($img->width() > 300) $img->scaleDown(width: 300);

        // Create a temporary file path to save the processed image
        $tempFilePath = tempnam(sys_get_temp_dir(), 'compressed_image');
        $img->save($tempFilePath, 75); // Save with 75% quality

        // Use the processed image to store the file
        $path = Storage::putFile($dir, new File($tempFilePath), 'public');

        // Delete the temporary file
        unlink($tempFilePath);

        // Prepare attributes for the new model
        $attributes = [
            'model_type' => get_class($model),
            'model_id' => $model->getKey(),
            'path' => $path,
            'sequence' => $sequence,
            'note' => $note,
        ];

        return self::create($attributes);
    }

    public function replaceMedia(Mediafile $mediafile, $dir, \Illuminate\Http\File|\Illuminate\Http\UploadedFile $file, $sequence = 0, $note = NULL): Model
    {
        $manager = ImageManager::gd();
        // Create an image instance
        $img = $manager->read($file->getRealPath());

        // Check the width of the image
        if ($img->width() > 300) $img->scaleDown(width: 300);

        // Create a temporary file path to save the processed image
        $tempFilePath = tempnam(sys_get_temp_dir(), 'compressed_image');
        $img->save($tempFilePath, 75); // Save with 75% quality

        // Use the processed image to store the file
        $path = Storage::putFile($dir, new File($tempFilePath), 'public');

        // Delete the temporary file
        unlink($tempFilePath);
        $attributes = [
            'path' => $path,
            'sequence' => $sequence,
            'note' => $note,
        ];

        return parent::update($mediafile, $attributes);
    }

    public function replaceMediaWithDelete(Mediafile $mediafile, $dir, \Illuminate\Http\File|\Illuminate\Http\UploadedFile $file, $sequence = 0, $note = NULL): Model
    {
        $manager = ImageManager::gd();
        // Create an image instance
        $img = $manager->read($file->getRealPath());

        // Check the width of the image
        if ($img->width() > 300) $img->scaleDown(width: 300);

        // Create a temporary file path to save the processed image
        $tempFilePath = tempnam(sys_get_temp_dir(), 'compressed_image');
        $img->save($tempFilePath, 75); // Save with 75% quality

        // Use the processed image to store the file
        $path = Storage::putFile($dir, new File($tempFilePath), 'public');

        // Delete the temporary file
        unlink($tempFilePath);

        Storage::delete($mediafile->getRawOriginal('path'));
        $attributes = [
            'path' => $path,
            'sequence' => $sequence,
            'note' => $note,
        ];

        return parent::update($mediafile, $attributes);
    }

    public function destroy(Model|Authenticatable $model)
    {
        Storage::delete($model->getRawOriginal('path'));
        return parent::destroy($model);
    }
}
