import React, { Component, PropTypes } from 'react';
import { connect } from 'react-redux';
import { DragSource } from 'react-dnd';
import { moveFile } from '../actions';

const mediaSource = {
  beginDrag(props) {
    return {
      id: props.id,
    };
  },
  endDrag(props, monitor, component) {
    if (!monitor.didDrop()) {
      return;
    }

    // When dropped on a compatible target, do something
    const item = monitor.getItem();
    const directory = monitor.getDropResult();

    props.moveMedia(item.id, directory.id)
  }
};

function collect(connect, monitor) {
  return {
    connectDragSource: connect.dragSource(),
    isDragging: monitor.isDragging()
  }
}

class MediaItem extends Component {
  static propTypes = {
    moveMedia: PropTypes.func,
  };

  render() {
    const { provider, file_type, images, name, metadata, thumb, connectDragSource, isDragging } = this.props;

    return connectDragSource(
      <div className={`item ${provider} ${ file_type}`}>
        {(provider === 'image') && (<img src={images.medialibrary} className="visual" alt={name} />)}
        {(provider === 'youtube' || provider === 'vimeo') && (<img src={ thumb.images.medialibrary } className="visual" alt={name} />)}
        <div className="image-wrapper">
          <div className="extended-data">
            {(provider === 'image') && (<span className="details">{ metadata.width }x{ metadata.height }px</span>)}
          </div>
          <div className="center-stage">
            {/*<button type="button" ng-if="picker.name" ng-click="selectMedia($index); $event.stopPropagation();" className="btn btn-default-outline include">Use media</button>
            <button type="button" ng-if="!picker.name" ng-click="editMedia($index); $event.stopPropagation();" className="btn btn-default-outline edit">Edit</button>
            <button type="button" ng-if="!picker.name" ng-click="confirmDelete($index); $event.stopPropagation();" className="btn btn-default-outline delete">Delete</button>*/}
          </div>
          <span className="name">{ name }</span>
        </div>
      </div>
    );
  }
}

export default connect(
  null,
  (dispatch) => ({
    moveMedia: (file, dir) => {
      dispatch(moveFile(file, dir));
    }
  })
)(DragSource('media', mediaSource, collect)(MediaItem));
