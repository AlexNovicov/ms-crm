import React from 'react';
import {Button, Col, Container, Form, Row, Spinner} from "react-bootstrap";
import {serializeForm} from "../../helpers/serialize";
import OperationStatus from "../Common/OperationStatus";
import {PROJECT_STATUSES_LIST} from "../../constants/project";
import CrmFields from "../Blocks/CrmFields";
import CrmStatuses from "../Blocks/CrmStatuses";

class CreateOrEditProject extends React.Component {

    constructor(props) {
        super(props);

        this.form = React.createRef();

        this.onSubmit = this.onSubmit.bind(this);
    }

    onSubmit(event) {
        event.preventDefault();

        let data = serializeForm(this.form.current);
        data.crm_default_pipeline = +data.crm_default_pipeline;
        data.crm_default_responsible_user_id = +data.crm_default_responsible_user_id;

        if(data.crm_access_token_expires === '') {
            data.crm_access_token_expires = null;
        } else {
            data.crm_access_token_expires = +data.crm_access_token_expires;
        }

        if(this.props.initialData) {
            data.id = this.props.initialData.get('id');
        }

        this.props.action(data);
    }

    render() {
        return (
            <Container fluid className="px-5 pt-4">
                <Row>
                    <Col xs={4}>
                        <h2 className="mb-4">
                            {this.props.initialData ? 'Изменение проекта' : 'Создание проекта'}
                        </h2>
                        <Form onSubmit={this.onSubmit} ref={this.form}>
                            <Form.Group className="mb-3">
                                <Form.Label>Название проекта</Form.Label>
                                <Form.Control type="text" name="name" required defaultValue={this.props.initialData?.get('name')} />
                            </Form.Group>
                            <Form.Group className="mb-3">
                                <Form.Label>Слаг (название латиницей)</Form.Label>
                                <Form.Control type="text" name="slug" required defaultValue={this.props.initialData?.get('slug')} />
                            </Form.Group>
                            <Form.Group className="mb-3">
                                <Form.Label>CRM pipleline по умолчанию</Form.Label>
                                <Form.Control type="text" name="crm_default_pipeline" defaultValue={this.props.initialData?.get('crm_default_pipeline')} />
                            </Form.Group>
                            <Form.Group className="mb-3">
                                <Form.Label>CRM ID ответственного за сделку по умолчанию</Form.Label>
                                <Form.Control type="text" name="crm_default_responsible_user_id" defaultValue={this.props.initialData?.get('crm_default_responsible_user_id')} />
                            </Form.Group>
                            <Form.Group className="mb-3">
                                <Form.Label>Поддомен CRM</Form.Label>
                                <Form.Control type="text" name="crm_subdomain" required defaultValue={this.props.initialData?.get('crm_subdomain')} />
                            </Form.Group>
                            <Form.Group className="mb-3">
                                <Form.Label>CRM client id</Form.Label>
                                <Form.Control type="text" name="crm_client_id" defaultValue={this.props.initialData?.get('crm_client_id')} />
                            </Form.Group>
                            <Form.Group className="mb-3">
                                <Form.Label>CRM secret</Form.Label>
                                <Form.Control type="text" name="crm_secret" defaultValue={this.props.initialData?.get('crm_secret')} />
                            </Form.Group>
                            <Form.Group className="mb-3">
                                <Form.Label>CRM access token</Form.Label>
                                <Form.Control type="text" name="crm_access_token" defaultValue={this.props.initialData?.get('crm_access_token')} />
                            </Form.Group>
                            <Form.Group className="mb-3">
                                   <Form.Label>CRM access token expires</Form.Label>
                                <Form.Control type="text" name="crm_access_token_expires" defaultValue={this.props.initialData?.get('crm_access_token_expires')} />
                            </Form.Group>
                            {this.props.initialData && (
                                <Form.Group className="mb-3">
                                    <Form.Label>Статус</Form.Label>
                                    <Form.Select className="mb-3" name="status" defaultValue={this.props.initialData?.get('status')}>
                                        {PROJECT_STATUSES_LIST.map((status) => (
                                            <option
                                                key={status.name}
                                                name="status"
                                                value={status.name}
                                            >
                                                {status.title}
                                            </option>
                                        ))}
                                    </Form.Select>
                                </Form.Group>
                            )}
                            <Button variant="primary" type="submit" disabled={this.props.forwarding}>
                                {this.props.forwarding ? <Spinner animation="border" /> : (this.props.initialData ? "Изменить" : "Создать")}
                            </Button>
                            <OperationStatus {...this.props} />
                        </Form>
                    </Col>
                    <Col xs={2} />
                    <Col xs={6}>
                        <div style={{ maxHeight: 1010, overflow: 'auto' }}>
                            {this.props.initialData && (
                                <CrmFields crmFields={this.props.initialData.get('crm_fields')} projectId={this.props.initialData.get('id')} />
                            )}
                        </div>
                        <div style={{ maxHeight: 600, overflow: 'auto' }}>
                            {this.props.initialData && (
                                <CrmStatuses crmStatuses={this.props.initialData.get('crm_statuses')} projectId={this.props.initialData.get('id')} />
                            )}
                        </div>
                    </Col>
                </Row>

            </Container>
        );
    }
}

export default CreateOrEditProject;

