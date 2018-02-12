import createLogger from 'redux-logger';
import thunkMiddleware from 'redux-thunk';
import { createStore, applyMiddleware, compose } from 'redux';
import { routerMiddleware } from 'react-router-redux';
import { browserHistory } from 'react-router';
// import eventHandler from 'opifer-rcs/src/middleware/events';
import reducer from './reducer';

let middleware = [
  thunkMiddleware,
  routerMiddleware(browserHistory),
];

if (process.env.NODE_ENV !== 'production') {
  const loggerMiddleware = createLogger();
  middleware = [...middleware, loggerMiddleware];
}

const store = createStore(
  reducer,
  compose(
    applyMiddleware(...middleware),
    process.env.NODE_ENV !== 'production' && window.devToolsExtension ? window.devToolsExtension() : f => f
  )
);

export default store;
