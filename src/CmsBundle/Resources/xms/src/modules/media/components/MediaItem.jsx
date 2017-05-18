import React, { Component, PropTypes } from 'react';
import { connect } from 'react-redux';
import { Link } from 'react-router';
import { DragSource } from 'react-dnd';
import { moveFile, removeFile } from '../actions';

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
    onPick: PropTypes.func,
    picker: PropTypes.bool,
  };

  static defaultProps = {
    picker: false,
  }

  constructor(props) {
    super(props);

    this.delete = this.delete.bind(this);
    this.pick = this.pick.bind(this);
  }

  delete() {
    this.props.removeMedia();
  }

  pick() {
    if (this.props.onPick) {
      this.props.onPick(this.props.id);
    }
  }

  render() {
    const { id, provider, file_type, images, name, metadata, thumb, connectDragSource, isDragging, picker } = this.props;

    return connectDragSource(
      <div className={`item ${provider} ${ file_type}`}>
        {(provider === 'image') && (<img src={images.medialibrary} className="visual" alt={name} />)}
        {(provider === 'youtube' || provider === 'vimeo') && (<img src={ thumb.images.medialibrary } className="visual" alt={name} />)}
        <div className="image-wrapper">
          <div className="extended-data">
            {(provider === 'image') && (<span className="details">{ metadata.width }x{ metadata.height }px</span>)}
          </div>
          <div className="center-stage">
            {(!picker) && (
              <a href={`/admin/media/${id}`} type="button" className="btn btn-default-outline edit">
                Edit
              </a>
            )}
            {(!picker) && (
              <button type="button" onClick={this.delete} className="btn btn-default-outline delete">
                Delete
              </button>
            )}
            {(picker) && (
              <button type="button" onClick={this.pick} className="btn btn-default-outline include">
                Use media
              </button>
            )}
          </div>
          <span className="name">{ name }</span>
        </div>
      </div>
    );
  }
}

export default connect(
  null,
  (dispatch, ownProps) => ({
    moveMedia: (file, dir) => {
      dispatch(moveFile(file, dir));
    },
    removeMedia: () => {
      dispatch(removeFile(ownProps.id));
    }
  })
)(DragSource('media', mediaSource, collect)(MediaItem));
