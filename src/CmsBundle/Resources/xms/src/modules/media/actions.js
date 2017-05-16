import { normalize, Schema, arrayOf } from 'normalizr';
import { setEntities, updateEntity } from 'opifer-rcs/src/redux/entities';
import * as api from '../../utilities/api';
import * as t from './actionTypes';
import { currentDirectorySelector } from './selectors';

const mediaSchema = new Schema('medias');
const directorySchema = new Schema('directories');
directorySchema.define({
  children: arrayOf(directorySchema),
  items: arrayOf(mediaSchema),
});

export function startUploading() {
  return {
    type: t.START_UPLOADING,
  };
}

export function startFetching() {
  return {
    type: t.START_FETCHING,
  };
}

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

export function addItems(items) {
  return (dispatch) => {
    const normalizedItems = normalize(items, arrayOf(mediaSchema));

    dispatch(setEntities(normalizedItems));
  };
}

export function moveFile(file, dir) {
  return (dispatch) => {
    return api.put({ directory: dir }, `media/${file}`, dispatch)
      .then(media => {
        dispatch(updateEntity('medias', media.id, {
          directory_id: media.directory_id,
        }));
      });
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

export function uploadFiles(files, callback, errorCallback) {
  return (dispatch, getState) => {
    const body = new FormData();
    Object.keys(files).forEach(key => {
      body.append(key, files[key]);
    });

    const directory = currentDirectorySelector(getState());
    if (directory) {
      body.append('directory', directory.id);
    }

    return api.post(null, 'media/upload', dispatch, {}, { body })
      .then(response => {
        callback(response);
      })
      .catch(error => {
        errorCallback(error);
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
