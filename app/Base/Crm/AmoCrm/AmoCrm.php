<?php

namespace App\Base\Crm\AmoCrm;

use AmoCRM\Collections\ContactsCollection;
use AmoCRM\Collections\CustomFieldsValuesCollection;
use AmoCRM\Collections\Leads\LeadsCollection;
use AmoCRM\Collections\NotesCollection;
use AmoCRM\Collections\TagsCollection;
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Exceptions\BadTypeException;
use AmoCRM\Filters\BaseRangeFilter;
use AmoCRM\Filters\ContactsFilter;
use AmoCRM\Filters\LeadsFilter;
use AmoCRM\Helpers\EntityTypesInterface;
use AmoCRM\Models\ContactModel;
use AmoCRM\Models\CustomFields\CustomFieldModel;
use AmoCRM\Models\CustomFieldsValues\BaseCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\Factories\CustomFieldValueCollectionFactory;
use AmoCRM\Models\CustomFieldsValues\Factories\CustomFieldValuesModelFactory;
use AmoCRM\Models\CustomFieldsValues\MultitextCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\MultitextCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\NullCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueModels\MultitextCustomFieldValueModel;
use AmoCRM\Models\LeadModel;
use AmoCRM\Models\NoteModel;
use AmoCRM\Models\NoteType\BillPaidNote;
use AmoCRM\Models\NoteType\CommonNote;
use AmoCRM\Models\TagModel;
use App\Contracts\Crm;
use App\Exceptions\AmoCRMoAuthApiException;
use App\Models\CrmField;
use App\Models\Project;
use App\Repositories\Project as ProjectRepository;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Token\AccessTokenInterface;
use ProfilanceGroup\BackendSdk\Exceptions\OperationError;
use ProfilanceGroup\BackendSdk\Helpers\Site;
use ProfilanceGroup\BackendSdk\Support\Response;

class AmoCrm implements Crm
{
    /**
     * Список базовых полей.
     */
    const BASE_FIELDS = [
        'id',
        'status',
        'updated_at',
        'closed_at',
    ];

    /** @var int  */
    const DEFAULT_LIMIT = 250;

    /**
     * @param ProjectRepository $project_repository
     */
    public function __construct(private readonly ProjectRepository $project_repository)
    {
    }

    //****************************************************************
    //************************ Авторизация ***************************
    //****************************************************************

    /**
     * Получение и сохранение токена авторизации.
     *
     * @param array $data
     * @return void
     */
    public function getToken(array $data)
    {
        /**
         * Проверка обязательных параметров.
         */
        if(!\Arr::has($data, ['code', 'state', 'referer', 'client_id'])) {
            alt_log()->file('error_crm')->error('Отсутствуют обязательные параметры в запросе на получения токена авторизации.', [$data]);
            return;
        }

        /**
         * Получение и проверка проекта.
         * @var Project $project
         */
        $project = $this->project_repository->find($data['state']);

        if(empty($project)) {
            alt_log()->file('error_crm')->error('Проект не найден.', $data);
            return;
        }

        if("{$project->crm_subdomain}.amocrm.ru" != $data['referer']) {
            alt_log()->file('error_crm')->error('Referer не соответствует проекту.', $data);
            return;
        }

        if($project->crm_client_id != $data['client_id']) {
            alt_log()->file('error_crm')->error('Client_id не соответствует проекту.', $data);
            return;
        }

        /**
         * Получение токенов авторизации.
         */
        try {

            $amo_crm_client = $this->getCrmClient($project);

            $access_token = $amo_crm_client->getOAuthClient()->getAccessTokenByCode($data['code']);

            if (!$access_token->hasExpired()) {
                $project->crm_access_token = $access_token->getToken();
                $project->crm_access_token_expires = $access_token->getExpires();
                $project->crm_refresh_token = $access_token->getRefreshToken();
                $project->status = Project::ACTIVE_STATUS;
                $project->save();
            }

        } catch (\Throwable $e) {
            alt_log()->file('error_crm')->exception($e, json_encode($data, JSON_PRETTY_PRINT));
        }
    }

