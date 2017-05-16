import React, { Component, PropTypes } from 'react';
import { connect } from 'react-redux';
import { switchDirectory } from '../actions';

class DirectoryItem extends Component {
  static propTypes = {
    id: PropTypes.number,
    name: PropTypes.string,
  };

  render() {
    const { id, name } = this.props;

    return (
      <div className="item thumbnail" onClick={this.props.openDirectory}>
        {name}
      </div>
    );
  }
}

export default connect(
  null,
  (dispatch, ownProps) => ({
    openDirectory: () => {
      dispatch(switchDirectory(ownProps.id));
    }
  })
)(DirectoryItem);
