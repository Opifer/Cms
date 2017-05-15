import merge from 'lodash/merge';
import isEqual from 'lodash/isEqual';
import * as t from './actionTypes';

const initialState = {
  isFetching: true,
  items: {},
  directories: {},
  directory: null,
  filters: {},
};

const media = (state = initialState, action) => {
  switch (action.type) {
    case t.SET_ITEMS: {
      const updatedState = merge({}, state.items, action.items);
      return isEqual(updatedState, state.items) ? state : {
        items: updatedState,
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
