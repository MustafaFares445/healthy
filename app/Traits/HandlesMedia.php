<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

trait HandlesMedia
{
    public array $fileTypes = ['image', 'video', 'file', 'audio'];
    public array $multipleFileTypes = ['images', 'videos', 'files', 'audios'];

    /**
     * Handle media upload for single or multiple files
     *
     * @param Request $request The incoming request containing files
     * @param HasMedia $model The model to associate the media with
     * @param string|null $collection The media collection name (default: null)
     * @param string|null $name The media name (default: null)
     * @return void
     */
    public function handleMediaUpload(Request $request, HasMedia $model, ?string $collection = null , ?string $name = null): void
    {
        $this->processFiles(request: $request, model: $model, collection: $collection, isMultiple: false , name: $name);
        $this->processFiles(request: $request, model: $model, collection: $collection, isMultiple: true);

    }

    /**
     * Update media by clearing existing collection and uploading new files
     *
     * @param Request $request The incoming request containing files
     * @param HasMedia $model The model to associate the media with
     * @param string|null $collection The media collection name (default: 'default')
     * @return void
     */
    public function handleMediaUpdate(Request $request, HasMedia $model, ?string $collection = null): void
    {
        if ($request->hasAny(array_merge($this->fileTypes, $this->multipleFileTypes))) {
            $this->clearCollections($request, $model, $collection);
            $this->handleMediaUpload($request, $model, $collection);
        }
    }

    /**
     * Delete all media in a collection
     *
     * @param HasMedia $model The model containing the media collection
     * @param string $collection The media collection name (default: 'default')
     * @return void
     */
    public function handleMediaDeletion(HasMedia $model, string $collection = 'default'): void
    {
        $model->clearMediaCollection($collection);
    }

     /**
     * Delete a specific media item after verifying it belongs to the model
     *
     * @param HasMedia $model The model to verify ownership against
     * @param Media $media The media item to delete
     * @return bool Returns true if deletion was successful, false if media doesn't belong to model
     */
    public function handleMediaDelete(HasMedia $model, Media $media): bool
    {
        if (!$media->model->is($model))
            return false;

        return $media->delete();
    }

    /**
     * Process file uploads for single or multiple files
     *
     * @param Request $request The incoming request containing files
     * @param HasMedia $model The model to associate the media with
     * @param string|null $collection The media collection name (default: null)
     * @param bool $isMultiple Whether to process multiple files or single file
     * @param string|null $name
     * @return void
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    private function processFiles(Request $request, HasMedia $model, ?string $collection, bool $isMultiple , ?string $name = null): void
    {
        $types = $isMultiple ? $this->multipleFileTypes : $this->fileTypes;


        foreach ($types as $type) {
            if ($request->hasFile($type)) {
                $isFirstIndex = true;

                /** @var array<UploadedFile> $files . $files */
                $files = $isMultiple ? $request->file($type) : [$request->file($type)];

                foreach ($files as $file) {
                    /** @var Model $model */
                    $collectionName = $type . 's';

                    $model->addMedia($file)
                        ->usingFileName($name ?? $file->getFilename())
                        ->toMediaCollection($collectionName);

                    $isFirstIndex = false;
                }
            }
        }
    }

    /**
     * Clear media collections based on request files
     *
     * @param Request $request The incoming request containing files
     * @param HasMedia $model The model containing the media collections
     * @param string|null $collection The specific collection to clear (null clears all relevant collections)
     * @return void
     */
    private function clearCollections(Request $request, HasMedia $model, ?string $collection): void
    {
        if ($collection) {
            $model->clearMediaCollection($collection);
            return;
        }

        $types = array_merge($this->fileTypes, $this->multipleFileTypes);

        foreach ($types as $type) {
            if ($request->hasFile($type)) {
                /** @var Model $model */
                $collectionName = $model->getTable() . '-' . (in_array($type, $this->fileTypes) ? $type . 's' : $type);

                $model->clearMediaCollection($collectionName);
            }
        }
    }
}
