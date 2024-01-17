import React from 'react';
import {Button} from "react-bootstrap";
import CrmFieldForm from "./CrmFieldForm";
import Immutable from "immutable";
import CrmStatusForm from "./CrmStatusForm";

class CrmStatuses extends React.Component {

    constructor(props) {
        super(props);

        this.state = {
            crmStatuses: props.crmStatuses
        }
    }

    render() {
        return (
            <>
                <h4 className="mt-5">
                    Статусы сделок
                </h4>
                {this.state.crmStatuses && this.state.crmStatuses.count() > 0
                    && this.state.crmStatuses.map((crmStatus, key) => <CrmStatusForm key={key} crmStatus={crmStatus} projectId={this.props.projectId} />)}
                <Button variant="outline-success" className="mb-5" onClick={() => this.setState({ crmStatuses: this.state.crmStatuses.push(Immutable.Map({})) })}>
                    Добавить
                </Button>
            </>
        );
    }
}

export default CrmStatuses;

