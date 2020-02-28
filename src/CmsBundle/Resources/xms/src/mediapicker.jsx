import React from 'react';
import { render } from 'react-dom';
import { Provider } from 'react-redux';
import { AppContainer } from 'react-hot-loader';
import { browserHistory } from 'react-router';
import { syncHistoryWithStore } from 'react-router-redux';
import createStore from './redux/store';
import MediaPicker from './modules/media/components/MediaPicker';
import { loginUserSuccess } from './auth/actions';

window.initializeMediaPicker = () => {
  const elements = document.getElementsByClassName('js-media-picker');

  Object.keys(elements).forEach(i => {
    const store = createStore();
    const history = syncHistoryWithStore(browserHistory, store);

    const token = localStorage.getItem('token');
    if (token !== null) {
      store.dispatch(loginUserSuccess(token));
    }

    const element = elements[i];
    const props = JSON.parse(element.dataset.props);

    render(
      <Provider store={store}>
        <MediaPicker {...props} />
      </Provider>,
      element
    );

    if (module.hot) {
      const RootComponent = require('./modules/media/components/MediaPicker').default;

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
};
