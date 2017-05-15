import React, { Component, PropTypes } from 'react';
import { connect } from 'react-redux';
import MediaItem from './MediaItem';
import MediaFilters from './MediaFilters';
import { getItems } from '../actions';

class MediaManager extends Component {
  static propTypes = {
    items: PropTypes.array,
    fetchItems: PropTypes.func,
  };

  componentDidMount() {
    this.props.fetchItems();
  }

  render() {
    const { items } = this.props;

    return (
      <div className="container-fluid">
        <div className="row row-space-2 row-space-top-2">
          <MediaFilters />
          <div className="col-md-6">
            <small className="text-muted">Maximum upload size: 10MB</small>
          </div>
        </div>
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
            {items.map((item, i) => (
              <MediaItem key={i} { ...item } />
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
    items: Object.keys(state.media.items).map(k => state.media.items[k]),
  }),
  (dispatch) => ({
    fetchItems: () => {
      dispatch(getItems());
    }
  })
)(MediaManager);
