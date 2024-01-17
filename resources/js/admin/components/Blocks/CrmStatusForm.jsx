import React from 'react';
import {Button, Collapse, Form, Spinner} from "react-bootstrap";
import {serializeForm} from "../../helpers/serialize";
import OperationStatus from "../Common/OperationStatus";
import {withMutationHandler} from "../Common/HOC/withMutationHandler";
import {CREATE_CRM_STATUS, UPDATE_CRM_STATUS} from "../../constants/actionTypes";
import {createCrmStatus, deleteCrmStatus, nodeClear, updateCrmStatus} from "../../redux/actions";
import {bindActionCreators} from "redux";
import {connect} from "react-redux";
import crmStatus from "../../redux/reducers/crmStatusReducer";

class crmStatusForm extends React.Component {

    constructor(props) {
        super(props);

        this.state = {
            open: !this.props.crmStatus.get('id')
        }

        this.form = React.createRef();

        this.onSubmit = this.onSubmit.bind(this);
        this.onDelete = this.onDelete.bind(this);
    }

    componentDidMount() {
        this.props.setMutationAction(this.props.crmStatus.get('id') ? UPDATE_CRM_STATUS : CREATE_CRM_STATUS);
    }

    /**
     * Создание или изменение поля.
     *
     * @param event
     */
    onSubmit(event) {
        event.preventDefault();

        const id = this.props.crmStatus.get('id');
        let data = serializeForm(this.form.current);
        data.project_id = +data.project_id;
        data.crm_id = +data.crm_id;

        if(id && id !== '') {
            data.id = id;
            this.props.crmStatusActions.updateCrmStatus(data);
        } else {
            this.props.crmStatusActions.createCrmStatus(data);
        }
    }

    /**
     * Удаление поля.
     *
     * @param event
     */
    onDelete(event) {
        const id = this.props.crmStatus.get('id');
        this.props.crmStatusActions.deleteCrmStatus(id);
        event.target.remove();
    }

    render() {
        return (
            <div className="card card-body" style={{ marginBottom: 15 }}>
                {this.props.crmStatus.get('id') && (
                    <Button onClick={() => this.setState({ open: !this.state.open})} className="btn btn-light">
                        {this.props.crmStatus.get('title')} ({this.props.crmStatus.get('name')}, {this.props.crmStatus.get('crm_id')})
                    </Button>
                )}
                <Collapse in={this.state.open}>
                    <Form onSubmit={this.onSubmit} ref={this.form} className="mb-4 collapse" style={{ marginTop: 15 }}>
                            <input type="hidden" name="id" defaultValue={this.props.crmStatus.get('id')} />
                            <input type="hidden" name="project_id" defaultValue={this.props.projectId} />
                            <Form.Group className="mb-3">
                                <Form.Label>Название</Form.Label>
                                <Form.Control type="text" name="title" required defaultValue={this.props.crmStatus.get('title')} />
                            </Form.Group>
                            <Form.Group className="mb-3">
                                <Form.Label>Название (латиницей)</Form.Label>
                                <Form.Control type="text" name="name" required defaultValue={this.props.crmStatus.get('name')} />
                            </Form.Group>
                            <Form.Group className="mb-3">
                                <Form.Label>Crm id</Form.Label>
                                <Form.Control type="text" name="crm_id" required defaultValue={this.props.crmStatus.get('crm_id')} />
                            </Form.Group>
                            <Button variant="primary" type="submit" disabled={this.props.forwarding}>
                                {this.props.forwarding ? <Spinner animation="border" /> : "Сохранить"}
                            </Button>
                            {this.props.crmStatus.get('id') && (
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

const crmStatusFormWithMutationHandler = withMutationHandler(
    crmStatusForm,
    'crmStatus',
    CREATE_CRM_STATUS,
    nodeClear
);

function mapDispatchToProps(dispatch) {
    return {
        crmStatusActions: bindActionCreators(
            {
                createCrmStatus,
                updateCrmStatus,
                deleteCrmStatus,
                nodeClear
            },
            dispatch
        )
    };
}

export default connect(undefined, mapDispatchToProps)(crmStatusFormWithMutationHandler);

