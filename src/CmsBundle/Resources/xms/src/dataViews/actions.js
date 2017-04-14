import { SubmissionError } from 'redux-form';
import * as t from './actionTypes';
import * as api from '../utilities/api';


export function invalidateDataViews(dataViews) {
  return {
    type: t.INVALIDATE_DATAVIEWS,
    dataViews,
  };
}

function requestDataViews(dataViews) {
  return {
    type: t.REQUEST_DATAVIEWS,
    dataViews,
  };
}

function receiveDataViews(dataViews) {
  return {
    type: t.RECEIVE_DATAVIEWS,
    dataViews,
    receivedAt: Date.now(),
  };
}

function fetchDataViews() {
  return (dispatch) => {
    dispatch(requestDataViews());
    return api.get('dataviews', dispatch).then(json => dispatch(receiveDataViews(json)));
  };
}

function shouldFetchDataViews(state) {
  const dataViews = state.dataViews;
  if (!dataViews) {
    return true;
  } else if (dataViews.isFetching) {
    return false;
  }
  return dataViews.didInvalidate;
}

export function fetchDataViewsIfNeeded() {
  return (dispatch, getState) => {
    if (shouldFetchDataViews(getState())) {
      return dispatch(fetchDataViews());
    }
  };
}

export function saveDataView(values, dispatch) {
  return api.put(values, 'dataviews', dispatch)
  .then(json => console.log('success', json))
  .catch(error => {
    throw new SubmissionError(error.errors);
  });
}
