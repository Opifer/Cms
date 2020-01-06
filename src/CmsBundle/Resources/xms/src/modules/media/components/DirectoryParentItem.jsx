import React, { Component, PropTypes } from 'react';
import { connect } from 'react-redux';
import { switchDirectory } from '../actions';
import { currentDirectorySelector } from '../selectors';

class DirectoryParentItem extends Component {
  constructor(props) {
    super(props);

    this.goBack = this.goBack.bind(this);
  }

  goBack() {
    this.props.openDirectory(this.props.current.parent_id || 0);
  }

  render() {
    const { current } = this.props;

    if (!current) {
      return null;
    }

    return (
      <div className="item item-directory-back thumbnail" onClick={this.goBack}>
        <i className="fa fa-arrow-left" />
      </div>
    );
  }
}

DirectoryParentItem.propTypes = {
  openDirectory: PropTypes.func.isRequired,
  current: PropTypes.object,
};

export default connect(
  (state) => ({
    current: currentDirectorySelector(state),
  }),
  (dispatch) => ({
    openDirectory: dir => dispatch(switchDirectory(dir)),
  })
)(DirectoryParentItem);