    /**
     * Обновление токенов авторизации проектов.
     *
     * @param array $project_ids
     * @return void
     */
    public function updateTokens(array $project_ids = [])
    {
        /**
         * Получение активных проектов для обновления токена.
         */
        $projects = $this->project_repository->getActiveProjectsForUpdateTokens($project_ids);

        if($projects->isEmpty()) {
            return;
        }

        /** @var Project $project */
        foreach ($projects as $project) {
            try {

                $amo_crm_client = $this->getCrmClient($project);

                $access_token = $amo_crm_client->getOAuthClient()->getAccessTokenByRefreshToken(
                    new AccessToken([
                        'access_token' => $project->crm_access_token,
                        'refresh_token' => $project->crm_refresh_token
                    ])
                );

                $this->setProjectToken($project, $access_token);

            } catch (\Throwable $e) {

                alt_log()->file('error_crm')->exception($e, "Ошибка обновления токена проекта {$project->name}.");

                $project->status = Project::ERROR_STATUS;
                $project->save();

            }
        }
    }

    //****************************************************************
    //************************** Сделки ******************************
    //****************************************************************

    /**
     * Создание сделки в CRM.
     *
     * @param array $data
     * @return array
     * @throws AmoCRMoAuthApiException
     * @throws OperationError
     * @throws AmoCRMApiException
     * @throws BadTypeException
     */
    public function createLead(array $data)
    {
        /**
         * Получение проекта.
         */
        $project = $this->getProject($data['project_slug']);

        $amo_crm_client = $this->getCrmClient($project);

        /**
         * Поиск или создание контакта.
         * @var ContactModel $contact
         */
        $contact = $this->createContact($data)['contact'];

        /**
         * Создание сделки.
         */
        $lead = new LeadModel();
        $lead->setName($data['crm_name'] ?? 'Заявка с сайта')
            ->setPipelineId($data['pipeline'] ?? $project->crm_default_pipeline)
            ->setContacts((new ContactsCollection())->add($contact))
            ->setCustomFieldsValues($this->getProjectCustomFieldsValues($project, $data, CrmField::LEAD_ENTITY))
            ->setResponsibleUserId($data['responsible_user_id'] ?? $project->crm_default_responsible_user_id ?? null)
            ->setPrice($data['price'] ?? null);

        $tags = (new TagsCollection())->add((new TagModel())->setName('Сайт'));

        if(!empty($data['tags'])) {
            foreach ($data['tags'] as $tag) {
                if(is_numeric($tag)) {
                    $tags->add((new TagModel())->setId($tag));
                } else {
                    $tags->add((new TagModel())->setName($tag));
                }
            }
        }

        $lead->setTags($tags);

        if (!empty($data['status'])) {

            $status_id = $this->getLeadStatusId($data['status']);

            if (!empty($status_id)) {
                $lead->setStatusId($status_id);
            }

        }

        /** @var LeadModel $lead */
        $lead = Request::handleRequest($data, function () use ($lead, $amo_crm_client) {
            return $amo_crm_client->leads()->addOne($lead);
        });

        return Response::success(null, ['lead' => $lead?->toArray()]);
    }

    /**
     * Получение сделок.
     *
     * @param array $data
     * @return array
     */
    public function getLeads(array $data)
    {
        if(empty($data['filter']['ids'])) {
            return Response::success(null, ['leads' => []]);
        }

        /**
         * Получение проекта.
         */
        $project = $this->getProject($data['project_slug']);
        $amo_crm_client = $this->getCrmClient($project);

        /**
         * Получение сделок.
         */
        $filter = new LeadsFilter();
        $filter = $this->setListFilters($data, $filter);

        /** @var LeadsCollection $leads */
        $leads = Request::handleRequest($data, function () use ($data, $filter, $amo_crm_client) {
            return $amo_crm_client->leads()->get($filter, $data['with'] ?? []);
        });

        /**
         * Примешивание названий полей из базы.
         */
        $leads = $this->addCustomFieldSystemNames($leads?->toArray(), $project, CrmField::LEAD_ENTITY);

        return Response::success(null, ['leads' => $leads]);
    }

    /**
     * Получение сделки.
     *
     * @param array $data
     * @return array
     */
    public function getLead(array $data)
    {
        /**
         * Получение проекта.
         */
        $project = $this->getProject($data['project_slug']);
        $amo_crm_client = $this->getCrmClient($project);

        /**
         * Получение сделки.
         * @var LeadModel $lead
         */
        $lead = Request::handleRequest($data, function () use ($data, $amo_crm_client) {
            return $amo_crm_client->leads()->getOne($data['lead_id'], $data['with'] ?? []);
        });

        /**
         * Примешивание названий полей из базы.
         */
        $lead = $this->addCustomFieldSystemNames([$lead?->toArray()], $project, CrmField::LEAD_ENTITY)[0] ?? null;

        return Response::success(null, ['lead' => $lead]);
    }

