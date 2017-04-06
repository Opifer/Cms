import {
  push,
} from 'react-router-redux';
// import jwtDecode from 'jwt-decode';
import fetch from 'isomorphic-fetch';
import * as t from './actionTypes';

import {
  checkHttpStatus,
} from '../utilities';

export function loginUserSuccess(token) {
  localStorage.setItem('token', token);
  return {
    type: t.LOGIN_USER_SUCCESS,
    payload: {
      token,
    },
  };
}

export function loginUserFailure(error) {
  localStorage.removeItem('token');
  return {
    type: t.LOGIN_USER_FAILURE,
    payload: {
      status: error.response.status,
      statusText: error.response.statusText,
    },
  };
}

export function loginUserRequest() {
  return {
    type: t.LOGIN_USER_REQUEST,
  };
}

export function logout() {
  localStorage.removeItem('token');
  return {
    type: t.LOGOUT_USER,
  };
}

export function logoutAndRedirect() {
  return (dispatch) => {
    dispatch(logout());
    dispatch(push('/admin/login'));
  };
}

export function loginUser({ username, password }, dispatch) {
  dispatch(loginUserRequest());
  return fetch('/admin/api/login_check', {
    method: 'post',
    headers: {
      Accept: 'application/json',
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({
      _username: username,
      _password: password,
    }),
  })
  .then(checkHttpStatus)
  .then(response => response.json())
  .then(response => {
    try {
      // const decoded = jwtDecode(response.token);
      console.log(response);
      dispatch(loginUserSuccess(response.token));
      dispatch(push('/admin/dataviews'));
    } catch (e) {
      dispatch(loginUserFailure({
        response: {
          status: 403,
          statusText: 'Invalid token',
        },
      }));
    }
  })
  .catch(error => {
    dispatch(loginUserFailure(error));
  });
}
