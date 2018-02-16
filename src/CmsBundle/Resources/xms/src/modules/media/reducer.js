// import merge from 'lodash/merge';
// import isEqual from 'lodash/isEqual';
import * as t from './actionTypes';

const initialState = {
  isFetching: false,
  isUploading: false,
  items: [],
  directories: [],
  directory: null,
  results: 0,
  totalResults: null,
  resultsPerPage: null,
  maxUploadSize: null,
};

const media = (state = initialState, action) => {
  switch (action.type) {
    case t.START_FETCHING: {
      return {
        ...state,
        isFetching: true,
      };
    }
    case t.DONE_FETCHING: {
      return {
        ...state,
        isFetching: false,
      };
    }
    case t.START_UPLOADING: {
      return {
        ...state,
        isUploading: true,
      };
    }
    case t.SET_DATA: {
      return {
        ...state,
        ...action.data,
      };
    }
    case t.SET_DIRECTORY: {
      return {
        ...state,
        directory: action.directory,
      };
    }
    default: {
      return state;
    }
  }
};

export default media;
