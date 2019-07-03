import { get, del, put } from '../services/apiService';

export const getContents = (params, cancelToken) => get({
  url: '/admin/api/content',
  params,
  cancelToken: cancelToken.token
});

export const deleteContent = id => del({
  url: `/admin/api/content/${id}`
});

export const duplicateContent = data => put({
  url: '/admin/api/content/duplicate',
  data
});
