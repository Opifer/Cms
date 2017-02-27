import React, { Component, PropTypes } from 'react';
import { connect } from 'react-redux';
import { Table, Column, Cell } from 'fixed-data-table';
import { Link } from 'react-router';
import dimensions from 'react-dimensions';
import { fetchDataViewsIfNeeded } from '../actions';
import { selectAll } from '../selectors';
import { Header } from '../../system/components';

class TextCell extends Component {
  render() {
    const { rowIndex, field, data, ...props } = this.props;
    return (
      <Cell {...props}>
        {data[rowIndex][field]}
      </Cell>
    );
  }
}

class ActiveCell extends Component {
  render() {
    const { rowIndex, field, data, ...props } = this.props;
    return (
      <Cell {...props}>
        {data[rowIndex][field] ? <span className="text-success">active</span> : <span className="text-muted">inactive</span>}
      </Cell>
    );
  }
}

class LinkToIdCell extends Component {
  render() {
    const { rowIndex, field, data, ...props } = this.props;
    return (
      <Cell {...props}>
        <Link to={`/admin/dataviews/${data[rowIndex].id}`}>Edit</Link>
      </Cell>
    );
  }
}

class DataViewTable extends Component {
  render() {
    const { dataViews, containerWidth, containerHeight } = this.props;

    return (
      <Table
        rowHeight={34}
        headerHeight={34}
        rowsCount={dataViews.length}
        width={containerWidth}
        height={containerHeight || 400}
      >
        <Column
          header={<Cell />}
          cell={<TextCell data={dataViews} field="id" />}
          width={40}
        />
        <Column
          header={<Cell>Active</Cell>}
          cell={<ActiveCell data={dataViews} field="active" />}
          width={70}
        />
        <Column
          header={<Cell>Name</Cell>}
          cell={<TextCell data={dataViews} field="name" />}
          flexGrow={1}
          width={100}
        />
        <Column
          header={<Cell>Display Name</Cell>}
          cell={<TextCell data={dataViews} field="display_name" />}
          width={400}
          flexGrow={2}
        />
        <Column
          header={<Cell />}
          cell={<LinkToIdCell data={dataViews} />}
          width={100}
        />
      </Table>
    );
  }
}

const DataViewTableDims = dimensions({
  getHeight: () => window.innerHeight - 200,
  getWidth: (element) => element.parentElement.clientWidth - 30,
  containerStyle: { width: '100%', height: 'auto' },
})(DataViewTable);


class DataViewIndex extends Component {
  componentDidMount() {
    this.props.dispatch(fetchDataViewsIfNeeded());
  }

  render() {
    const { dataViews } = this.props;

    return (
      <div className="container-fluid">
        <div className="row">
          <div className="col-xs-12 col-lg-10 offset-lg-1">
            <Header title={'Dataviews'} secondaryText={`${dataViews.length} items`} createLink={'/admin/dataviews/0'} />
            <DataViewTableDims {...this.props} />
          </div>
        </div>
      </div>
    );
  }
}

DataViewIndex.propTypes = {
  dispatch: PropTypes.func.isRequired,
  dataViews: PropTypes.array.isRequired,
  containerWidth: PropTypes.number,
  containerHeight: PropTypes.number,
};

const mapStateToProps = (state) => ({
  dataViews: selectAll(state),
});

export default connect(
  mapStateToProps
)(DataViewIndex);
