/**
 * Статус проекта.
 *
 * @type {{inactive: {color: string, title: string}, active: {color: string, title: string}, error: {color: string, title: string}}}
 */
export const PROJECT_STATUS = {
    active: {
        title: 'Активен',
        className: 'text-success'
    },
    inactive: {
        title: 'Не активен',
        className: 'text-warning'
    },
    error: {
        title: 'Ошибка',
        className: 'text-danger'
    },
};

/**
 * Список статусов.
 *
 * @type {[{name: string, title: string},{name: string, title: string},{name: string, title: string}]}
 */
export const PROJECT_STATUSES_LIST = [
    {
        name: 'active',
        title: 'Активен',
    },
    {
        name: 'inactive',
        title: 'Не активен',
    },
    {
        name: 'error',
        title: 'Ошибка',
    },
];

