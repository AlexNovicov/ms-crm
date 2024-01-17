import React from 'react';
import {Button} from "react-bootstrap";
import CrmFieldForm from "./CrmFieldForm";
import Immutable from "immutable";

class CrmFields extends React.Component {

    constructor(props) {
        super(props);

        this.state = {
            crmFields: props.crmFields
        }
    }

    render() {
        return (
            <>
                <h4 className="mt-5">
                    Кастомные поля CRM (дли изменения кликнуть по полю)
                </h4>
                {this.state.crmFields && this.state.crmFields.count() > 0
                    && this.state.crmFields.map((crmField, key) => <CrmFieldForm key={key} crmField={crmField} projectId={this.props.projectId} />)}
                <Button variant="outline-success" className="mb-5" onClick={() => this.setState({ crmFields: this.state.crmFields.push(Immutable.Map({})) })}>
                    Добавить
                </Button>
            </>
        );
    }
}

export default CrmFields;

