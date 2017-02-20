import React, { PropTypes } from 'react';
import { Link } from 'react-router';
import SubmitStatus from './SubmitStatus';

const FormActions = (props) => {
  const { cancelLink, formName } = props;

  return (
    <div className="row">
      <div className="col-xs-12">
        <button type="submit" className="btn btn-primary">save</button>
        {cancelLink && <Link to={cancelLink} className="btn btn-link">Cancel</Link>}
        <div className="float-xs-right py-1">
          <SubmitStatus formName={formName} />
        </div>
      </div>
    </div>
  );
};

FormActions.propTypes = {
  cancelLink: PropTypes.string,
  formName: PropTypes.string,
};

export default FormActions;
