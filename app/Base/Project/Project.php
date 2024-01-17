<?php

namespace App\Base\Project;

use ProfilanceGroup\BackendSdk\Exceptions\OperationError;
use ProfilanceGroup\BackendSdk\Support\Response;
use \App\Models\Project as ProjectModel;

class Project
{
    /**
     * Получение информации о проекте.
     *
     * @param string $project_slug
     * @return array
     * @throws OperationError
     */
    public function getProject($project_slug)
    {
        /**
         * Получение проекта.
         * @var ProjectModel $project
         */
        $project = app(\App\Repositories\Project::class)->getCachedProjects()->where('slug', $project_slug)->first();

        if(empty($project)) {
            throw new OperationError('Проект не найден.');
        }

        return Response::success(null, [
            'project' => array_merge($project->only([
                'name',
                'slug',
                'crm_subdomain',
                'crm_default_pipeline',
                'crm_default_responsible_user_id',
                'status',
            ]), [
                'fields' => $project->crm_fields->toArray(),
                'statuses' => $project->crm_statuses->toArray(),
            ])
        ]);
    }
}
