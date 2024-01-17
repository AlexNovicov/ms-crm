<?php

namespace App\Http\Controllers\Web;

use App\Contracts\Crm;
use Illuminate\Http\Request;

class CrmController
{
    /**
     * Обработка вебхука получения токена авторизации.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function get_token(Request $request)
    {
       /**
         * Получение и сохранение токена.
         */
        app(Crm::class)->getToken($request->all());

        return response()->redirectToRoute('admin');
    }
}
