import React, { Component, PropTypes } from 'react';
import { connect } from 'react-redux';
import { reset, Field, reduxForm } from 'redux-form';
import { createDirectory } from '../actions';

class DirectoryCreateItem extends Component {
  static propTypes = {
  };

  constructor(props) {
    super(props);

    this.onSubmit = this.onSubmit.bind(this);
  }

  onSubmit(values) {
    this.props.onCreateDirectory(values);
  }

  render() {
    const { handleSubmit } = this.props;

    return (
      <form
        className="item item-directory-new thumbnail"
        onSubmit={handleSubmit(this.onSubmit)}
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

export default connect(
  null,
  (dispatch, ownProps) => ({
    onCreateDirectory: (values) => {
      dispatch(createDirectory(values));
      dispatch(reset('directory'));
    }
  })
)(reduxForm({
  form: 'directory',
})(DirectoryCreateItem));
