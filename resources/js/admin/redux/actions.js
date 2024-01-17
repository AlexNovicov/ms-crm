import {
    CREATE_CRM_FIELD, CREATE_CRM_STATUS,
    CREATE_PROJECT, DELETE_CRM_FIELD, DELETE_CRM_STATUS, DELETE_PROJECT,
    NODE_CLEAR, PROJECT,
    PROJECTS, REFRESH_PROJECT_TOKEN, UPDATE_CRM_FIELD, UPDATE_CRM_STATUS, UPDATE_PROJECT,
} from '../constants/actionTypes'

/**
 * Clear node.
 *
 * @param {string} node
 * @return {{type: string, payload: {node: string}}}
 */
export const nodeClear = (node) => {
    return {
        type: NODE_CLEAR,
        payload: {node}
    };
}

/**
 * Получение списка проектов.
 */
export const projects = (data = {}, isPagination = false) => ({
    type: PROJECTS,
    apiCall: true,
    isPagination,
    request: {
        query: `
          query projects($name: String, $status: project_status) {
            projects(name: $name, status: $status) {
              data {
                id
                name
                slug
                crm_default_pipeline
                crm_default_responsible_user_id
                crm_subdomain
                crm_client_id
                crm_secret
                crm_access_token
                crm_refresh_token
                status
              }
            }
          }
        `,
        variables: data
    }
});

/**
 * Получение списка проектов.
 */
export const project = (id) => ({
    type: PROJECT,
    apiCall: true,
    request: {
        query: `
          query project($id: ID!) {
            project(id: $id) {
             id
             name
             slug
             crm_default_pipeline
             crm_default_responsible_user_id
             crm_subdomain
             crm_client_id
             crm_secret
             crm_access_token
             crm_access_token_expires
             status
             crm_fields {
               id
               name
               title
               crm_id
               crm_entity
               crm_type
               crm_enum
               entity
               entity_field
               type
               type_format
             }
             crm_statuses {
               id
               name
               title
               crm_id
             }
            }
          }
        `,
        variables: { id }
    }
});

/**
 * Создание проекта.
 */
export const createProject = (data) => ({
    type: CREATE_PROJECT,
    apiCall: true,
    request: {
        query: `
          mutation create_project($name: String!, $slug: String!, $crm_subdomain: String!, $crm_default_pipeline: Int, $crm_default_responsible_user_id: Int, $crm_client_id: String, $crm_secret: String) {
            create_project(input: { name: $name, slug: $slug, crm_subdomain: $crm_subdomain, crm_default_pipeline: $crm_default_pipeline, crm_default_responsible_user_id: $crm_default_responsible_user_id, crm_client_id: $crm_client_id, crm_secret: $crm_secret }) {
              id
              name
              slug
              crm_default_pipeline
              crm_default_responsible_user_id
              crm_subdomain
              crm_client_id
              crm_secret
              crm_access_token
              status
            }
          }
        `,
        variables: data
    }
});

/**
 * Изменение проекта.
 */
export const updateProject = (data) => ({
    type: UPDATE_PROJECT,
    apiCall: true,
    request: {
        query: `
          mutation update_project($id: ID!, $name: String!, $slug: String!, $crm_subdomain: String!, $crm_default_pipeline: Int, $crm_default_responsible_user_id: Int, $crm_client_id: String, $crm_secret: String, $status: project_status, $crm_access_token: String, $crm_access_token_expires: Int) {
            update_project(id: $id, input: { name: $name, slug: $slug, crm_subdomain: $crm_subdomain, crm_default_pipeline: $crm_default_pipeline, crm_default_responsible_user_id: $crm_default_responsible_user_id, crm_client_id: $crm_client_id, crm_secret: $crm_secret, status: $status, crm_access_token: $crm_access_token, crm_access_token_expires: $crm_access_token_expires }) {
              id
              name
              slug
              crm_default_pipeline
              crm_default_responsible_user_id
              crm_subdomain
              crm_client_id
              crm_secret
              crm_access_token
              crm_access_token_expires
              status
            }
          }
        `,
        variables: data
    }
});

/**
 * Изменение проекта.
 */