    /**
     * Обновление сделки.
     *
     * @param array $data
     * @return array
     */
    public function updateLead(array $data)
    {
        /**
         * Получение проекта.
         */
        $project = $this->getProject($data['project_slug']);

        $amo_crm_client = $this->getCrmClient($project);

        $lead = (new LeadModel())
            ->setId((int)$data['lead_id'])
            ->setCustomFieldsValues(null);

        /**
         * Обновление кастомных полей сделки.
         */
        if(!empty(array_diff_key($data, array_merge(['lead_id' => true, 'project_slug' => true], array_flip(self::BASE_FIELDS))))) {

            $custom_fields = $this->getProjectCustomFieldsValues($project, $data, CrmField::LEAD_ENTITY);

            if(!empty($custom_fields) && !$custom_fields->isEmpty()) {
                $lead->setCustomFieldsValues($custom_fields);
            }

        }

        /**
         * Обновление статуса.
         */
        if (!empty($data['status'])) {

            $status_id = $this->getLeadStatusId($data['status']);

            if (!empty($status_id)) {
                $lead->setStatusId($status_id);
            }

        }

        /**
         * Обновление ответственного за сделку.
         */
        if(!empty($data['responsible_user_id'])) {
            $lead->setResponsibleUserId($data['responsible_user_id']);
        }

        /**
         * Обновление суммы.
         */
        if(isset($data['price'])) {
            $lead->setPrice($data['price']);
        }

        /**
         * Обновление сделки.
         */
        Request::handleRequest($data, function () use ($lead, $amo_crm_client) {
            $amo_crm_client->leads()->updateOne($lead);
        });

        return Response::success();
    }

    /**
     * Обновление сделок.
     *
     * @param array $data
     * @return array
     */
    public function updateLeads(array $data)
    {
        /**
         * Получение проекта.
         */
        $project = $this->getProject($data['project_slug']);

        $amo_crm_client = $this->getCrmClient($project);

        $leads_collection = new LeadsCollection();

        foreach ($data['leads'] as $lead_data) {

            $lead = (new LeadModel())
                ->setId((int)$lead_data['id'])
                ->setCustomFieldsValues(null);

            /**
             * Обновление кастомных полей сделки.
             */
            if(!empty(array_diff_key($lead_data, array_flip(self::BASE_FIELDS)))) {

                $custom_fields = $this->getProjectCustomFieldsValues($project, $lead_data, CrmField::LEAD_ENTITY);

                if(!empty($custom_fields) && !$custom_fields->isEmpty()) {
                    $lead->setCustomFieldsValues($custom_fields);
                }

            }

            /**
             * Обновление статуса.
             */
            if (!empty($lead_data['status'])) {

                $status_id = $this->getLeadStatusId($lead_data['status']);

                if (!empty($status_id)) {
                    $lead->setStatusId($status_id);
                }

            }

            /**
             * Обновление ответственного за сделку.
             */
            if(!empty($lead_data['responsible_user_id'])) {
                $lead->setResponsibleUserId($lead_data['responsible_user_id']);
            }

            /**
             * Обновление суммы.
             */
            if(isset($lead_data['price'])) {
                $lead->setPrice($lead_data['price']);
            }

            if(!empty($lead_data['updated_at'])) {
                $lead->setUpdatedAt($lead_data['updated_at']);
            }

            if(!empty($lead_data['closed_at'])) {
                $lead->setClosedAt($lead_data['closed_at']);
            }

            $leads_collection->add($lead);

        }

        /**
         * Обновление сделки.
         */
        Request::handleRequest($data, function () use ($leads_collection, $amo_crm_client) {
            $amo_crm_client->leads()->update($leads_collection);
        });

        return Response::success();
    }

