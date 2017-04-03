import * as t from './actionTypes';

const dataViews = (state = {
  isFetching: false,
  didInvalidate: false,
  items: [],
}, action) => {
  switch (action.type) {
    case t.INVALIDATE_DATAVIEWS:
      return Object.assign({}, state, {
        didInvalidate: true,
      });
    case t.REQUEST_DATAVIEWS:
      return Object.assign({}, state, {
        isFetching: true,
        didInvalidate: false,
      });
    case t.RECEIVE_DATAVIEWS:
      return Object.assign({}, state, {
        isFetching: false,
        didInvalidate: false,
        items: action.dataViews,
        lastUpdated: action.receivedAt,
      });
    default:
      return state;
  }
};

export default dataViews;
