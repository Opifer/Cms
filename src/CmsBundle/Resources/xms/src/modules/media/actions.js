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

export function doneFetching() {
  return {
    type: t.DONE_FETCHING,
  };
}

export function setItems(items) {
  return {
    type: t.SET_ITEMS,
    items,
  };
}

export function setSelected(selected) {
  return {
    type: t.SET_SELECTED,
    selected,
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

    return dispatch(setEntities(normalizedDirs));
  };
}

export function addItems(items) {
  return (dispatch, getState) => {
    const state = getState();
    if (!items.length) {
      return [];
    }

    const normalizedItems = normalize(items, arrayOf(mediaSchema));

    dispatch(setData({
      totalResults: state.media.totalResults + items.length,
      results: state.media.results + items.length,
      items: normalizedItems.result.concat(state.media.items),
    }));

    return dispatch(setEntities(normalizedItems));
  };
}

export function moveFile(file, dir) {
  return (dispatch) =>
    api.put({ directory: dir }, `media/${file}`, dispatch)
      .then(media => {
        dispatch(updateEntity('medias', media.id, {
          directory_id: media.directory_id,
        }));
      });
}

export function removeFile(file) {
  return (dispatch, getState) =>
    api.del(`media/${file}`, dispatch)
      .then(media => {
        const state = getState();

        const index = state.media.items.indexOf(file);

        if (index !== -1) {
          return dispatch(setData({
            totalResults: state.media.totalResults - 1,
            results: state.media.results - 1,
            items: state.media.items.filter(i => i !== file),
          }));
        }

        return media;
      });
}

export function getItems(filters = {}, refresh = false) {
  return (dispatch, getState) => {
    const state = getState();
    if (state.media.isFetching) {
      return Promise.resolve();
    }

    dispatch(startFetching());

    let page = 1;
    if (state.media.totalResults && refresh === false) {
      if (state.media.results >= state.media.totalResults) {
        return Promise.resolve();
      }

      page = Math.ceil(state.media.results / state.media.resultsPerPage) + 1;
    }

    filters.page = page;

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
            results: response.results.length,
            items: normalizedItems.result,
            directories: normalizedDirs.result,
            maxUploadSize: response.max_upload_size,
          }));
        } else {
          dispatch(setData({
            resultsPerPage: response.results_per_page,
            totalResults: response.total_results,
            results: state.media.results + response.results.length,
            items: state.media.items.concat(normalizedItems.result),
            directories: state.media.directories.concat(normalizedDirs.result),
          }));
        }

        dispatch(doneFetching());

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

    return api.post(null, 'media', dispatch, {}, { body })
      .then(response => {
        callback(response);
        return response;
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
      .then(response => dispatch(addDirectories([response])))
      .catch(error => console.error('Could not create directory', error));
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
    // Switch the directory
    dispatch(setDirectory(directory));

    // And fetch the items for this directory
    return dispatch(getItems(directory ? { directory } : undefined, true));
  };
}
