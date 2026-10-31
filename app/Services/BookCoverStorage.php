<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class BookCoverStorage
{
    public const DISK = 'public';

    public const DIRECTORY = 'covers';

    public const PLACEHOLDER = 'no_cover.jpg';

    /**
     * Store an uploaded cover under a server-generated name and return its path.
     */
    public function store(UploadedFile $file): string
    {
        return $file->store(self::DIRECTORY, self::DISK);
    }

    /**
     * Delete a stored cover. The placeholder is never deleted.
     */
    public function delete(?string $cover): void
    {
        if (blank($cover) || $cover === self::PLACEHOLDER) {
            return;
        }

        if ($this->isLegacy($cover)) {
            $path = public_path(self::DIRECTORY.'/'.basename($cover));

            if (is_file($path)) {
                unlink($path);
            }

            return;
        }

        Storage::disk(self::DISK)->delete($cover);
    }

    public function url(?string $cover): string
    {
        if (blank($cover) || $cover === self::PLACEHOLDER) {
            return asset(self::DIRECTORY.'/'.self::PLACEHOLDER);
        }

        if ($this->isLegacy($cover)) {
            return asset(self::DIRECTORY.'/'.$cover);
        }

        return Storage::disk(self::DISK)->url($cover);
    }

    /**
     * Covers uploaded before the move to the storage disk were saved as a bare
     * file name in public/covers; new ones are stored as "covers/<hash>.<ext>".
     */
    private function isLegacy(string $cover): bool
    {
        return ! str_contains($cover, '/');
    }
}
