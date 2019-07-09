import React from 'react';
import PropTypes from 'prop-types';
import { Grid as GridContainer, Table, TableHeaderRow } from '@devexpress/dx-react-grid-bootstrap4';
import TableRow from './rows/TableRow';

import '@devexpress/dx-react-grid-bootstrap4/dist/dx-react-grid-bootstrap4.css';

const Grid = ({ cellComponent, children, columns, onRowClick, rows, showHeaderRow }) => (
  <GridContainer
    columns={columns}
    rows={rows}
  >
    <Table
      rowComponent={props => <TableRow onRowClick={onRowClick} {...props} />}
      cellComponent={cellComponent}
    />
    {showHeaderRow && (
      <TableHeaderRow />
    )}
    {children}
  </GridContainer>
);

Grid.propTypes = {
  cellComponent: PropTypes.oneOfType([PropTypes.func, PropTypes.element]),
  children: PropTypes.any,
  columns: PropTypes.array,
  onRowClick: PropTypes.func,
  rows: PropTypes.array,
  showHeaderRow: PropTypes.bool,
};

Grid.defaultProps = {
  cellComponent: Table.Cell,
  children: undefined,
  columns: [],
  onRowClick: undefined,
  rows: [],
  showHeaderRow: true
};

export default Grid;