    /**
     * Создание заметки в сделки.
     *
     * @param array $data
     * @return array
     */
    public function createLeadNote(array $data)
    {
        /**
         * Получение проекта.
         */
        $amo_crm_client = $this->getCrmClient($this->getProject($data['project_slug']));

        /**
         * Формирование заметки.
         */
        $notes_collection = new NotesCollection();

        $service_message_note = match ($data['type'] ?? null) {
            'bill_paid' => (new BillPaidNote())
                ->setIconUrl($data['icon_url'])
                ->setService($data['service'] ?? 'Profilance Group'),
            default => (new CommonNote())
        };

        $service_message_note->setEntityId($data['lead_id'])
            ->setText($data['message'])
            ->setCreatedBy(0);

        $notes_collection->add($service_message_note);

        /**
         * Создание заметки.
         * @var NoteModel $note
         */
        $note = Request::handleRequest($data, function () use ($notes_collection, $amo_crm_client) {
            return $amo_crm_client->notes(EntityTypesInterface::LEADS)->add($notes_collection);
        });

        return Response::success(null, ['note' => $note?->toArray()]);
    }

    /**
     * Получение кастомного поля сделки.
     *
     * @param array $data
     * @return array
     */
    public function getLeadCustomField(array $data)
    {
        /**
         * Получение проекта.
         */
        $project = $this->getProject($data['project_slug']);
        $amo_crm_client = $this->getCrmClient($project);

        /**
         * Получение поля.
         * @var CustomFieldModel $lead
         */
        $custom_field = Request::handleRequest($data, function () use ($data, $amo_crm_client) {
            return $amo_crm_client->customFields(EntityTypesInterface::LEADS)->getOne($data['id']);
        });

        return Response::success(null, ['custom_field' => $custom_field?->toArray()]);
    }

    //****************************************************************
    //************************* Контакты *****************************
    //****************************************************************

    /**
     * Создание контакта.
     *
     * @param array $data
     * @return array
     * @throws AmoCRMoAuthApiException
     * @throws BadTypeException
     * @throws OperationError
     */
    public function createContact(array $data)
    {
        /**
         * Получение проекта.
         */
        $project = $this->getProject($data['project_slug']);

        $amo_crm_client = $this->getCrmClient($project);

        /**
         * Поиск контакта.
         */
        if (!empty($data['contact_id']) || !empty($data['phone']) || !empty($data['email'])) {
            $contact = $this->getContact([
                'project_slug' => $data['project_slug'],
                'contact_id' => $data['contact_id'] ?? null,
                'search' => $data['phone'] ?? $data['email'] ?? null,
            ])['contact'] ?? null;
        }

        /**
         * Создание контакта в случае его отсутствия.
         * @var ContactModel $contact
         */
        if (empty($contact)) {
            $contact = new ContactModel();
            $contact->setName($data['login'] ?? 'Anonim');
            $contact->setCustomFieldsValues($this->getProjectCustomFieldsValues($project, $data, CrmField::CONTACT_ENTITY));
            $contact = Request::handleRequest($data, function () use ($contact, $amo_crm_client) {
                return $amo_crm_client->contacts()->addOne($contact);
            });
        }
        /**
         * Сброс флага удаления, в случае его наличия.
         */
        else if(isset($data['is_deleted'])){

            $is_deleted_field_id = $project->crm_fields->where('name', 'is_deleted')->first()->crm_id;

            if(!empty($is_deleted_field_id)) {

                $current_value = $contact->getCustomFieldsValues()->getBy('field_id', $is_deleted_field_id)?->getValues()->first()->getValue();

                if(!empty($current_value)) {
                    $contact->setCustomFieldsValues($this->getProjectCustomFieldsValues($project, \Arr::only($data, ['is_deleted']), CrmField::CONTACT_ENTITY));
                    Request::handleRequest($data, function () use ($contact, $amo_crm_client) {
                        $amo_crm_client->contacts()->updateOne($contact);
                    });
                }
            }
        }

        return Response::success(null, ['contact' => $contact?->toArray()]);
    }

    /**
     * Получение контакта.
     *
     * @param array $data
     * @return array
     * @throws AmoCRMoAuthApiException
     * @throws OperationError
     */
    public function getContact(array $data)
    {
        /**
         * Получение проекта.
         */
        $project = $this->getProject($data['project_slug']);

        $amo_crm_client = $this->getCrmClient($project);

        /**
         * Поиск контакта.
         * @var ContactModel $contact
         */
        if(!empty($data['contact_id'])) {
            $contact = Request::handleRequest($data, function () use ($data, $amo_crm_client) {
                return $amo_crm_client->contacts()->getOne($data['contact_id'], $data['with'] ?? []);
            });
        } else {
            $filter = new ContactsFilter();
            $filter->setQuery($data['search']);
            $contact = Request::handleRequest($data, function () use ($data, $filter, $amo_crm_client) {
                return $amo_crm_client->contacts()->get($filter, $data['with'] ?? [])->first();
            });
        }

        /**
         * Примешивание названий полей из базы.
         */
        $contact = $this->addCustomFieldSystemNames([$contact?->toArray()], $project, CrmField::CONTACT_ENTITY)[0] ?? null;

        return Response::success(null, ['contact' => $contact]);
    }

