import Immutable from "immutable";
import {CREATE_CRM_FIELD, DELETE_CRM_FIELD, NODE_CLEAR, UPDATE_CRM_FIELD,} from "../../constants/actionTypes";
import {clearNodeHandler, queryHandler, setSuccessOperationStatus} from "./helpers";

/**
 * Init App State.
 *
 * @type {Immutable.Map}
 */
const immutableState = Immutable.Map({});

/**
 * ReportsReducer.
 *
 * @param {Immutable.Map} state
 * @param {Object} action
 * @return {Immutable.Map}
 */
export default function (state = immutableState, action) {

    if (!action.main_type) {
        action.main_type = action.type;
    }

    /**
     * Store actions.
     */
    switch (action.main_type) {

        case NODE_CLEAR:
            return clearNodeHandler(state, action);

    }

    /**
     * API actions.
     */
    return queryHandler(state, action, (state, action) => {

        switch (action.main_type) {

            case CREATE_CRM_FIELD:
                return setSuccessOperationStatus(state, action);

            case UPDATE_CRM_FIELD:
                return setSuccessOperationStatus(state, action);

            case DELETE_CRM_FIELD:
                return setSuccessOperationStatus(state, action);

            default:
                return state;
        }

    });

};

