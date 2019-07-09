import React from 'react';
import Cell from './Cell';

const LabelCell = props => (
  <Cell>
    <div className="label label-info">
      {props.value}
    </div>
  </Cell>
);

export default LabelCell;
