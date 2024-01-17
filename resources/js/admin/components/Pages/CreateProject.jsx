import React from 'react';
import {connect} from "react-redux";
import {createProject, nodeClear} from "../../redux/actions";
import {bindActionCreators} from "redux";
import {CREATE_PROJECT} from "../../constants/actionTypes";
import {withMutationHandler} from "../Common/HOC/withMutationHandler";
import CreateOrEditProject from "./CreateOrEditProject";

class CreateProject extends React.Component {
    render() {
        const {forwarding, validation, forwardingError, operation_status} = this.props;

        return <CreateOrEditProject
            action={this.props.projectActions.createProject}
            forwarding={forwarding}
            validation={validation}
            forwardingError={forwardingError}
            operation_status={operation_status}
        />
    }
}

const CreateProjectWithMutationHandler = withMutationHandler(
    CreateProject,
    "project",
    CREATE_PROJECT,
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
                createProject,
                nodeClear
            },
            dispatch
        )
    };
}

export default connect(mapStateToProps, mapDispatchToProps)(CreateProjectWithMutationHandler);

