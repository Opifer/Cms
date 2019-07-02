import React, { Component } from 'react';
import PropTypes from 'prop-types';
import { Getter } from '@devexpress/dx-react-core';
import { EditingState } from '@devexpress/dx-react-grid';
import { Grid as GridContainer, Table, TableEditColumn, TableHeaderRow } from '@devexpress/dx-react-grid-bootstrap4';
import '@devexpress/dx-react-grid-bootstrap4/dist/dx-react-grid-bootstrap4.css';
import TableRow from './TableRow';

class Grid extends Component {
  render = () => {
    const { children, columns, onRowClick, onRowEdit, rows, showHeaderRow } = this.props;

    return (
      <GridContainer
        columns={columns}
        rows={rows}
      >
        <EditingState onCommitChanges={() => { console.log('commit changes') }} />
        <Table
          rowComponent={props => <TableRow onRowClick={onRowClick} {...props} />}
        />
        {showHeaderRow && (
          <TableHeaderRow />
        )}
        {onRowEdit && (
          <TableEditColumn showEditCommand />
        )}
        
        <Getter
          name="tableColumns"
          computed={({ tableColumns }) => {
            // Temporary fix to position the TableEditColumn as last column in the grid.
            // See https://github.com/DevExpress/devextreme-reactive/issues/287
            const result = [...(tableColumns || []).filter(c => c.type !== TableEditColumn.COLUMN_TYPE)];
            if (onRowEdit) result.push({ key: 'editCommand', type: TableEditColumn.COLUMN_TYPE, width: 140 });
            return result;
          }}
        />
        {children}
      </GridContainer>
    );
  };
}

Grid.propTypes = {
  children: PropTypes.any,
  columns: PropTypes.array,
  onRowClick: PropTypes.func,
  rows: PropTypes.array,
  showHeaderRow: PropTypes.bool,
};

Grid.defaultProps = {
  children: undefined,
  columns: [],
  onRowClick: undefined,
  rows: [],
  showHeaderRow: true
};

export default Grid;
