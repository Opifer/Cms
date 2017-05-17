import React, { Component, PropTypes } from 'react';
import { connect } from 'react-redux';
import { Field, reduxForm } from 'redux-form';

class DirectoryCreateItem extends Component {
  static propTypes = {
  };

  createDirectory() {
    console.log('CREATE DIRECTORY');
  }

  render() {
    const { handleSubmit } = this.props;

    return (
      <form className="item thumbnail" onSubmit={handleSubmit}>
        <i className="fa fa-plus"></i>
        <Field
          name="name"
          component="input"
          type="text"
          className="form-control"
        />
      </form>
    );
  }
}

export default connect(
  null,
  (dispatch, ownProps) => ({
    // openDirectory: () => {
    //   console.log('OPEN DIRECTORY', ownProps.id);
    //   // dispatch();
    // }
  })
)(reduxForm({
  form: 'directory',
})(DirectoryCreateItem));
