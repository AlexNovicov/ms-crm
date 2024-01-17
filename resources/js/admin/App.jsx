import React from 'react';
import Navbar from './components/Navbar';
import LeftSidebar from "./components/LeftSidebar/LeftSidebar";
import {Route, Switch} from "react-router-dom";
import {ThemeProvider} from "react-bootstrap";
import 'bootstrap/dist/css/bootstrap.min.css';
import Projects from "./components/Pages/Projects";
import CreateProject from "./components/Pages/CreateProject";
import EditProject from "./components/Pages/EditProject";

class App extends React.Component {
    render() {
        return (
            <ThemeProvider
                breakpoints={['xxxl', 'xxl', 'xl', 'lg', 'md', 'sm', 'xs', 'xxs']}
            >
                <Navbar />
                <div className="d-flex d-max-height">
                    <LeftSidebar />
                    <Switch>
                        <Route path="/projects/create">
                            <CreateProject />
                        </Route>
                        <Route path="/projects/:id">
                            <EditProject />
                        </Route>
                        <Route path="/">
                            <Projects />
                        </Route>
                    </Switch>
                </div>
            </ThemeProvider>
        )
    }
}

export default App;
