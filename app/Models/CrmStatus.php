<?php

namespace App\Models;

use ProfilanceGroup\BackendSdk\Abstracts\Model;

class CrmStatus extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'project_id',
        'name',
        'title',
        'crm_id',
    ];
}
