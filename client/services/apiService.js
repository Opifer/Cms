import axios from 'axios';


export const api = axios.create({
  baseURL: process.env.NODE_ENV === 'production' ? undefined : '/app_dev.php',
});

export const get = config => api({
  method: 'GET',
  withCredentials: true,
  ...config,
});

export const put = config => api({
  method: 'PUT',
  withCredentials: true,
  ...config,
});

export const post = config => api({
  method: 'POST',
  withCredentials: true,
  ...config,
});

export const remove = config => api({
  method: 'DELETE',
  withCredentials: true,
  ...config,
});
