import Immutable from "immutable";
import {
    CREATE_PROJECT,
    NODE_CLEAR,
    PROJECT,
    PROJECTS,
    REFRESH_PROJECT_TOKEN,
    UPDATE_PROJECT,
} from "../../constants/actionTypes";
import {clearNodeHandler, queryHandler, setData, setSuccessOperationStatus} from "./helpers";
import {project} from "../actions";

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

            case PROJECT:
                return state.set('project', Immutable.fromJS(action.response.data.project));

            case PROJECTS:
                return setData(state, 'projects', action);

            case CREATE_PROJECT:
                return setSuccessOperationStatus(state, action);

            case UPDATE_PROJECT:
                return setSuccessOperationStatus(state, action);

            case REFRESH_PROJECT_TOKEN:
                return setSuccessOperationStatus(state, action);

            default:
                return state;
        }

    });

};

