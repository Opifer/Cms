import React, { Component, PropTypes } from 'react';
import { connect } from 'react-redux';
import { Link } from 'react-router';
import { DragSource } from 'react-dnd';
import { moveFile, removeFile } from '../actions';

const mediaSource = {
  beginDrag(props) {
    return {
      id: props.media.id,
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
    media: PropTypes.object,
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
      this.props.onPick(this.props.media);
    }
  }

  render() {
    const { media, connectDragSource, isDragging, picker } = this.props;

    return connectDragSource(
      <div className={`item ${media.provider} ${media.file_type}`}>
        {(media.provider === 'image') && (<img src={media.images.medialibrary} className="visual" alt={media.ame} />)}
        {(media.provider === 'youtube' || media.provider === 'vimeo') && (<img src={media.thumb.images.medialibrary} className="visual" alt={media.name} />)}
        <div className="image-wrapper">
          <div className="extended-data">
            {(media.provider === 'image') && (<span className="details">{ media.metadata.width }x{ media.metadata.height }px</span>)}
          </div>
          <div className="center-stage">
            {(!picker) && (
              <a href={`/admin/media/${media.id}`} type="button" className="btn btn-default-outline edit">
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
          <span className="name">{media.name}</span>
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
      dispatch(removeFile(ownProps.media.id));
    }
  })
)(DragSource('media', mediaSource, collect)(MediaItem));
