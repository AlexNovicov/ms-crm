<?php

namespace App\Exceptions;

use AmoCRM\Exceptions\AmoCRMoAuthApiException as AmoCRMoAuthApiExceptionBase;
use App\Models\Project;
use ProfilanceGroup\BackendSdk\Support\Response;

class AmoCRMoAuthApiException extends AmoCRMoAuthApiExceptionBase
{
    /** @var int */
    protected $project_id;

    /** @var string */
    protected $project_slug;

    /**
     * Установка идентификатора проекта.
     *
     * @param int $project_id
     * @return AmoCRMoAuthApiException
     */
    public function setProjectId($project_id)
    {
        $this->project_id = $project_id;

        return $this;
    }

    /**
     * Установка слага проекта.
     *
     * @param string $project_slug
     * @return AmoCRMoAuthApiException
     */
    public function setProjectSlug($project_slug)
    {
        $this->project_slug = $project_slug;

        return $this;
    }

    /**
     * Получение идентификатора проекта.
     *
     * @return int
     */
    public function getProjectId()
    {
        return $this->project_id;
    }

    /**
     * Получение слага проекта.
     *
     * @return string
     */
    public function getProjectSlug()
    {
        return $this->project_slug;
    }

    /**
     * Report the exception.
     *
     * @return bool|null
     */
    public function report()
    {
        try {

            if(empty($this->project_id) && empty($this->project_slug)) {
                return true;
            }

            /** @var Project $project */
            $project_repository = app(\App\Repositories\Project::class);

            if(!empty($this->project_id)) {
                $project = $project_repository->getCachedProjects()->where('id', $this->project_id)->first();
            } else {
                $project = $project_repository->getCachedProjects()->where('slug', $this->project_slug)->first();
            }

            if(empty($project)) {
                return true;
            }

            $project->status = Project::ERROR_STATUS;
            $project->save();

            alt_log()->file('error_crm')->error($this->getMessage(), ['project_id' => $project->id, 'project_name' => $project->name]);

        } catch (\Throwable $e) {
            alt_log()->file('error_crm')->exception($e);
        }

        return true;
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function render($request)
    {
        return response()->json(Response::error('Произошла ошибка на сервере. Повторите позже.', [], 500));
    }

}
