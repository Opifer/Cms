import { push } from 'react-router-redux';
import { checkHttpStatus } from './index';
import { loginUserFailure } from '../auth/actions';

const defaultHeaders = {
  'Content-Type': 'application/json',
};

export function put(values, url, dispatch) {
  const token = localStorage.getItem('token');
  return fetch(`/app_dev.php/admin/api/${url}`, {
    credentials: 'include',
    method: 'put',
    headers: {
      Authorization: `Bearer ${token}`,
      'Content-Type': 'application/json',
    },
    body: JSON.stringify(values),
  })
  .then(checkHttpStatus)
  .then(response => response.json())
  .then(json => Promise.resolve(json))
  .catch(error => {
    if (error.response.status === 401) {
      dispatch(loginUserFailure(error));
      dispatch(push('/admin/login'));
    }
    return error.response.json().then(json => Promise.reject(json));
  });
}

export function post(values, url, dispatch, headers = defaultHeaders, options = {}) {
  const token = localStorage.getItem('token');
  return fetch(`/app_dev.php/admin/api/${url}`, {
    credentials: 'include',
    method: 'post',
    headers: {
      Authorization: `Bearer ${token}`,
      ...headers,
    },
    body: JSON.stringify(values),
    ...options,
  })
  .then(checkHttpStatus)
  .then(response => response.json())
  .then(json => Promise.resolve(json))
  .catch(error => {
    if (error.response.status === 401) {
      dispatch(loginUserFailure(error));
      dispatch(push('/admin/login'));
    }
    return error.response.json().then(json => Promise.reject(json));
  });
}

export function get(url, dispatch) {
  const token = localStorage.getItem('token');
  return fetch(`/app_dev.php/admin/api/${url}`, {
    credentials: 'include',
    headers: {
      Authorization: `Bearer ${token}`,
    },
  })
  .then(checkHttpStatus)
  .then(response => response.json())
  .catch(error => {
    if (error.response.status === 401) {
      dispatch(loginUserFailure(error));
      dispatch(push('/admin/login'));
    }
    return error.response.json().then(json => Promise.reject(json));
  });
}
