<?php

namespace App\Base\Crm\AmoCrm;

use AmoCRM\Exceptions\AmoCRMApiErrorResponseException;
use App\Base\Notifications\Aggregators\System;
use App\Exceptions\AmoCRMoAuthApiException;
use App\Exceptions\CrmApiOverLimit;
use ProfilanceGroup\BackendSdk\Exceptions\OperationError;

abstract class Request
{
    const API_OVER_LIMIT_CODE = 1;

    /**
     * Обработка отправки запроса в CRM.
     *
     * @param array $data
     * @param callable $callable
     * @return mixed
     * @throws AmoCRMoAuthApiException
     * @throws OperationError
     */
    public static function handleRequest(array $data, Callable $callable)
    {
        try {
            return $callable();
        } catch (CrmApiOverLimit) {
            alt_log()->file('warning_crm')->warning('Превышен лимит API');
            throw new OperationError('Превышен лимит запросов к CRM.', self::API_OVER_LIMIT_CODE);
        } catch (AmoCRMApiErrorResponseException $e) {

            alt_log()->file('error_requests')->error('Ошибка валидации запроса: ' . json_encode($e->getValidationErrors(), JSON_PRETTY_PRINT), [$data, $e]);

            throw new OperationError('Ошибка валидации запроса: ' . json_encode($e->getValidationErrors(), JSON_PRETTY_PRINT));

        } catch (\AmoCRM\Exceptions\AmoCRMoAuthApiException $e) {

            if ($e->getCode() == 401 || $e->getCode() == 403 || $e->getCode() == 443) {
                app(System::class)->aboutCrmAuthError($e->getCode());
            }

            throw  (new AmoCRMoAuthApiException($e->getMessage(), $e->getCode()))->setProjectSlug($data['project_slug']);
        } catch (\AmoCRM\Exceptions\AmoCRMApiNoContentException) {
        } catch (\Throwable $e) {
            alt_log()->file('error_crm')->exception($e, 'Ошибка отправки запроса в CRM');
            throw new OperationError('Ошибка отправки запроса в CRM.', self::API_OVER_LIMIT_CODE);
        }
    }
}
