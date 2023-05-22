<?php

namespace Botble\Media\Models;

use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use RvMedia;

class MediaFolder extends BaseModel
{
    use SoftDeletes;

    protected $table = 'media_folders';

    protected $fillable = [
        'name',
        'slug',
        'parent_id',
        'user_id',
    ];

    public function files(): HasMany
    {
        return $this->hasMany(MediaFile::class, 'folder_id', 'id');
    }

    protected static function boot()
    {
        parent::boot();
        static::deleting(function (MediaFolder $folder) {
            if ($folder->isForceDeleting()) {
                $files = MediaFile::where('folder_id', $folder->getKey())->onlyTrashed()->get();

                foreach ($files as $file) {
                    RvMedia::deleteFile($file);
                    $file->forceDelete();
                }
            } else {
                $files = MediaFile::where('folder_id', $folder->getKey())->withTrashed()->get();

                foreach ($files as $file) {
                    $file->delete();
                }
            }
        });

        static::restoring(function (MediaFolder $folder) {
            MediaFile::where('folder_id', $folder->getKey())->restore();
        });
    }
}
