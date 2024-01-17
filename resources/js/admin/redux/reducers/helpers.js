import {
    error_type,
    fail_type,
    fetching_type,
    request_type,
    response_type,
    validation_type
} from "../../constants/apiConstants";
import Immutable from "immutable";

export function queryHandler(state, action, success_handler) {


    switch (action.type) {
        case request_type(action.main_type):
            return state.set(fetching_type(action.main_type), true)
                .delete(error_type(action.main_type))
                .delete(validation_type(action.main_type));
        case response_type(action.main_type):
            return success_handler(state.delete(fetching_type(action.main_type)), action);
        case fail_type(action.main_type):
            return state.delete(fetching_type(action.main_type))
                .set(error_type(action.main_type), Immutable.fromJS(action.error))
                .set(validation_type(action.main_type), Immutable.fromJS(action.validation));
        default:
            return state;
    }

}

/**
 * Clear node handler.
 *
 * @param {Immutable.Map} state
 * @param {Object} action
 * @return {Immutable.Map}
 */
export function clearNodeHandler(state, action) {

    if (Array.isArray(action.payload.node)) {
        return state.deleteIn(action.payload.node);
    } else {
        return state.delete(action.payload.node);
    }

}

/**
 * Установка данных списка.
 *
 * @param nextState
 * @param key
 * @param preparedAction
 */
export function setData(
    nextState,
    key,
    preparedAction
) {
    if (preparedAction.isPagination) {
        return nextState
            .updateIn([key, 'data'], (data) =>
                list.merge(Immutable.fromJS(preparedAction.response.data[key].data))
            )
            .setIn([key, 'pagination'], Immutable.fromJS(preparedAction.response.data[key].pagination));
    }

    return nextState.set(key, Immutable.fromJS(preparedAction.response.data[key]));
}

/**
 * Установка данных списка.
 *
 * @param nextState
 * @param preparedAction
 */
export function setSuccessOperationStatus(
    nextState,
    preparedAction
) {
    return nextState.setIn(
        [preparedAction.main_type, 'operation_status'],
        Immutable.fromJS({
            status: 'success',
            message: 'Операция успешно выполнена.'
        })
    );
}