    /**
     * Получение контактов.
     *
     * @param array $data
     * @return array
     */
    public function getContacts(array $data)
    {
        /**
         * Получение проекта.
         */
        $project = $this->getProject($data['project_slug']);
        $amo_crm_client = $this->getCrmClient($project);

        /**
         * Установка фильтров.
         */
        $filter = new ContactsFilter();
        $filter = $this->setListFilters($data, $filter);

        /**
         * Получение контактов.
         * @var LeadsCollection $contacts
         */
        $contacts = Request::handleRequest($data, function () use ($data, $filter, $amo_crm_client) {
            return $amo_crm_client->contacts()->get($filter, $data['with'] ?? []);
        });

        /**
         * Примешивание названий полей из базы.
         */
        $contacts = $this->addCustomFieldSystemNames($contacts?->toArray(), $project, CrmField::CONTACT_ENTITY);

        return Response::success(null, ['contacts' => $contacts]);
    }

    /**
     * Обновление контакта.
     *
     * @param array $data
     * @return array
     */
    public function updateContact(array $data)
    {
        /**
         * Получение проекта.
         */
        $project = $this->getProject($data['project_slug']);

        $amo_crm_client = $this->getCrmClient($project);

        $contact = (new ContactModel())
            ->setId((int)$data['contact_id'])
            ->setCustomFieldsValues(null);

        /**
         * Обновление кастомных полей.
         */
        if(!empty(array_diff_key($data, array_merge(['contact_id' => true, 'project_slug' => true], array_flip(self::BASE_FIELDS))))) {

            $custom_fields = $this->getProjectCustomFieldsValues($project, $data, CrmField::CONTACT_ENTITY);

            if(!empty($custom_fields) && !$custom_fields->isEmpty()) {
                $contact->setCustomFieldsValues($custom_fields);
            }

        }

        /**
         * Обновление.
         */
        Request::handleRequest($data, function () use ($contact, $amo_crm_client) {
            $amo_crm_client->contacts()->updateOne($contact);
        });

        return Response::success();
    }

    //****************************************************************
    //************************** Support *****************************
    //****************************************************************

    /**
     * Проверка и получение проекта.
     *
     * @param string $project_slug
     * @return Project
     */
    protected function getProject($project_slug)
    {
        /**
         * Получение проекта.
         * @var Project $project
         */
        $project = $this->project_repository->getCachedProjects()->where('slug', $project_slug)->first();

        if(empty($project)) {
            alt_log()->file('error_crm')->error('[sendOrder] Проект не найден.', [$project_slug]);
            throw new OperationError('Проект не найден.');
        }

        if($project->isNotActive()) {
            alt_log()->file('error_crm')->error('[sendOrder] Проект не активен.', [$project_slug]);
            throw new OperationError('Проект не активен.');
        }

        return $project;
    }

    /**
     * Получение CRM клиента.
     *
     * @param Project $project
     * @return \AmoCRM\Client\AmoCRMApiClient
     */
    protected function getCrmClient(Project $project)
    {
        $client = new \AmoCRM\Client\AmoCRMApiClient($project->crm_client_id, $project->crm_secret, config('app_main.crm_redirect_uri') ?? Site::getUrl('crm.get_token'));
        $client->setAccountBaseDomain("{$project->crm_subdomain}.amocrm.ru")
            ->onAccessTokenRefresh(function (AccessTokenInterface $access_token) use ($project) {
                $this->setProjectToken($project, $access_token);
            });

        if(!empty($project->crm_access_token)) {

            $access_token = new AccessToken([
                'access_token' => $project->crm_access_token,
                'expires' => $project->crm_access_token_expires,
                'refresh_token' => $project->crm_refresh_token,
            ]);

            if(!$access_token->hasExpired()) {
                $client->setAccessToken($access_token);
            }
        }

        return $client;
    }

