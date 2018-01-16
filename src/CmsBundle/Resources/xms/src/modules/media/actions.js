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

export function addDirectories(directories) {
  return (dispatch) => {
    const normalizedDirs = normalize(directories, arrayOf(directorySchema));

    dispatch(setEntities(normalizedDirs));
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

export function removeFile(file) {
  return (dispatch) => {
    return api.del(`media/${file}`, dispatch)
      .then(media => {
        console.log('TODO: REMOVE ENTITY');
        // dispatch(updateEntity('medias', media.id, {
        //   directory_id: media.directory_id,
        // }));
      });
  };
}

export function getItems(filters = null, refresh = false) {
  return (dispatch, getState) => {
    const queryString = api.objectToQueryParams(filters);

    return api
      .get(`media${queryString}`, dispatch)
      .then(response => {
        const normalizedItems = normalize(response.results, arrayOf(mediaSchema));
        const normalizedDirs = normalize(response.directories, arrayOf(directorySchema));

        dispatch(setEntities(normalizedItems));
        dispatch(setEntities(normalizedDirs));

        if (refresh) {
          dispatch(setData({
            resultsPerPage: response.results_per_page,
            totalResults: response.total_results,
            items: normalizedItems.result,
            directories: normalizedDirs.result,
          }));
        } else {
          const state = getState();
          dispatch(setData({
            resultsPerPage: response.results_per_page,
            totalResults: response.total_results,
            items: state.media.items.concat(normalizedItems.result),
            directories: state.media.directories.concat(normalizedDirs.result),
          }));
        }

        return response;
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

export function createDirectory(data) {
  return (dispatch, getState) => {
    const curDir = currentDirectorySelector(getState());
    if (curDir) {
      data.parent = curDir.id;
    }

    return api.post(data, 'directories', dispatch)
      .then(response => {
        dispatch(addDirectories([response]));
      })
      .catch(error => {

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
