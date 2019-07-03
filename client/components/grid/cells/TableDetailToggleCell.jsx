import React from 'react';
import PropTypes from 'prop-types';
import SmallIcon from '../../icons/SmallIcon';

const TableDetailToggleCell = ({
  expanded, onToggle,
  tableColumn, tableRow, row, className,
  ...restProps
}) => (
  <td
    className="text-center align-middle"
    {...restProps}
  >
    {row.has_children && (
      <SmallIcon
        onClick={onToggle}
        icon={expanded ? 'keyboard_arrow_up' : 'keyboard_arrow_down'}
      />
    )}
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
