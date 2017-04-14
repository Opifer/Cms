import { combineReducers } from 'redux';
import { reducer as reduxForm } from 'redux-form';
// import reducers from 'opifer-rcs/src/redux/reducers';
import { routerReducer } from 'react-router-redux';
import auth from '../auth';
import dataViews from '../dataViews';

const reducer = combineReducers({
  form: reduxForm,
  routing: routerReducer,
  [dataViews.constants.NAME]: dataViews.reducer,
  user: auth.reducer,
});

export default reducer;
