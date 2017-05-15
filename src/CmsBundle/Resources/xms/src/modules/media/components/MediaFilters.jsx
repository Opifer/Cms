import React, { Component, PropTypes } from 'react';
import { Field, reduxForm } from 'redux-form';

class MediaFilters extends Component {
  static propTypes = {

  };

  render() {
    return (
      <div className="col-md-6">
        <div className="input-group search-group">
          <span className="input-group-addon">
            <i className="material-icons md-18">search</i>
          </span>
          <Field
            component="input"
            type="text"
            name="search"
            className="form-control"
            placeholder="Enter name, filename or description"
          />
          {/*<input
            type="text"
            className="form-control"
            placeholder="Enter name, filename or description"
            // ng-model="mediaCollection.search"
            // ng-change="mediaCollection.loadMore(true)"
            // ng-model-options="{debounce:{'default':500}}"
          />*/}
        </div>
      </div>
    );
  }
}

export default reduxForm({
  form: 'media',
})(MediaFilters);
