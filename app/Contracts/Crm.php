<?php

namespace App\Contracts;

interface Crm
{
    /**
     * Получение и сохранение токена авторизации.
     *
     * @param array $data
     * @return void
     */
    public function getToken(array $data);

    /**
     * Обновление токенов проектов.
     *
     * @param array $project_ids
     * @return void
     */
    public function updateTokens(array $project_ids = []);

    /**
     * Создание сделки в CRM.
     *
     * @param array $data
     * @return array
     */
    public function createLead(array $data);

    /**
     * Получение сделки.
     *
     * @param array $data
     * @return array
     */
    public function getLead(array $data);

    /**
     * Получение сделок.
     *
     * @param array $data
     * @return array
     */
    public function getLeads(array $data);

    /**
     * Обновление сделки.
     *
     * @param array $data
     * @return array
     */
    public function updateLead(array $data);

    /**
     * Обновление сделок.
     *
     * @param array $data
     * @return array
     */
    public function updateLeads(array $data);

    /**
     * Создание заметки в сделки.
     *
     * @param array $data
     * @return array
     */
    public function createLeadNote(array $data);

    /**
     * Получение кастомного поля сделки.
     *
     * @param array $data
     * @return array
     */
    public function getLeadCustomField(array $data);

    /**
     * Создание контакта.
     *
     * @param array $data
     * @return array
     */
    public function createContact(array $data);

    /**
     * Получение контакта.
     *
     * @param array $data
     * @return array
     */
    public function getContact(array $data);

    /**
     * Получение контактов.
     *
     * @param array $data
     * @return array
     */
    public function getContacts(array $data);

    /**
     * Обновление контакта.
     *
     * @param array $data
     * @return array
     */
    public function updateContact(array $data);

}
