import jwtDecode from 'jwt-decode';
import * as t from './actionTypes';

const user = (state = { token: null, isAuthenticated: false }, { type, payload }) => {
  switch (type) {
    case t.LOGIN_USER_REQUEST:
      return Object.assign({}, state, {
        isAuthenticating: true,
        statusText: null,
      });
    case t.LOGIN_USER_SUCCESS:
      return Object.assign({}, state, {
        isAuthenticating: false,
        isAuthenticated: true,
        token: payload.token,
        username: jwtDecode(payload.token).username,
        statusText: 'You have been successfully logged in.',
      });
    case t.LOGIN_USER_FAILURE:
      return Object.assign({}, state, {
        isAuthenticating: false,
        isAuthenticated: false,
        token: null,
        username: null,
        statusText: `Authentication Error: ${payload.status} ${payload.statusText}`,
      });
    case t.LOGOUT_USER:
      return Object.assign({}, state, {
        isAuthenticated: false,
        token: null,
        username: null,
        statusText: 'You have been successfully logged out.',
      });
    default:
      return state;
  }
};

export default user;
