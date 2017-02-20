import React, { Component, PropTypes } from 'react';
import { connect } from 'react-redux';
import { Field, reduxForm } from 'redux-form';
import { loginUser } from '../actions';

class Login extends Component {
  render() {
    const { handleSubmit } = this.props;

    return (
      <div className="my-3 col-xs-12 col-md-6 offset-md-3 col-lg-4 offset-lg-4">
        <div className="card">
          <div className="card-block">
            <h3>Login</h3>
            <form onSubmit={handleSubmit}>
              <div className="form-group">
                <label htmlFor="username">Username</label>
                <Field name="username" component="input" type="text" className="form-control" />
              </div>
              <div className="form-group">
                <label htmlFor="password">Password</label>
                <Field name="password" component="input" type="password" className="form-control" />
              </div>
              <button type="submit" className="btn btn-primary">login</button>
            </form>
          </div>
        </div>
      </div>
    );
  }
}

Login.propTypes = {
  dispatch: PropTypes.func.isRequired,
  handleSubmit: PropTypes.func.isRequired,
};

const mapStateToProps = (state) => ({
  dataViews: state.dataViews,
});


export default reduxForm({
  form: 'login',
  onSubmit: loginUser,
})(connect(mapStateToProps)(Login));
