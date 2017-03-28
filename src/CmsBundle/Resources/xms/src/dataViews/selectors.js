import { createSelector } from 'reselect';
import * as c from './constants';

export const selectAll = state => state[c.NAME].items;
export const getEditId = (state, props) => props.params.id;

export const selectEditItem = createSelector(
  selectAll,
  getEditId,
  (items, id) => items.find(item => item.id.toString() === id)
);

export const getDisplayName = createSelector(
  selectEditItem,
  item => {
    return item ? item.display_name : '';
  }
);

