import {combineReducers} from 'redux-immutable'
import project from './projectReducer'
import crmField from './crmFieldReducer'
import crmStatus from './crmStatusReducer'

/**
 * Combine reducers.
 */
const rootReducer = () => combineReducers({
    project,
    crmField,
    crmStatus
});

export default rootReducer;
