import React from 'react';
import { Table } from '@devexpress/dx-react-grid-bootstrap4';

const TableRow = ({ row, onRowClick, ...restProps }) => (
  <Table.Row
    {...restProps}
    onClick={() => onRowClick ? onRowClick(row) : null}
  />
);

export default TableRow;
