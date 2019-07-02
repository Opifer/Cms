import React from 'react';
import PropTypes from 'prop-types';

const TableDetailToggleCell = ({
  expanded, onToggle,
  tableColumn, tableRow, row, className,
  ...restProps
}) => (
  <td
    className="text-center align-middle"
    {...restProps}
  >
    <i
      className="material-icons md-18"
      style={{ cursor: 'pointer' }}
      onClick={(e) => {
        e.stopPropagation();
        onToggle()
      }}
    >
      {expanded ? 'keyboard_arrow_up' : 'keyboard_arrow_down'}
    </i>
  </td>
);

TableDetailToggleCell.propTypes = {
  className: PropTypes.string,
  expanded: PropTypes.bool,
  onToggle: PropTypes.func,
  tableColumn: PropTypes.object,
  tableRow: PropTypes.object,
  row: PropTypes.any,
};

TableDetailToggleCell.defaultProps = {
  className: undefined,
  expanded: false,
  onToggle: () => {},
  tableColumn: undefined,
  tableRow: undefined,
  row: undefined,
};

export default TableDetailToggleCell;
