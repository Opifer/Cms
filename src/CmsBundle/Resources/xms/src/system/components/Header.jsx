import React, { PropTypes } from 'react';
import { Link } from 'react-router';
import Add from 'react-icons/lib/md/add';
import SubmitStatus from './SubmitStatus';

const Header = (props) => {
  const { title, secondaryText, createLink, formName } = props;

  return (
    <div className="row pb-2">
      <div className="col-xs-12">
        <div className="float-xs-left">
          {secondaryText && <small className="text-muted">{secondaryText}</small>}
          <h3>{title}</h3>
        </div>
        <div className="float-xs-right py-1">
          <SubmitStatus formName={formName} />
        </div>
        {createLink && <Link to={createLink} className="btn btn-primary float-xs-right">New <Add /></Link>}
      </div>
    </div>
  );
};

Header.propTypes = {
  title: PropTypes.string,
  secondaryText: PropTypes.string,
  createLink: PropTypes.string,
  formName: PropTypes.string,
};

export default Header;
