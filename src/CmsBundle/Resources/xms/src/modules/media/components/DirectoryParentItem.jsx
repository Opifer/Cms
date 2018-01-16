import React, { Component, PropTypes } from 'react';
import { connect } from 'react-redux';
import { switchDirectory } from '../actions';
import { currentDirectorySelector, parentDirectorySelector } from '../selectors';

class DirectoryParentItem extends Component {
  static propTypes = {
  };

  constructor(props) {
    super(props);

    this.goBack = this.goBack.bind(this);
  }

  goBack() {
    this.props.openDirectory(this.props.current.parent_id || null);
  }

  render() {
    const { current } = this.props;

    if (!current) {
      return null;
    }

    return (
      <div className="item item-directory-back thumbnail" onClick={this.goBack}>
        <i className="fa fa-arrow-left"></i>
      </div>
    );
  }
}

export default connect(
  (state) => ({
    current: currentDirectorySelector(state),
    // parent: parentDirectorySelector(state),
  }),
  (dispatch, ownProps) => ({
    openDirectory: (dir) => {
      dispatch(switchDirectory(dir));
    }
  })
)(DirectoryParentItem);
