import { get } from '../services/apiService';

export const getContents = params => get({
  url: '/admin/api/content',
  params
});
