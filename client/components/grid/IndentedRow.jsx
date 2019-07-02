import React, { Component } from 'react';
import { RowDetailState } from '@devexpress/dx-react-grid';
import { TableRowDetail } from '@devexpress/dx-react-grid-bootstrap4';
import { getContents } from '../../api/contentApi';
import Grid from './Grid';
import TableDetailToggleCell from './TableDetailToggleCell';

class IndentedRow extends Component {
  state = {
    results: []
  }

  componentDidMount = () => {
    const { row } = this.props;

    getContents({ limit: 25, p: 1, parent_id: row.id })
      .then((result) => {
        if (result.data && result.data.results) {
          this.setState({
            results: result.data.results
          });
        }
      });
  }

  render = () => {
    const { results } = this.state;

    return (
      <Grid
        showHeaderRow={false}
        columns={[
          { name: 'title', title: 'Title' },
          { name: 'path', title: 'Permalink' },
          { name: 'content_type.name', title: 'Type', getCellValue: row => row.content_type.name },
        ]}
        rows={results}
        onRowClick={(row) => {
          window.location.href = `/app_dev.php/admin/designer/content/${row.id}`;
        }}
        onRowEdit={(row) => {
          console.log('edit');
        }}
      >
        <RowDetailState />
        <TableRowDetail
          contentComponent={IndentedRow}
          toggleCellComponent={TableDetailToggleCell}
        />
      </Grid>
    );
  }
}


export default IndentedRow;
