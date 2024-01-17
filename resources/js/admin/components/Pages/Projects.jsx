import React from 'react';
import {connect} from "react-redux";
import {deleteProject, nodeClear, projects, refreshProjectToken} from "../../redux/actions";
import {bindActionCreators} from "redux";
import {Button, Col, Container, Row, Spinner, Table} from "react-bootstrap";
import {Link} from "react-router-dom";
import {PROJECT_STATUS} from "../../constants/project";
import {faKey, faRotate, faTrash, faWrench} from "@fortawesome/free-solid-svg-icons";
import {FontAwesomeIcon} from "@fortawesome/react-fontawesome";
import {CopyToClipboard} from 'react-copy-to-clipboard';
import {fetching_type} from "../../constants/apiConstants";
import {PROJECTS} from "../../constants/actionTypes";

class Projects extends React.Component {

    componentDidMount() {
        this.props.projectActions.projects();
    }

    /**
     * Получение списка проектов
     *
     * @returns {JSX.Element}
     */
    getProjectsList() {
        return (
            <Table striped hover>
                <thead>
                <tr>
                    <th>Название</th>
                    <th>Поддомен CRM</th>
                    <th>Статус</th>
                    <th>Действия</th>
                </tr>
                </thead>
                <tbody>
                {this.props.project.getIn(['projects', 'data']).map((project) => (
                    <tr key={project.get('id')}>
                        <td>
                            <Link to={`/projects/${project.get('id')}`}>
                                {project.get('name')}
                            </Link>
                        </td>
                        <td>{project.get('crm_subdomain')}</td>
                        <td className={PROJECT_STATUS[project.get('status')].className}>{PROJECT_STATUS[project.get('status')].title}</td>
                        <td>
                            {project.get('crm_access_token') && project.get('status') !== 'error' && (
                                <CopyToClipboard text={project.get('crm_access_token')}>
                                    <FontAwesomeIcon icon={faKey} className="bi text-primary me-4" style={{ cursor: 'pointer' }} title="Копировать токен доступа" />
                                </CopyToClipboard>
                            )}
                            {(!project.get('crm_access_token') || project.get('status') === 'error') && (
                                <FontAwesomeIcon icon={faWrench} className="bi text-primary me-4" style={{ cursor: 'pointer' }} title="Добавить токен доступа" onClick={() => {
                                    window.open(`https://www.amocrm.ru/oauth?client_id=${project.get('crm_client_id')}&state=${project.get('id')}&mode=popup`);
                                }} />
                            )}
                            {project.get('crm_refresh_token') && project.get('status') === 'error' && (
                                <FontAwesomeIcon icon={faRotate} className="bi text-primary me-4" style={{ cursor: 'pointer' }} title="Перевыпустить токен" onClick={(event) => {
                                    this.props.projectActions.refreshProjectToken(project.get('id'));
                                    event.target.remove();
                                }} />
                            )}
                            <FontAwesomeIcon icon={faTrash} className="bi text-danger" style={{ cursor: 'pointer' }} title="Удалить" onClick={(event) => {
                                this.props.projectActions.deleteProject(project.get('id'));
                                event.target.remove();
                            }} />
                        </td>
                    </tr>
                ))}
                </tbody>
            </Table>
        );
    }

    render() {

        const projectsList = this.props.project.getIn(['projects', 'data']) && this.props.project.getIn(['projects', 'data']).count() > 0 ? this.getProjectsList() : <h5>Список пуст</h5>;

        return (
            <Container fluid className="px-5 pt-4">
                <Row>
                    <Col>
                        <h2>
                            Проекты
                            <Link to="/projects/create">
                                <Button variant="outline-success" className="ms-3">Добавить</Button>
                            </Link>
                            <CopyToClipboard text={`${window.location.protocol}//${window.location.hostname}/crm/get_token`}>
                                <Button variant="outline-primary" className="ms-3">Ссылка для вебхука</Button>
                            </CopyToClipboard>
                        </h2>
                        {this.props.project.get(fetching_type(PROJECTS)) ? <Spinner animation="border" /> : projectsList}
                    </Col>
                </Row>
            </Container>
        );
    }
}

function mapStateToProps(state) {
    return {
        project: state.get('project')
    };
}

function mapDispatchToProps(dispatch) {
    return {
        projectActions: bindActionCreators(
            {
                projects,
                deleteProject,
                refreshProjectToken,
                nodeClear
            },
            dispatch
        )
    };
}

export default connect(mapStateToProps, mapDispatchToProps)(Projects);

