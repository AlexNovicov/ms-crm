<?php

use App\Http\Controllers\Api\CrmController;
use Illuminate\Support\Facades\Route;

/**
 * Отправка заказа в CRM.
 */
Route::post('/send_order', [CrmController::class, 'send_order']);

/**
 * Отправка контакта в CRM.
 */
Route::post('/send_contact', [CrmController::class, 'send_contact']);

/**
 * Работа со сделками.
 */
Route::get('/lead', [CrmController::class, 'get_lead']);
Route::get('/leads', [CrmController::class, 'get_leads']);
Route::patch('/lead', [CrmController::class, 'update_lead']);
Route::patch('/leads', [CrmController::class, 'update_leads']);
Route::put('/lead/note', [CrmController::class, 'create_lead_note']);
Route::get('/lead/custom_field', [CrmController::class, 'get_lead_custom_field']);

/**
 * Работа с контактами.
 */
Route::get('/contact', [CrmController::class, 'get_contact']);
Route::get('/contacts', [CrmController::class, 'get_contacts']);
Route::patch('/contact', [CrmController::class, 'update_contact']);

/**
 * Работа с проектами.
 */
Route::get('/project', [CrmController::class, 'get_project']);