export const deleteProject = (id) => ({
    type: DELETE_PROJECT,
    apiCall: true,
    request: {
        query: `
          mutation delete_project($id: ID!) {
            delete_project(id: $id) {
              id
            }
          }
        `,
        variables: { id }
    }
});

/**
 * Создание поля.
 */
export const createCrmField = (data) => ({
    type: CREATE_CRM_FIELD,
    apiCall: true,
    request: {
        query: `
          mutation create_crm_field($project_id: ID!, $name: String!, $title: String!, $crm_id: Int!, $crm_entity: crm_field_crm_entity!, $crm_type: crm_field_crm_type!, $crm_enum: String, $entity: crm_field_entity!, $entity_field: String!, $type: crm_field_type, $type_format: String) {
            create_crm_field(input: { project_id: $project_id, name: $name, title: $title, crm_id: $crm_id, crm_entity: $crm_entity, crm_type: $crm_type, crm_enum: $crm_enum, entity: $entity, entity_field: $entity_field, type: $type, type_format: $type_format }) {
              id
              name
              title
              crm_id
              crm_entity
              crm_type
              crm_enum
              entity
              entity_field
              type
              type_format
            }
          }
        `,
        variables: data
    }
});

/**
 * Изменение поля.
 */
export const updateCrmField = (data) => ({
    type: UPDATE_CRM_FIELD,
    apiCall: true,
    request: {
        query: `
          mutation update_crm_field($id: ID!, $project_id: ID!, $name: String!, $title: String!, $crm_id: Int!, $crm_entity: crm_field_crm_entity!, $crm_type: crm_field_crm_type!, $crm_enum: String, $entity: crm_field_entity!, $entity_field: String!, $type: crm_field_type, $type_format: String) {
            update_crm_field(id: $id, input: { project_id: $project_id, name: $name, title: $title, crm_id: $crm_id, crm_entity: $crm_entity, crm_type: $crm_type, crm_enum: $crm_enum, entity: $entity, entity_field: $entity_field, type: $type, type_format: $type_format  }) {
              id
              name
              title
              crm_id
              crm_entity
              crm_type
              crm_enum
              entity
              entity_field
              type
              type_format
            }
          }
        `,
        variables: data
    }
});

/**
 * Изменение проекта.
 */
export const deleteCrmField = (id) => ({
    type: DELETE_CRM_FIELD,
    apiCall: true,
    request: {
        query: `
          mutation delete_crm_field($id: ID!) {
            delete_crm_field(id: $id) {
              id
            }
          }
        `,
        variables: { id }
    }
});

/**
 * Создание статуса.
 */
export const createCrmStatus = (data) => ({
    type: CREATE_CRM_STATUS,
    apiCall: true,
    request: {
        query: `
          mutation create_crm_status($project_id: ID!, $name: String!, $title: String!, $crm_id: Int!) {
            create_crm_status(input: { project_id: $project_id, name: $name, title: $title, crm_id: $crm_id }) {
              id
              name
              title
              crm_id
            }
          }
        `,
        variables: data
    }
});

/**
 * Изменение статуса.
 */
export const updateCrmStatus = (data) => ({
    type: UPDATE_CRM_STATUS,
    apiCall: true,
    request: {
        query: `
          mutation update_crm_status($id: ID!, $project_id: ID!, $name: String!, $title: String!, $crm_id: Int!) {
            update_crm_status(id: $id, input: { project_id: $project_id, name: $name, title: $title, crm_id: $crm_id }) {
              id
              name
              title
              crm_id
            }
          }
        `,
        variables: data
    }
});

/**
 * Удаление статуса.
 */
export const deleteCrmStatus = (id) => ({
    type: DELETE_CRM_STATUS,
    apiCall: true,
    request: {
        query: `
          mutation delete_crm_status($id: ID!) {
            delete_crm_status(id: $id) {
              id
            }
          }
        `,
        variables: { id }
    }
});

/**
 * Перевыпуск токена проекта.
 */
export const refreshProjectToken = (id) => ({
    type: REFRESH_PROJECT_TOKEN,
    apiCall: true,
    request: {
        query: `
          mutation refresh_project_token($id: ID!) {
            refresh_project_token(id: $id) {
              id
              status
            }
          }
        `,
        variables: { id }
    }
});
