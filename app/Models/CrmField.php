<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use ProfilanceGroup\BackendSdk\Abstracts\Model;
use function PHPUnit\Framework\matches;

class CrmField extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'project_id',
        'name',
        'title',
        'crm_id',
        'crm_entity',
        'crm_type',
        'crm_enum',
        'entity',
        'entity_field',
        'type',
        'type_format',
    ];

    protected $appends = [
        'crm_entity_string',
    ];

    /**
     * Тип поля.
     * @var integer
     */
    const DEFAULT_TYPE = 1;
    const DATE_TYPE = 2;

    /**
     * CRM сущность.
     * @var integer
     */
    const LEAD_ENTITY = 1;
    const CONTACT_ENTITY = 2;

    /**
     * Сущность.
     * @var integer
     */
    const ORDER_ENTITY = 1;
    const USER_ENTITY = 2;

    /**
     * Получение строкового представления типа сущности.
     *
     * @return string
     */
    public function getCrmEntityStringAttribute()
    {
        return match($this->crm_entity) {
            self::LEAD_ENTITY => 'lead',
            self::CONTACT_ENTITY => 'contact',
            default => 'undefined'
        };
    }

}
