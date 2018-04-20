import React, { Component } from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import Dropzone from 'react-dropzone';
import { DragDropContext } from 'react-dnd';
import HTML5Backend from 'react-dnd-html5-backend';
import DirectoryItem from './DirectoryItem';
import DirectoryCreateItem from './DirectoryCreateItem';
import DirectoryParentItem from './DirectoryParentItem';
import MediaItem from './MediaItem';
import MediaFilters from './MediaFilters';
import { addItems, getItems, uploadFiles } from '../actions';
import { activeItemsSelector, activeDirectoriesSelector } from '../selectors';

import '../styles/mediamanager.scss';

class MediaManager extends Component {
  static propTypes = {
    directories: PropTypes.array,
    items: PropTypes.array,
    fetchItems: PropTypes.func,
    onPick: PropTypes.func,
    picker: PropTypes.bool,
  };

  static defaultProps = {
    picker: false,
  }

  componentDidMount() {
    this.props.fetchItems();
  }

  render() {
    const { directories, items, processFiles, loadMore, maxUploadSize, isFetching, results, totalResults } = this.props;

    return (
      <div className="media-manager container-fluid">
        <div className="row row-space-2 row-space-top-2">
          <MediaFilters />
          <div className="col-md-6">
            <div className="file-drop-area-overlay"></div>
            <Dropzone
              name="dropzone"
              activeClassName="dragover"
              className="card p-2 border-dashed dropzone text-center file-drop-area"
              onDrop={(acceptedFiles, rejectedFiles, e) => processFiles(acceptedFiles)}
            >
              <small className="text-muted"><i className="fa fa-upload"></i> Drop files here (max. {maxUploadSize}B)</small>
            </Dropzone>
          </div>
        </div>
        
        <section className="row media-items">
          <div className="image-section col-xs-12 clearfix">
            <DirectoryParentItem />

            {directories.map((dir, i) => (
              <DirectoryItem key={i} { ...dir } />
            ))}

            {(!this.props.picker) && (
              <DirectoryCreateItem />
            )}

            {items.map((item, i) => (
              <MediaItem
                key={i}
                onPick={(media) => {
                  if (this.props.onPick) {
                    this.props.onPick(media);
                  }
                }}
                picker={this.props.picker}
                media={item}
              />
            ))}
          </div>

          <div className="panel-body text-center">
            {(results < totalResults) && (
              <button className="btn btn-primary" onClick={loadMore} disabled={isFetching}>
                {isFetching ? 'Fetching media' : 'Load more'}
              </button>
            )}
          </div>
        </section>
      </div>
    );
  }
}

export default connect(
  (state) => ({
    directories: activeDirectoriesSelector(state),
    items: activeItemsSelector(state),
    maxUploadSize: state.media.maxUploadSize,
    isFetching: state.media.isFetching,
    results: state.media.results,
    totalResults: state.media.totalResults,
  }),
  (dispatch) => ({
    fetchItems: () => dispatch(getItems({ directory: 0 }, true)),
    loadMore: () => dispatch(getItems({ directory: 0 })),
    processFiles: (files) =>
      dispatch(uploadFiles(
        files,
        (media) => dispatch(addItems(media)),
        (error) => {
          console.error(error);
        }
      )),
  })
)(DragDropContext(HTML5Backend)(MediaManager));
