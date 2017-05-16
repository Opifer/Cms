import { normalize, Schema, arrayOf } from 'normalizr';
import { setEntities } from 'opifer-rcs/src/redux/entities';
import * as api from '../../utilities/api';
import * as t from './actionTypes';

const mediaSchema = new Schema('medias');
const directorySchema = new Schema('directories');
directorySchema.define({
  children: arrayOf(directorySchema),
  items: arrayOf(mediaSchema),
});

export function setItems(items) {
  return {
    type: t.SET_ITEMS,
    items,
  };
}

export function setData(data) {
  return {
    type: t.SET_DATA,
    data,
  };
}

export function getItems() {
  return (dispatch) => {
    return api.get('media', dispatch)
      .then(json => {
        const normalizedItems = normalize(json.results, arrayOf(mediaSchema));
        const normalizedDirs = normalize(json.directories, arrayOf(directorySchema));

        dispatch(setEntities(normalizedItems));
        dispatch(setEntities(normalizedDirs));

        dispatch(setData({
          resultsPerPage: json.results_per_page,
          totalResults: json.total_results,
        }));
      });
  };
}

export function setDirectory(directory) {
  return {
    type: t.SET_DIRECTORY,
    directory,
  };
}

export function switchDirectory(directory) {
  return (dispatch) => {
    dispatch(setDirectory(directory));
  };
}
