import React, { Component, Fragment } from 'react';
import { RowDetailState, SearchState } from '@devexpress/dx-react-grid';
import { SearchPanel, TableRowDetail, Toolbar } from '@devexpress/dx-react-grid-bootstrap4';
import { getContents, deleteContent, duplicateContent } from '../../api/contentApi';
import Grid from './Grid';
import Cell from './cells/Cell';
import IndentedCell from './cells/IndentedCell';
import LabelCell from './cells/LabelCell';
import LinkCell from './cells/LinkCell';
import TableDetailToggleCell from './cells/TableDetailToggleCell';
import ActionsColumn from './columns/ActionsColumn';
import ConfirmationModal from '../modals/ConfirmationModal';
import { createCancelToken, isCancelled } from '../../services/apiService';
import { getContentDesignerPath, getContentEditPath } from '../../routes/router';

const ContentCell = isSearching => (props) => {
  const { column, row } = props;

  switch (column.name) {
    case 'title': {
      return <IndentedCell level={isSearching ? 0 : row.level} {...props} />;
    }
    case 'path': {
      return <LinkCell {...props} />;
    }
    case 'content_type.name': {
      return <LabelCell {...props} />;
    }
    default: {
      return <Cell {...props} />;
    }
  }
};

class ContentGrid extends Component {
  state = {
    results: [],
    showDeleteModal: false,
    showCopyModal: false,
    selectedRow: null,
    isSearching: false,
  }

  constructor(props) {
    super(props);

    this.cancelToken = null;
  }

  componentDidMount = () => {
    this.fetchContent();
  }

  fetchContent = (params) => {
    if (params && params.q && this.cancelToken) {
      this.cancelToken.cancel('New search call requested');
    }

    this.setState({ isSearching: (params && params.q && params.q !== '') ? true : false });

    this.cancelToken = createCancelToken();
    return getContents({ limit: 25, parent_id: !this.isRoot() ? this.props.row.id : undefined, ...params }, this.cancelToken)
      .then((result) => {
        this.cancelToken = null;
        if (result.data && result.data.results) {
          this.setState({
            results: result.data.results
          });
        }
      })
      .catch((err) => {
        this.cancelToken = null;
        if (!isCancelled(err)) {
          throw err;
        }
      });
  }

  isRoot = () => !this.props.row;

  handleSearch = term => this.fetchContent({ q: term })

  toggleDeleteModal = row => this.setState(prevState => ({
    showDeleteModal: !prevState.showDeleteModal,
    selectedRow: !prevState.selectedRow ? row : null
  }));

  toggleCopyModal = row => this.setState(prevState => ({
    showCopyModal: !prevState.showCopyModal,
    selectedRow: !prevState.selectedRow ? row : null
  }))

  render = () => {
    const { results, showDeleteModal, showCopyModal, selectedRow, isSearching } = this.state;

    const isRoot = this.isRoot();

    return (
      <Fragment>
        <Grid
          cellComponent={ContentCell(isSearching)}
          showHeaderRow={false}
          columns={[
            { name: 'title', title: 'Title' },
            { name: 'path', title: 'Permalink' },
            { name: 'content_type.name', title: 'Type', getCellValue: row => row.content_type.name },
          ]}
          rows={results}
          onRowClick={(row) => {
            window.location.href = getContentDesignerPath(row.id);
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
              { icon: 'edit', action: (row) => { window.location.href = getContentEditPath(row.id); } },
              { icon: 'layers', action: (row) => { window.location.href = getContentDesignerPath(row.id); } },
              { icon: 'control_point_duplicate', action: row => this.toggleCopyModal(row) },
              { icon: 'delete', action: row => this.toggleDeleteModal(row) }
            ]}
          />
        </Grid>
        {showDeleteModal && (
          <ConfirmationModal
            isOpen={showDeleteModal}
            toggle={this.toggleDeleteModal}
            onConfirm={() => deleteContent(selectedRow.id)}
          >
            {`Are you sure you want to delete "${selectedRow.title}"?`}
          </ConfirmationModal>
        )}
        {showCopyModal && (
          <ConfirmationModal
            isOpen={showCopyModal}
            toggle={this.toggleCopyModal}
            onConfirm={() => duplicateContent({ id: selectedRow.id })}
          >
            {`Are you sure you want to copy "${selectedRow.title}"?`}
          </ConfirmationModal>
        )}
      </Fragment>
    );
  }
}

export default ContentGrid;
