import React from 'react';
import {FontAwesomeIcon} from "@fortawesome/react-fontawesome";
import {faBookOpen, faRoadBarrier, faBook} from "@fortawesome/free-solid-svg-icons";
import {NavLink} from "react-router-dom";

class LeftSidebar extends React.Component {
    render() {
        return (
            <div className="d-flex flex-column flex-shrink-0 p-3 text-white bg-dark sticky-top" style={{ width: 280, height: "calc(100%-56px)", position: "relative" }}>
                <ul className="nav nav-pills flex-column mb-auto">
                    <li className="nav-item">
                        <NavLink to="/" className="nav-link text-white">
                            <FontAwesomeIcon icon={faBookOpen} className="bi me-2" />
                            Проекты
                        </NavLink>
                        <a href="/admin/skcWdsDdms/alt-log/logs" target="_blank" className="nav-link text-white">
                            <FontAwesomeIcon icon={faRoadBarrier} className="bi me-2" />
                            Логи
                        </a>
                        <NavLink to="/docs" className="nav-link text-white">
                            <FontAwesomeIcon icon={faBook} className="bi me-2" />
                            Документация
                        </NavLink>
                    </li>
                </ul>

                <div style={{ position: "absolute", bottom: 90, textAlign: "center" }}>
                    <img src="/assets/images/logo/logo_profilance_white.png" style={{ width: "80%", marginLeft: -8 }}  alt=""/>
                </div>

            </div>
        );
    }
}

export default LeftSidebar;
