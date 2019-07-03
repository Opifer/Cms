import React, { Component } from 'react';
import { RowDetailState, SearchState } from '@devexpress/dx-react-grid';
import { SearchPanel, TableRowDetail, Toolbar } from '@devexpress/dx-react-grid-bootstrap4';
import { getContents } from '../../api/contentApi';
import Grid from './Grid';
import Cell from './cells/Cell';
import IndentedCell from './cells/IndentedCell';
import LabelCell from './cells/LabelCell';
import TableDetailToggleCell from './cells/TableDetailToggleCell';
import ActionsColumn from './columns/ActionsColumn';

const ContentCell = (props) => {
  const { column, row } = props;

  if (column.name === 'title') {
    return <IndentedCell level={row.level} {...props} />;
  }
  if (column.name === 'content_type.name') {
    return <LabelCell {...props} />;
  }
  return <Cell {...props} />;
};

class ContentGrid extends Component {
  state = {
    results: []
  }

  componentDidMount = () => {
    this.fetchContent();
  }

  fetchContent = (params) => getContents({ limit: 25, parent_id: !this.isRoot() ? this.props.row.id : undefined, ...params })
    .then((result) => {
      if (result.data && result.data.results) {
        this.setState({
          results: result.data.results
        });
      }
    });

  isRoot = () => !this.props.row;

  handleSearch = term => this.fetchContent({ q: term })

  render = () => {
    const { results } = this.state;

    const isRoot = this.isRoot();

    return (
      <Grid
        cellComponent={ContentCell}
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
      >
        <RowDetailState />
        {isRoot && <SearchState onValueChange={this.handleSearch} />}
        {isRoot && <Toolbar />}
        {isRoot && <SearchPanel />}
        <TableRowDetail
          contentComponent={ContentGrid}
          toggleCellComponent={TableDetailToggleCell}
        />
        <ActionsColumn
          actions={[
            { icon: 'edit', action: row => window.location.href = `/app_dev.php/admin/content/edit/${row.id}` },
            { icon: 'layers', action: row => window.location.href = `/app_dev.php/admin/designer/content/${row.id}` },
            { icon: 'control_point_duplicate', action: () => {} },
            { icon: 'delete', action: () => {} }
          ]}
        />
      </Grid>
    );
  }
}

export default ContentGrid;
