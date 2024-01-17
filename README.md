# CRM микросервис

### Используется:
- php 8;
- Laravel Octane;
- Laravel 9;
- GraphQL;
- REST API;
- React JS;
- AmoCRM SDK amocrm/amocrm-api-library.

### Описание

Микросервис для взаимодействия с CRM предоставляет список методов для получения/изменения/удаления сделок и контактов в CRM. В настоящий момент реализовано взаимодействие с AmoCRM.

### Структура

- Админка - /admin
- GQL endpoint - /graphql
- Public API endpoint - /api
- Логи - /admin/skcWdsDdms/alt-log/logs

### Превью

![Админка](https://i.imgur.com/YXJjcIw.png)
![Админка](https://i.imgur.com/EGgeMlM.png)

### Примечание
Быть внимательными с использованием статических свойств классов, внедрением зависимостей app и request, т.к. используется Laravel Octane и один экземпляр приложения.

Авторизаця не предусмотрена. Использовать http auth.
