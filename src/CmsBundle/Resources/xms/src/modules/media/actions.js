import { normalize, Schema, arrayOf } from 'normalizr';
import * as api from '../../utilities/api';
import * as t from './actionTypes';

const mediaSchema = new Schema('medias');

export function setItems(items) {
  return {
    type: t.SET_ITEMS,
    items,
  }
}

export function getItems() {
  return (dispatch, getState) => {
    return api.get('media', dispatch)
      .then(json => {
        const normalized = normalize(json.results, arrayOf(mediaSchema));

        dispatch(setItems(normalized.entities.medias));
      });
  };
}
