import React from 'react';
import {Button, Collapse, Form, Spinner} from "react-bootstrap";
import {serializeForm} from "../../helpers/serialize";
import OperationStatus from "../Common/OperationStatus";
import {withMutationHandler} from "../Common/HOC/withMutationHandler";
import {CREATE_CRM_FIELD, UPDATE_CRM_FIELD} from "../../constants/actionTypes";
import {createCrmField, deleteCrmField, nodeClear, updateCrmField} from "../../redux/actions";
import {bindActionCreators} from "redux";
import {connect} from "react-redux";
import crmField from "../../redux/reducers/crmFieldReducer";

class CrmFieldForm extends React.Component {

    constructor(props) {
        super(props);

        this.state = {
            open: !this.props.crmField.get('id')
        }

        this.form = React.createRef();

        this.onSubmit = this.onSubmit.bind(this);
        this.onDelete = this.onDelete.bind(this);
    }

    componentDidMount() {
        this.props.setMutationAction(this.props.crmField.get('id') ? UPDATE_CRM_FIELD : CREATE_CRM_FIELD);
    }

    /**
     * Создание или изменение поля.
     *
     * @param event
     */
    onSubmit(event) {
        event.preventDefault();

        const id = this.props.crmField.get('id');
        let data = serializeForm(this.form.current);
        data.project_id = +data.project_id;
        data.crm_id = +data.crm_id;

        if(id && id !== '') {
            data.id = id;
            this.props.crmFieldActions.updateCrmField(data);
        } else {
            this.props.crmFieldActions.createCrmField(data);
        }
    }

    /**
     * Удаление поля.
     *
     * @param event
     */
    onDelete(event) {
        const id = this.props.crmField.get('id');
        this.props.crmFieldActions.deleteCrmField(id);
        event.target.remove();
    }

    render() {
        return (
            <div className="card card-body" style={{ marginBottom: 15 }}>
                {this.props.crmField.get('id') && (
                    <Button onClick={() => this.setState({ open: !this.state.open})} className="btn btn-light">
                        {this.props.crmField.get('title')} ({this.props.crmField.get('name')}, {this.props.crmField.get('crm_id')})
                    </Button>
                )}
                <Collapse in={this.state.open}>
                    <Form onSubmit={this.onSubmit} ref={this.form} className="mb-4 collapse" style={{ marginTop: 15 }}>
                            <input type="hidden" name="id" defaultValue={this.props.crmField.get('id')} />
                            <input type="hidden" name="project_id" defaultValue={this.props.projectId} />
                            <Form.Group className="mb-3">
                                <Form.Label>Название</Form.Label>
                                <Form.Control type="text" name="title" required defaultValue={this.props.crmField.get('title')} />
                            </Form.Group>
                            <Form.Group className="mb-3">
                                <Form.Label>Название (латиницей как в формах)</Form.Label>
                                <Form.Control type="text" name="name" required defaultValue={this.props.crmField.get('name')} />
                            </Form.Group>
                            <Form.Group className="mb-3">
                                <Form.Label>Crm id</Form.Label>
                                <Form.Control type="text" name="crm_id" required defaultValue={this.props.crmField.get('crm_id')} />
                            </Form.Group>
                            <Form.Group className="mb-3">
                                <Form.Label>Crm сущность</Form.Label>
                                <Form.Select name="crm_entity" className="mb-3" defaultValue={this.props.crmField?.get('crm_entity')}>
                                    <option name="crm_entity" value="lead">Лид</option>
                                    <option name="crm_entity" value="contact">Контакт</option>
                                </Form.Select>
                            </Form.Group>
                            <Form.Group className="mb-3">
                                <Form.Label>Crm тип поля</Form.Label>
                                <Form.Select name="crm_type" className="mb-3" defaultValue={this.props.crmField?.get('crm_type')}>
                                    <option name="crm_type" value="text">Текстовое</option>
                                    <option name="crm_type" value="numeric">Числовое</option>
                                    <option name="crm_type" value="bool">Булевое (Да/Нет)</option>
                                    <option name="crm_type" value="city">Город</option>
                                </Form.Select>
                            </Form.Group>
                            <Form.Group className="mb-3">
                                <Form.Label>Crm enum</Form.Label>
                                <Form.Control type="text" name="crm_enum" defaultValue={this.props.crmField.get('crm_enum')} />
                            </Form.Group>
                            <Form.Group className="mb-3">
                                <Form.Label>Сущность</Form.Label>
                                <Form.Select name="entity" className="mb-3" defaultValue={this.props.crmField?.get('entity')}>
                                    <option name="entity" value="order">Заказ</option>
                                    <option name="entity" value="user">Пользователь</option>
                                </Form.Select>
                            </Form.Group>
                            <Form.Group className="mb-3">
                                <Form.Label>Поле сущности</Form.Label>
                                <Form.Control type="text" name="entity_field" defaultValue={this.props.crmField.get('entity_field')} />
                            </Form.Group>
                            <Form.Group className="mb-3">
                                <Form.Label>Тип</Form.Label>
                                <Form.Select name="type" className="mb-3" defaultValue={this.props.crmField?.get('type')}>
                                    <option name="type" value="default">По умолчанию</option>
                                    <option name="type" value="date">Дата</option>
                                </Form.Select>
                            </Form.Group>
                            <Form.Group className="mb-3">
                                <Form.Label>Формат для типа</Form.Label>
                                <Form.Control type="text" name="type_format" defaultValue={this.props.crmField.get('type_format')} />
                            </Form.Group>
                            <Button variant="primary" type="submit" disabled={this.props.forwarding}>
                                {this.props.forwarding ? <Spinner animation="border" /> : "Сохранить"}
                            </Button>
                            {this.props.crmField.get('id') && (
                                <Button variant="outline-danger ms-3" onClick={this.onDelete}>
                                    Удалить
                                </Button>
                            )}
                            <OperationStatus {...this.props} />
                        </Form>
                </Collapse>
            </div>
        );
    }
}

const CrmFieldFormWithMutationHandler = withMutationHandler(
    CrmFieldForm,
    'crmField',
    CREATE_CRM_FIELD,
    nodeClear
);

function mapDispatchToProps(dispatch) {
    return {
        crmFieldActions: bindActionCreators(
            {
                createCrmField,
                updateCrmField,
                deleteCrmField,
                nodeClear
            },
            dispatch
        )
    };
}

export default connect(undefined, mapDispatchToProps)(CrmFieldFormWithMutationHandler);

