import React from 'react'
import {connect} from 'react-redux'
import {bindActionCreators} from 'redux'
import {error_type, fetching_type, validation_type} from "../../../constants/apiConstants";

/**
 * HOC withMutationHandler - universal mutations handler
 *
 * @param WrappedComponent {function}
 * @param storeNode {string}
 * @param action
 * @param nodeClear {function}
 * @return {*}
 */
export function withMutationHandler(WrappedComponent, storeNode, action, nodeClear) {

    class MutationHandlerComponent extends React.Component {

        constructor(props) {

            super(props);

            this.state = {
                validation: null,
                operation_status: null,
                forwarding: false,
                forwardingError: null,
                action: action
            };

        }

        static getDerivedStateFromProps(props, state) {

            let forwarding = Boolean(props.connectedNode.get(fetching_type(state.action)));

            if (forwarding !== state.forwarding) {
                return {
                    forwarding
                }
            }

            return null;
        }

        componentDidUpdate(prevProps) {

                let operationStatusPath = [this.state.action, 'operation_status'];

                let forwarding = Boolean(this.props.connectedNode.get(fetching_type(this.state.action))),
                    prevForwarding = Boolean(prevProps.connectedNode.get(fetching_type(this.state.action))),
                    forwardingError = String(this.props.connectedNode.get(error_type(this.state.action), '')),
                    forwardingValidation = this.props.connectedNode.get(validation_type(this.state.action), null),
                    statusOperation = operationStatusPath.length > 0 ? this.props.connectedNode.getIn(operationStatusPath) : null;

                if (!forwarding && prevForwarding) {

                    if (!forwardingError) {

                        let operation_status = statusOperation ? statusOperation.toJS() : null;

                        if (operation_status) {

                            this.setState({operation_status, validation: null, forwardingError: null});

                            this.props.connectedNodeActions.nodeClear(this.state.action);

                        }

                    } else {

                        if (forwardingError === 'validation' && forwardingValidation) {

                            let validation = forwardingValidation.toJS();

                            this.setState({validation, operation_status: null, forwardingError: null});

                            this.props.connectedNodeActions.nodeClear(validation_type(this.state.action));

                        } else {
                            this.setState({forwardingError, validation: null, operation_status: null});
                        }

                        this.props.connectedNodeActions.nodeClear(error_type(this.state.action));

                    }
                }
        }

        render() {
            return (
                <WrappedComponent
                    validation={this.state.validation}
                    operation_status={this.state.operation_status}
                    forwarding={this.state.forwarding}
                    forwardingError={this.state.forwardingError}
                    setOperationStatus={(operation_status)=>this.setState({operation_status})}
                    setError={(forwardingError)=>this.setState({forwardingError})}
                    setValidation={(validation)=>this.setState({validation})}
                    setMutationAction={(action)=>this.setState({action})}
                    {...this.props}
                />
            )
        }
    }

    function mapStateToProps(state) {
        return {
            connectedNode: state.get(storeNode)
        }
    }

    function mapDispatchToProps(dispatch) {
        return {
            connectedNodeActions: bindActionCreators({nodeClear}, dispatch)
        }
    }

    return connect(mapStateToProps, mapDispatchToProps)(MutationHandlerComponent);
}
