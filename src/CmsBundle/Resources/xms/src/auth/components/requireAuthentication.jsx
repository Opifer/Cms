/* global BASENAME_ROUTE */
import React, { PropTypes } from 'react';
import { connect } from 'react-redux';
import { push } from 'react-router-redux';

const requireAuthentication = (Component) => {
  class AuthenticatedComponent extends React.Component {
    componentWillMount() {
      this.checkAuth();
    }

    componentWillReceiveProps() {
      this.checkAuth();
    }

    checkAuth() {
      const { dispatch, isAuthenticated, location } = this.props;

      if (!isAuthenticated) {
        dispatch(push(`/admin/login?next=${location.pathname}`));
      }
    }

    render() {
      return (
        <div>
          {this.props.isAuthenticated
            ? <Component {...this.props} />
            : null
          }
        </div>
      );
    }
  }

  const mapStateToProps = (state) => ({
    token: state.user.token || null,
    username: state.user.username || null,
    isAuthenticated: state.user.isAuthenticated || false,
  });

  AuthenticatedComponent.propTypes = {
    dispatch: PropTypes.func,
  };

  return connect(mapStateToProps)(AuthenticatedComponent);
};

export default requireAuthentication;
