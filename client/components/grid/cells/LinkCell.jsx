import React from 'react';
import PropTypes from 'prop-types';
import Cell from './Cell';

const LinkCell = ({ value }) => (
  <Cell>
    <a href={value} target="_blank" rel="noopener noreferrer">
      {value}
    </a>
  </Cell>
);

LinkCell.defaultProps = {
  value: ''
};

LinkCell.propTypes = {
  value: PropTypes.string
};

export default LinkCell;
