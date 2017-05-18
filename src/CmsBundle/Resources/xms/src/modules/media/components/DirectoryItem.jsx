import React, { Component, PropTypes } from 'react';
import { connect } from 'react-redux';
import { switchDirectory } from '../actions';
import { DropTarget } from 'react-dnd';

const directoryTarget = {
  drop(props) {
    return {
      id: props.id,
    }
  }
};

function collect(connect, monitor) {
  return {
    connectDropTarget: connect.dropTarget(),
    isOver: monitor.isOver()
  };
}

class DirectoryItem extends Component {
  static propTypes = {
    id: PropTypes.number,
    name: PropTypes.string,
  };

  render() {
    const { id, name, connectDropTarget, isOver } = this.props;

    return connectDropTarget(
      <div className={`item thumbnail ${(isOver) ? 'hover' : ''}`} onClick={this.props.openDirectory}>
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
)(DropTarget('media', directoryTarget, collect)(DirectoryItem));
