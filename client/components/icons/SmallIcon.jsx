import React from 'react';
import PropTypes from 'prop-types';

const SmallIcon = ({ onClick, icon }) => (
  <i
    className="material-icons md-18"
    style={{ cursor: 'pointer' }}
    onClick={(e) => {
      e.stopPropagation();
      onClick()
    }}
  >
    {icon}
  </i>
);

SmallIcon.propTypes = {
  icon: PropTypes.string.isRequired,
  onClick: PropTypes.func,
};

SmallIcon.defaultProps = {
  onClick: () => {},
};

export default SmallIcon;
