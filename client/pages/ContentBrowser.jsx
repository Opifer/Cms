import React, { Component } from 'react';
import { RowDetailState } from '@devexpress/dx-react-grid';
import { TableRowDetail } from '@devexpress/dx-react-grid-bootstrap4';
import Grid from '../components/grid/Grid';
import { getContents } from '../api/contentApi';
import IndentedRow from '../components/grid/IndentedRow';
import TableDetailToggleCell from '../components/grid/TableDetailToggleCell';

class ContentBrowser extends Component {
  state = {
    results: []
  }

  componentDidMount = () => {
    getContents({ limit: 25, p: 1 })
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

export default ContentBrowser;
