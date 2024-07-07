<?php

namespace App\Repositories;

use App\Models\Mediafile;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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
        $attributes = [
            'model_type' => get_class($model),
            'model_id' => $model->getKey(),
            'path' => Storage::putFile($dir, $file, 'public'),
            'sequence' => $sequence,
            'note' => $note,
        ];

        return self::create($attributes);
    }

    public function replaceMedia(Mediafile $mediafile, $dir, \Illuminate\Http\File|\Illuminate\Http\UploadedFile $file, $sequence = 0, $note = NULL): Model
    {
        $attributes = [
            'path' => Storage::putFile($dir, $file, 'public'),
            'sequence' => $sequence,
            'note' => $note,
        ];

        return parent::update($mediafile, $attributes);
    }

    public function replaceMediaWithDelete(Mediafile $mediafile, $dir, \Illuminate\Http\File|\Illuminate\Http\UploadedFile $file, $sequence = 0, $note = NULL): Model
    {
        Storage::delete($mediafile->getRawOriginal('path'));
        $attributes = [
            'path' => Storage::putFile($dir, $file, 'public'),
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
