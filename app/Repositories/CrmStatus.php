<?php

namespace App\Repositories;

use ProfilanceGroup\BackendSdk\Abstracts\Repository;

class CrmStatus extends Repository
{
    protected $model = \App\Models\CrmStatus::class;

    /**
     * Получение CRM id по названию статуса.
     *
     * @param string $name
     * @return \Illuminate\Database\Eloquent\HigherOrderBuilderProxy|mixed|null
     */
    public function getCrmIdByName($name)
    {
        return $this->query()
            ->where(compact('name'))
            ->first()
            ->crm_id ?? null;
    }

}
