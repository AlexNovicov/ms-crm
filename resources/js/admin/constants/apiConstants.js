/**
 * Get full action type query.
 *
 * @param {string} type - action type
 * @return {string} - full action type query
 */
export const request_type = type => `${type}_REQUEST`;

/**
 * Get full action type response.
 *
 * @param {string} type - action type
 * @return {string} - full action type response
 */
export const response_type = type => `${type}_RESPONSE`;

/**
 * Get full action type error.
 *
 * @param {string} type - action type
 * @return {string} - full action type error
 */
export const fail_type = type => `${type}_FAIL`;

/**
 * Get full action type fetching query.
 *
 * @param {string} type
 * @return {string}
 */
export const fetching_type = type => `${type}_FETCHING`;

/**
 * Get full action type error query.
 *
 * @param {string} type
 * @return {string}
 */
export const error_type = type => `${type}_ERROR`;

/**
 * Get full action type validation query.
 *
 * @param {string} type
 * @return {string}
 */
export const validation_type = type => `${type}_VALIDATION`;

/**
 * Get url API endpoint.
 *
 * @param {string} endpoint
 * @return {string}
 */
export const GET_API_URL = (endpoint) => {
    return document.querySelector('meta[name="api-path"]').content + endpoint;
};

/**
 * Точка входа graphql запросов
 *
 * @type {string}
 */
export const GRAPHQL_ENDPOINT = '/graphql';
