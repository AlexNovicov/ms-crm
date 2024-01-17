<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Crm;
use Illuminate\Http\Request;
use ProfilanceGroup\BackendSdk\Abstracts\Controller;
use ProfilanceGroup\BackendSdk\Support\Response;

class CrmController extends Controller
{
    public function __construct(private readonly Crm $crm)
    {
    }

    /**
     * Отправка заказа в CRM.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function send_order(Request $request)
    {
        if(!empty($result = $this->validateRequest($request))) {
            return $result;
        }

        if(empty($request->except(['project_slug']))) {
            return $this->returnJsonResult(Response::error('Не переданы данные.'));
        }

        $result = $this->crm->createLead($request->all());

        return $this->returnJsonResult($result);
    }

    /**
     * Отправка заказа в CRM.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function send_contact(Request $request)
    {
        if(!empty($result = $this->validateRequest($request))) {
            return $result;
        }

        if(empty($request->except(['project_slug']))) {
            return $this->returnJsonResult(Response::error('Не переданы данные.'));
        }

        $result = $this->crm->createContact($request->all());

        return $this->returnJsonResult($result);
    }

    /**
     * Получение сделки.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function get_lead(Request $request)
    {
        if(!empty($result = $this->validateRequest($request, ['lead_id' => 'required|int|min:0']))) {
            return $result;
        }

        $result = $this->crm->getLead($request->all());

        return $this->returnJsonResult($result);
    }

    /**
     * Получение сделок.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function get_leads(Request $request)
    {
        if(!empty($result = $this->validateRequest($request))) {
            return $result;
        }

        $result = $this->crm->getLeads($request->all());

        return $this->returnJsonResult($result);
    }

    /**
     * Изменение сделки.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update_lead(Request $request)
    {
        if(!empty($result = $this->validateRequest($request, ['lead_id' => 'required|int|min:0']))) {
            return $result;
        }

        $result = $this->crm->updateLead($request->all());

        return $this->returnJsonResult($result);
    }

    /**
     * Изменение сделок.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update_leads(Request $request)
    {
        if(!empty($result = $this->validateRequest($request, ['leads' => 'required|array']))) {
            return $result;
        }

        $result = $this->crm->updateLeads($request->all());

        return $this->returnJsonResult($result);
    }

    /**
     * Создание заметки в сделке.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create_lead_note(Request $request)
    {
        if(!empty($result = $this->validateRequest($request, [
            'lead_id' => 'required|int|min:0',
            'message' => 'required|string|max:512',
            'service' => 'nullable|string|max:32',
            'type' => 'nullable|in:bill_paid',
            'icon_url' => 'requiredIf:type,bill_paid|url',
        ]))) {
            return $result;
        }

        $result = $this->crm->createLeadNote($request->all());

        return $this->returnJsonResult($result);
    }

    /**
     * Получение кастомного поля сделки.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function get_lead_custom_field(Request $request)
    {
        if(!empty($result = $this->validateRequest($request, ['id' => 'required|int|min:0']))) {
            return $result;
        }

        $result = $this->crm->getLeadCustomField($request->all());

        return $this->returnJsonResult($result);
    }

    /**
     * Получение контакта.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function get_contact(Request $request)
    {
        if(!empty($result = $this->validateRequest($request, [
            'contact_id' => 'requiredWithout:search|nullable|int|min:0',
            'search' => 'requiredWithout:contact_id|nullable|string|min:1|max:512',
        ]))) {
            return $result;
        }

        $result = $this->crm->getContact($request->all());

        return $this->returnJsonResult($result);
    }

    /**
     * Получение контактов.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function get_contacts(Request $request)
    {
        if(!empty($result = $this->validateRequest($request))) {
            return $result;
        }

        $result = $this->crm->getContacts($request->all());

        return $this->returnJsonResult($result);
    }

    /**
     * Изменение контакта.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update_contact(Request $request)
    {
        if(!empty($result = $this->validateRequest($request, ['contact_id' => 'required|int|min:0']))) {
            return $result;
        }

        $result = $this->crm->updateContact($request->all());

        return $this->returnJsonResult($result);
    }


    /**
     * Получение контакта.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function get_project(Request $request)
    {
        if(!empty($result = $this->validateRequest($request))) {
            return $result;
        }

        $result = app(\App\Base\Project\Project::class)->getProject($request->project_slug);

        return $this->returnJsonResult($result);
    }

    //****************************************************************
    //************************** Support *****************************
    //****************************************************************

    /**
     * Валидация данных.
     *
     * @param Request $request
     * @param array $rules
     * @return \Illuminate\Http\JsonResponse
     */
    protected function validateRequest(Request $request, array $rules = [])
    {
        $validator = \Validator::make($request->all(), array_merge([
            'project_slug' => 'required|string|max:128'
        ], $rules));

        if ($validator->fails()) {
            return $this->returnJsonResult(Response::error('Ошибка валидации запроса.', ['errors' => $validator->errors()->all()]));
        }

        return null;
    }
}
