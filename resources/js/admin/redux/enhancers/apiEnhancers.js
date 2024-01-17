import axios from 'axios';

import {
    request_type,
    response_type,
    fail_type, GET_API_URL, GRAPHQL_ENDPOINT,
} from '../../constants/apiConstants'

export const apiMiddleware = store => next => action => {

    const {apiCall, isRestAPI, request, ...actualAction} = action;

    if (!apiCall) {
        return next(action)
    }

    store.dispatch({...actualAction, main_type: action.type, type: request_type(action.type)});

    let url = GET_API_URL(GRAPHQL_ENDPOINT);
    let method = 'post'

    if (isRestAPI) {
        url =
            isRestAPI.host +
            (isRestAPI.method === 'get' && request ? `?${new URLSearchParams(request).toString()}` : '');
        method = isRestAPI.method;
    }


    axios({
        url: url,
        method: method,
        headers: {
            'Content-Type': 'application/json',
        },
        data: request,
        validateStatus: function (status) {
            return status >= 200 && status < 300 || status === 422;
        },
    }).then(response => {

        if (response.data.errors || response.data.operation_status && response.data.operation_status.status === 'error') {

            let message, code, validation;

            if(response.data.errors) {
                message = 'validation';
                validation = response.data.errors;
                code = 422;
            } else {
                message = response.data.operation_status.message;
                code = response.data.operation_status.code;
            }

            // if (code && code === 'not_auth') {
            //     window.location = '/';
            // }

            if(validation) {
                store.dispatch({error: message, validation: validation, main_type: action.type, type: fail_type(action.type)});
            } else {
                store.dispatch({error: message, code: code, main_type: action.type, type: fail_type(action.type)});
            }

        } else {
            store.dispatch({response: response.data, main_type: action.type, type: response_type(action.type)});
        }

    }).catch(error => {
        store.dispatch({error: error.message, main_type: action.type, type: fail_type(action.type)})
    });

};