    /**
     * Получение коллекции значений кастомных полей.
     *
     * @param Project $project
     * @param array $data
     * @param int $crm_entity
     * @return CustomFieldsValuesCollection
     * @throws \AmoCRM\Exceptions\BadTypeException
     */
    protected function getProjectCustomFieldsValues(Project $project, array $data, $crm_entity)
    {
        $custom_fields_values = new CustomFieldsValuesCollection();

        $project_custom_fields = $project->crm_fields->where('crm_entity', $crm_entity);

        foreach ($data as $field_name => $value) {

            if($crm_entity == CrmField::CONTACT_ENTITY && in_array($field_name, ['phone', 'email'])) {
                $custom_fields_values->add(
                    (new MultitextCustomFieldValuesModel())
                        ->setFieldCode(strtoupper($field_name))
                        ->setValues(
                            (new MultitextCustomFieldValueCollection())
                                ->add(
                                    (new MultitextCustomFieldValueModel())
                                        ->setEnum('WORK')
                                        ->setValue($value)
                                )
                        )
                );
                continue;
            }

            /** @var CrmField $project_custom_field */
            $project_custom_field = $project_custom_fields->where('name', $field_name)->first();

            if(empty($project_custom_field)) {
                continue;
            }

            $custom_fields_values->add(CustomFieldValuesModelFactory::createModel([
                'field_id' => $project_custom_field->crm_id,
                'field_type' => $this->castProjectCustomFieldType($project_custom_field),
                'values' => [['value' => $this->castProjectCustomFieldValue($project_custom_field, $value)]],
            ]));

        }

        return $custom_fields_values;
    }

    /**
     * Обновление коллекции значений кастомных полей.
     *
     * @param Project $project
     * @param CustomFieldsValuesCollection|BaseCustomFieldValuesModel[] $custom_fields
     * @param array $data
     * @param int $crm_entity
     * @return CustomFieldsValuesCollection
     * @throws \AmoCRM\Exceptions\BadTypeException
     */
    protected function updateCustomFieldsValues(Project $project, $custom_fields, array $data, $crm_entity)
    {
        $project_custom_fields = $project->crm_fields->where('crm_entity', $crm_entity);

        foreach ($data as $field_name => $value) {

            /** @var CrmField $project_custom_field */
            $project_custom_field = $project_custom_fields->where('name', $field_name)->first();

            if(empty($project_custom_field)) {
                continue;
            }

            $custom_field = $custom_fields->getBy('fieldId', $project_custom_field->crm_id);

            //            todo
            //            if($crm_entity == CrmField::CONTACT_ENTITY && in_array($field_name, ['phone', 'email'])) {
            //                $custom_fields_values->add(
            //                    (new MultitextCustomFieldValuesModel())
            //                        ->setFieldCode(strtoupper($field_name))
            //                        ->setValues(
            //                            (new MultitextCustomFieldValueCollection())
            //                                ->add(
            //                                    (new MultitextCustomFieldValueModel())
            //                                        ->setEnum('WORK')
            //                                        ->setValue($value)
            //                                )
            //                        )
            //                );
            //                continue;
            //            }

            if(empty($custom_field)) {
                if(!empty($value)) {
                    $custom_fields->add(CustomFieldValuesModelFactory::createModel([
                        'field_id' => $project_custom_field->crm_id,
                        'field_type' => $this->castProjectCustomFieldType($project_custom_field),
                        'values' => [['value' => $this->castProjectCustomFieldValue($project_custom_field, $value)]],
                    ]));
                }
            } else {
                $custom_field->setValues(CustomFieldValueCollectionFactory::createCollection([
                    'field_type' => $this->castProjectCustomFieldType($project_custom_field),
                    'values' => empty($value) ? [new NullCustomFieldValueCollection()] : [['value' => $this->castProjectCustomFieldValue($project_custom_field, $value)]],
                ]));
            }

        }

        return $custom_fields;
    }

    /**
     * Приведение типа кастомного поля.
     *
     * @param CrmField $project_custom_field
     * @return string
     */
    protected function castProjectCustomFieldType(CrmField $project_custom_field)
    {
        return match ($project_custom_field->crm_type) {
            'city' => 'text',
            'bool' => 'checkbox',
            default => $project_custom_field->crm_type,
        };
    }

