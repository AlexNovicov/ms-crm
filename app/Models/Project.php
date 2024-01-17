<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use ProfilanceGroup\BackendSdk\Abstracts\Model;

class Project extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'crm_subdomain',
        'crm_default_pipeline',
        'crm_default_responsible_user_id',
        'crm_client_id',
        'crm_secret',
        'crm_access_token',
        'crm_refresh_token',
        'status',
    ];

    protected $casts = [
        'crm_subdomain' => 'encrypted',
        'crm_client_id' => 'encrypted',
        'crm_secret' => 'encrypted',
        'crm_access_token' => 'encrypted',
        'crm_refresh_token' => 'encrypted',
    ];

    /**
     * Статус интеграции.
     * @var int
     */
    const ACTIVE_STATUS = 1;
    const INACTIVE_STATUS = 2;
    const ERROR_STATUS = 3;

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::created(function () {
            app(\App\Repositories\Project::class)->clearProjectsCache();
        });

        static::updated(function () {
            app(\App\Repositories\Project::class)->clearProjectsCache();
        });

        static::deleted(function () {
            app(\App\Repositories\Project::class)->clearProjectsCache();
        });
    }

    /**
     * Поля crm.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function crm_fields()
    {
        return $this->hasMany(CrmField::class);
    }

    /**
     * Статусы crm.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function crm_statuses()
    {
        return $this->hasMany(CrmStatus::class);
    }

    /**
     * Проект активен.
     *
     * @return bool
     */
    public function isActive()
    {
        return $this->status == self::ACTIVE_STATUS;
    }

    /**
     * Проект не активен.
     *
     * @return bool
     */
    public function isNotActive()
    {
        return $this->status != self::ACTIVE_STATUS;
    }

}
