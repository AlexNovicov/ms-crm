import React from 'react';
import ReactDOM from 'react-dom';
import App from './App';
import { Provider } from 'react-redux'
import configureStore from './store/configureStore'
import Immutable from 'immutable'
import {HashRouter} from "react-router-dom";

ReactDOM.render((
    <Provider store={configureStore(Immutable.Map())}>
        <HashRouter>
            <App />
        </HashRouter>
    </Provider>
    ), document.getElementById('root'));
