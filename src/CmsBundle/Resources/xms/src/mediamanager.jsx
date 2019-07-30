import React from 'react';
import { render } from 'react-dom';
import { Provider } from 'react-redux';
import { AppContainer } from 'react-hot-loader';
import { browserHistory } from 'react-router';
import { syncHistoryWithStore } from 'react-router-redux';
import createStore from './redux/store';
import MediaManager from './modules/media/components/MediaManager';
import { loginUserSuccess } from './auth/actions';

const elements = document.getElementsByClassName('js-media-manager');

Object.keys(elements).forEach(i => {
  const store = createStore();
  const history = syncHistoryWithStore(browserHistory, store);

  const token = localStorage.getItem('token');
  if (token !== null) {
    store.dispatch(loginUserSuccess(token));
  }

  const element = elements[i];

  render(
    <Provider store={store}>
      <MediaManager />
    </Provider>,
    element
  );

  if (module.hot) {
    const RootComponent = require('./modules/media/components/MediaManager').default;

    module.hot.accept(RootComponent, () => {
      render(
        <AppContainer>
          <RootComponent store={store} history={history} />
        </AppContainer>,
        element
      );
    });
  }
});