    /**
     * Приведение типа значения кастомного поля.
     *
     * @param CrmField $project_custom_field
     * @param mixed $value
     * @return mixed
     */
    protected function castProjectCustomFieldValue(CrmField $project_custom_field, $value)
    {
        if(is_null($value)) {
            return null;
        }

        return match ($project_custom_field->crm_type) {
            'numeric' => (float)$value,
            'city' => config('app_lists.pluck_cities', [])[$value] ?? null,
            'bool' => !(empty($value) || $value === 'false'),
            default => is_array($value) ? implode(', ', $value) : (string)$value,
        };
    }

    /**
     * Установка токена проекта.
     *
     * @param Project $project
     * @param AccessTokenInterface $access_token
     * @return void
     */
    protected function setProjectToken(Project $project, AccessTokenInterface $access_token)
    {
        if (!$access_token->hasExpired()) {
            $project->crm_access_token = $access_token->getToken();
            $project->crm_refresh_token = $access_token->getRefreshToken();
            $project->crm_access_token_expires = $access_token->getExpires();
            $project->save();
        }
    }

    /**
     * Приведение статуса к нужному формату.
     *
     * @param string|int $status
     * @return float|\Illuminate\Database\Eloquent\HigherOrderBuilderProxy|int|mixed|string|null
     */
    protected function getLeadStatusId($status)
    {
        if(is_numeric($status)) {
            return $status;
        }

        return app(\App\Repositories\CrmStatus::class)->getCrmIdByName($status);
    }

    /**
     * Добавление системных названий кастомных полей
     *
     * @param array|null $data
     * @param Project $project
     * @param int $crm_entity
     * @return array
     */
    protected function addCustomFieldSystemNames($data, Project $project, $crm_entity)
    {
        if(empty($data)) {
            return $data;
        }

        foreach ($data as &$item) {

            if(!empty($item['status_id'])) {

                $status_system_name = $project->crm_statuses->where('crm_id', $item['status_id'])->first()?->name;

                if(!empty($status_system_name)) {
                    $item['status_system_name'] = $status_system_name;
                }

            }

            foreach ($item['custom_fields_values'] as &$custom_field) {
                $custom_field['field_system_name'] = $project->crm_fields
                    ->where('crm_id', $custom_field['field_id'])
                    ->where('crm_entity', $crm_entity)
                    ->first()?->name;
            }
        }

        return $data;
    }

    /**
     * Установка фильтра на дату обновления.
     *
     * @param array $data
     * @param LeadsFilter|ContactsFilter $filter
     * @return LeadsFilter|ContactsFilter
     */
    protected function setUpdatedAtFilter(array $data, $filter)
    {
        if (!empty($data['filter']['updated_at'])) {
            $filter->setUpdatedAt($this->getRangeFilter('updated_at', $data));
        }

        return $filter;
    }

    /**
     * Установка фильтра на дату обновления.
     *
     * @param array $data
     * @param LeadsFilter|ContactsFilter $filter
     * @return LeadsFilter|ContactsFilter
     */
    protected function setCreatedAtFilter(array $data, $filter)
    {
        if (!empty($data['filter']['created_at'])) {
            $filter->setCreatedAt($this->getRangeFilter('created_at', $data));
        }

        return $filter;
    }

    /**
     * Получение фильтра на диапазон.
     *
     * @param string $field_name
     * @param array $data
     * @return BaseRangeFilter
     */
    protected function getRangeFilter($field_name, array $data)
    {
        $range = new BaseRangeFilter();

        if (!empty($data['filter'][$field_name]['from'])) {
            $range->setFrom($data['filter'][$field_name]['from']);
        }

        if (!empty($data['filter'][$field_name]['to'])) {
            $range->setTo($data['filter'][$field_name]['to']);
        }

        return $range;
    }

    /**
     * Установка фильтров списка.
     *
     * @param array $data
     * @param LeadsFilter|ContactsFilter $filter
     * @return LeadsFilter|ContactsFilter
     */
    protected function setListFilters(array $data, $filter)
    {
        if (!empty($data['filter']['ids'])) {
            $filter->setIds($data['filter']['ids']);
        }

        if (!empty($data['filter']['page'])) {
            $filter->setPage($data['filter']['page']);
        }

        if (!empty($data['filter']['page'])) {
            $filter->setPage($data['filter']['page']);
        }

        $filter->setLimit($data['filter']['limit'] ?? self::DEFAULT_LIMIT);

        $filter = $this->setCreatedAtFilter($data, $filter);

        return $this->setUpdatedAtFilter($data, $filter);
    }

}
