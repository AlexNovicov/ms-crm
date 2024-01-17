<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;
use ProfilanceGroup\BackendSdk\Abstracts\Repository;

class Project extends Repository
{
    /** @var string  */
    const PROJECTS_CACHE_KEY = 'projects_list';

    protected $model = \App\Models\Project::class;

    /**
     * Получение активных проектов для обновления токенов.
     *
     * @param array $project_ids
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getActiveProjectsForUpdateTokens(array $project_ids = [])
    {
        $builder = $this->query()
            ->whereNotNull('crm_refresh_token');

        if(!empty($project_ids)) {
            $builder->whereIn('id', $project_ids);
        } else {
            $builder->where('status', \App\Models\Project::ACTIVE_STATUS);
        }

        return $builder->get();
    }

    /**
     * Получение закешированного списка проектов.
     *
     * @return Collection
     */
    public function getCachedProjects()
    {
        return \Cache::remember(self::PROJECTS_CACHE_KEY, 60*60*24, function () {
            return $this->query()->get();
        });
    }


    /**
     * Очистка кеша проектов
     *
     * @return void
     */
    public function clearProjectsCache()
    {
        \Cache::forget(self::PROJECTS_CACHE_KEY);
    }
}
