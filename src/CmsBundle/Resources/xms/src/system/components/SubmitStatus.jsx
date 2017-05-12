import React, { PropTypes } from 'react';
import { Link } from 'react-router';
import { connect } from 'react-redux';
import ReactCSSTransitionGroup from 'react-addons-css-transition-group';
import Check from 'react-icons/lib/md/check';
import ErrorOutline from 'react-icons/lib/md/error-outline';
import { FoldingCube } from 'better-react-spinkit';
import { submitInProgress, submitSucceeded, submitFailed } from '../selectors';
import '../stylesheets/submitstatus.scss';

const SubmitStatus = (props) => {
  const { isSubmitInProgress, hasSubmitSucceeded, hasSubmitFailed } = props;

  return (
    <ReactCSSTransitionGroup
      transitionName="submit-status"
      transitionEnterTimeout={0}
      transitionLeaveTimeout={6000}
    >
      {isSubmitInProgress && (
        <span key="0" className="submit-status-progress text-muted">
          <span className="float-xs-left"><FoldingCube size={20} color={'#bbb'} /></span>{' '}
          <span className="float-xs-left"> Saving...</span>
        </span>
      )}
      {hasSubmitSucceeded && <span key="1" className="submit-status-success text-success"><Check size={18} /> Saved!</span>}
      {hasSubmitFailed && <span key="2" className="submit-status-fail text-danger"><ErrorOutline size={18} /> Error</span>}
    </ReactCSSTransitionGroup>
  );
};

SubmitStatus.propTypes = {
  formName: PropTypes.string,
  isSubmitInProgress: PropTypes.bool,
  hasSubmitSucceeded: PropTypes.bool,
  hasSubmitFailed: PropTypes.bool,
};

const mapStateToProps = (state, ownProps) => ({
  isSubmitInProgress: submitInProgress(state, ownProps),
  hasSubmitSucceeded: submitSucceeded(state, ownProps),
  hasSubmitFailed: submitFailed(state, ownProps),
});

export default connect(mapStateToProps)(SubmitStatus);



