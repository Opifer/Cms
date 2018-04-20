import React, { Component, PropTypes } from 'react';
import { connect } from 'react-redux';
import { Field, reduxForm } from 'redux-form';
import debounce from 'lodash.debounce';
import { getItems } from '../actions';

class MediaFilters extends Component {
  static propTypes = {
    fetchItems: PropTypes.func.isRequired,
  };

  constructor(props) {
    super(props);

    this.debouncedFetchItems = debounce(this.props.fetchItems, 300);
  }

  render() {
    const { handleSubmit } = this.props;

    return (
      <div className="col-md-6">
        <form
          onSubmit={handleSubmit}
          onChange={() => setTimeout(handleSubmit(params => this.debouncedFetchItems(params)))}
        >
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
          </div>
        </form>
      </div>
    );
  }
}

export default reduxForm({
  form: 'media-filters',
  enableReinitialize: true,
})(connect(
  null,
  (dispatch) => ({
    fetchItems: (filters) => dispatch(getItems(filters, true)),
  })
)(MediaFilters));
