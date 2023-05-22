<?php

namespace Botble\ACL\Models;

use Botble\ACL\Traits\PermissionTrait;
use Botble\Base\Casts\SafeContent;
use Botble\Base\Models\BaseModel;
use Exception;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends BaseModel
{
    use PermissionTrait;

    protected $table = 'roles';

    protected $fillable = [
        'name',
        'slug',
        'permissions',
        'description',
        'is_default',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'permissions' => 'json',
        'name' => SafeContent::class,
        'description' => SafeContent::class,
    ];

    protected function permissions(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                try {
                    return json_decode($value ?: '', true) ?: [];
                } catch (Exception) {
                    return [];
                }
            },
            set: fn ($value) => $value ? json_encode($value) : ''
        );
    }

    public function delete(): ?bool
    {
        if ($this->exists) {
            $this->users()->detach();
        }

        return parent::delete();
    }

    public function users(): BelongsToMany
    {
        return $this
            ->belongsToMany(User::class, 'role_users', 'role_id', 'user_id')
            ->withTimestamps();
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by')->withDefault();
    }
}
