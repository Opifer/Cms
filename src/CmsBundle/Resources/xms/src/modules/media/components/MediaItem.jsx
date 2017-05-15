import React, { Component, PropTypes } from 'react';

class MediaItem extends Component {
  static propTypes = {

  };

  render() {
    const { provider, file_type, images, name, metadata, thumb } = this.props;

    return (
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

export default MediaItem;
