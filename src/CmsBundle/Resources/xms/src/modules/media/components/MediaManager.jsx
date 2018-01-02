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
    const { directories, items } = this.props;

    return (
      <div className="container-fluid">
        <Dropzone
          name="dropzone"
          activeClassName="dragover"
          disableClick
          className="card p-2 border-dashed dropzone text-center file-drop-area"
          onDrop={(acceptedFiles, rejectedFiles, e) => {
            this.props.processFiles(acceptedFiles);
          }}
        >
          <div className="file-drop-area-overlay"></div>
          <div className="row row-space-2 row-space-top-2">
            <MediaFilters />
            <div className="col-md-6">
              <small className="text-muted">Maximum upload size: 10MB</small>
            </div>
          </div>
        </Dropzone>
        <section className="row media-items">
          {/*<div ng-show="uploadingFiles != null">
            <div ng-repeat="f in uploadingFiles">
              <div className="progress" ng-show="progress[$index] < 100">
                <div className="progress-bar" role="progressbar" aria-valuenow="{{ progress[$index] }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ progress[$index] }}%;">
                  {{ progress[$index] }}%
                </div>
              </div>
            </div>
          </div>*/}

          <div className="image-section col-xs-12 clearfix">
            <DirectoryParentItem />

            {directories.map((dir, i) => (
              <DirectoryItem key={i} { ...dir } />
            ))}

            <DirectoryCreateItem />

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
            <div className="drag-drop">
              <span className="fa fa-image"></span>
              <h3>Drag and drop files to upload</h3>
            </div>
            {/*<div className="btn btn-primary" id="btnMediaPaginate" in-view="mediaCollection.loadMore()" ng-click="mediaCollection.loadMore()" ng-if="(picker.pickerShown || !picker.name)" ng-hide="mediaCollection.end">
              <span ng-if="! MediaCollection.busy">Load more</span>
              <span ng-if="MediaCollection.busy">Loading…</span>
            </div>*/}
          </div>

        {/*<ng-modal show='confirmation.shown'>
                <div className="modal-header">
                    <h4 className="modal-title">Confirm delete</h4>
                </div>
                <div className="modal-body">
                    Do you really want to delete {{ confirmation.name }}?
                </div>
                <div className="modal-footer">
                    <button type="button" className="btn btn-default btn-link" ng-click="confirmation.shown = false">
                        Cancel
                    </button>
                    <button className="btn btn-danger modal-confirm-button" ng-click="deleteMedia(confirmation.idx)" title="Confirms removal">
                        Delete
                    </button>
                </div>
            </ng-modal>*/}
        </section>
      </div>
    );
  }
}

export default connect(
  (state) => ({
    directories: activeDirectoriesSelector(state),
    items: activeItemsSelector(state),
  }),
  (dispatch) => ({
    fetchItems: () => {
      dispatch(getItems());
    },
    processFiles: (files) => {
      dispatch(uploadFiles(
        files,
        (media) => {
          dispatch(addItems(media));
        }, (error) => {

        }
      ));
    },
  })
)(DragDropContext(HTML5Backend)(MediaManager));
