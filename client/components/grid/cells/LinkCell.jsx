import React from 'react';
import PropTypes from 'prop-types';
import Cell from './Cell';
import { route } from '../../../routes/router';

const LinkCell = ({ value }) => (
  <Cell>
    <a href={route(value)} target="_blank" rel="noopener noreferrer">
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
