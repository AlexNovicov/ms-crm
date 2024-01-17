import React from 'react';
import {connect} from "react-redux";
import {nodeClear, project, updateProject} from "../../redux/actions";
import {bindActionCreators} from "redux";
import {Spinner} from "react-bootstrap";
import {PROJECT, UPDATE_PROJECT} from "../../constants/actionTypes";
import {withMutationHandler} from "../Common/HOC/withMutationHandler";
import CreateOrEditProject from "./CreateOrEditProject";
import {withRouter} from "react-router";
import {fetching_type} from "../../constants/apiConstants";

class EditProject extends React.Component {

    componentDidMount() {
        this.props.projectActions.project(this.props.match.params.id)
    }

    render() {

        if(this.props.project.get(fetching_type(PROJECT))) {
            return <Spinner animation="border" />
        }

        const {forwarding, validation, forwardingError, operation_status} = this.props;

        return <CreateOrEditProject
            action={this.props.projectActions.updateProject}
            forwarding={forwarding}
            validation={validation}
            forwardingError={forwardingError}
            operation_status={operation_status}
            initialData={this.props.project.get('project')}
        />
    }
}

const EditProjectWithMutationHandler = withMutationHandler(
    EditProject,
    "project",
    UPDATE_PROJECT,
    nodeClear
);

function mapStateToProps(state) {
    return {
        project: state.get('project')
    };
}

function mapDispatchToProps(dispatch) {
    return {
        projectActions: bindActionCreators(
            {
                project,
                updateProject,
                nodeClear
            },
            dispatch
        )
    };
}

export default connect(mapStateToProps, mapDispatchToProps)(withRouter(EditProjectWithMutationHandler));

