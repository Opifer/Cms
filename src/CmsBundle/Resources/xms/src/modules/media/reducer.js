import merge from 'lodash/merge';
import isEqual from 'lodash/isEqual';
import * as t from './actionTypes';

const initialState = {
  isFetching: true,
  items: [],
  directories: [],
  directory: null,
  // filters: {},
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
    case t.SET_ITEMS: {
      const updatedState = merge({}, state.items, action.items);
      return isEqual(updatedState, state.items) ? state : {
        items: updatedState,
        isFetching: false,
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
    case t.SET_DIRECTORIES: {
      const updatedState = merge({}, state.directories, action.directories);
      return isEqual(updatedState, state.directories) ? state : {
        items: updatedState,
      };
    }
    default: {
      return state;
    }
  }
}

export default media;
