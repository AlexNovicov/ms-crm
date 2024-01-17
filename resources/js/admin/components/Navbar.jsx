import React from 'react';
import {Navbar as ReactNavbar} from "react-bootstrap";
import {Link} from "react-router-dom";

class Navbar extends React.Component {
    render() {
        return (
            <ReactNavbar bg="dark" expand="lg" variant="dark">
                <Link to="/" className="navbar-brand ps-4">CRM микросервис</Link>
            </ReactNavbar>
        );
    }
}

export default Navbar;
