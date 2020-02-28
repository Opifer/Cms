import React, { Component, PropTypes } from 'react';
import { connect } from 'react-redux';
import { reset, Field, reduxForm } from 'redux-form';
import { createDirectory } from '../actions';

class DirectoryCreateItem extends Component {
  render() {
    const { handleSubmit, onCreateDirectory } = this.props;

    return (
      <form
        className="item item-directory-new thumbnail"
        onSubmit={handleSubmit(onCreateDirectory)}
      >
        <div className="directory-tab" />
        <div className="directory-body">
          <Field
            name="name"
            component="input"
            type="text"
            className="form-control"
            placeholder="New folder"
          />
        </div>
      </form>
    );
  }
}

DirectoryCreateItem.propTypes = {
  handleSubmit: PropTypes.func,
  onCreateDirectory: PropTypes.func,
};

export default reduxForm({
  form: 'directory',
  enableReinitialize: true,
})(connect(
  null,
  (dispatch) => ({
    onCreateDirectory: (values) => {
      dispatch(createDirectory(values))
        .then(() => dispatch(reset('directory')));
    },
  })
)(DirectoryCreateItem));
