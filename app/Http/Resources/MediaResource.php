<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\MediaLibrary\MediaCollections\Exceptions\InvalidConversion;

/**
 * @OA\Schema(
 *     schema="MediaResource",
 *     title="MediaResource",
 *     description="Media resource representation for files and images",
 *     required={"id", "name", "fileName", "collection", "url", "size", "type", "extension", "caption"},
 *
 *     @OA\Property(property="id", type="integer", format="int64", description="Unique identifier for the media", example=1),
 *     @OA\Property(property="name", type="string", description="Name of the media file without extension", example="nature_photo"),
 *     @OA\Property(property="fileName", type="string", description="Complete file name with extension", example="nature_photo.jpg"),
 *     @OA\Property(property="collection", type="string", description="Collection name where the media is stored", example="photos"),
 *     @OA\Property(property="url", type="string", format="uri", description="Full URL to watermarked WebP version", example="https://example.com/storage/media/conversions/nature_photo-watermarked-webp.webp"),
 *     @OA\Property(property="originalUrl", type="string", format="uri", description="Full URL to original file", example="https://example.com/storage/media/nature_photo.jpg"),
 *     @OA\Property(property="size", type="string", description="Human-readable file size", example="2.5 MB"),
 *     @OA\Property(property="type", type="string", description="Type of media", enum={"image", "video", "document", "audio", "other"}, example="image"),
 *     @OA\Property(property="extension", type="string", description="File extension", example="jpg"),
 *     @OA\Property(property="caption", type="string", description="Caption or description of the media", example="Beautiful nature photograph"),
 *     @OA\Property(property="width", type="integer", format="int32", description="Width in pixels (images only)", example=1920, nullable=true),
 *     @OA\Property(property="height", type="integer", format="int32", description="Height in pixels (images only)", example=1080, nullable=true),
 *     @OA\Property(property="thumbnailUrl", type="string", format="uri", description="URL of thumbnail (watermarked WebP)", example="https://example.com/storage/media/conversions/nature_photo-thumb.webp", nullable=true)
 * )
 */
final class MediaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [];
        try {
            $data = [
                'id' => $this->id,
                'name' => $this->name,
                'fileName' => $this->file_name,
                'collection' => $this->collection_name,
                'url' => $this->getWatermarkedWebpUrl(),
                'originalUrl' => $this->getFullUrl(),
                'size' => $this->human_readable_size,
                'extension' => $this->extension,
                'type' => $this->getTypeFromExtension(),
                'caption' => $this->getCustomProperty('caption') ?? $this->name,
            ];

            // Add image dimensions if it's an image
            if ($this->getTypeFromExtension() === 'image') {
                $data['width'] = $this->getCustomProperty('width');
                $data['height'] = $this->getCustomProperty('height');
            }
        }catch (\Exception $exception){}

        return $data;
    }

    /**
     * Get watermarked WebP URL with fallback to original
     */
    private function getWatermarkedWebpUrl(): string
    {
        try {
            return $this->getFullUrl('watermark');
        } catch (InvalidConversion $e) {
            // If conversion doesn't exist, fallback to original
            return $this->getFullUrl();
        }
    }

    /**
     * Determine media type from extension
     */
    private function getTypeFromExtension(): string
    {
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg'];
        $videoExtensions = ['mp4', 'avi', 'mov', 'wmv', 'flv', 'webm'];
        $audioExtensions = ['mp3', 'wav', 'flac', 'aac', 'ogg'];
        $documentExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt'];

        $extension = strtolower($this->extension);

        if (in_array($extension, $imageExtensions)) {
            return 'image';
        } elseif (in_array($extension, $videoExtensions)) {
            return 'video';
        } elseif (in_array($extension, $audioExtensions)) {
            return 'audio';
        } elseif (in_array($extension, $documentExtensions)) {
            return 'document';
        }

        return 'other';
    }
}
