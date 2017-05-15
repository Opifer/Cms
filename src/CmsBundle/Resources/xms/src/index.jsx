import React from 'react';
import { render } from 'react-dom';
import { AppContainer } from 'react-hot-loader';
import { browserHistory } from 'react-router';
import { syncHistoryWithStore } from 'react-router-redux';
import store from './redux/store';
import Root from './containers/Root';
import { loginUserSuccess } from './auth/actions';

const element = document.getElementById('app');
console.log('XMS>', element);
if (element) {
  const history = syncHistoryWithStore(browserHistory, store);

  const token = localStorage.getItem('token');
  if (token !== null) {
    store.dispatch(loginUserSuccess(token));
  }

  render(
    <AppContainer>
      <Root store={store} history={history} />
    </AppContainer>,
    element
  );


  const RootComponent = require('./containers/Root').default;

  if (module.hot) {
    module.hot.accept(RootComponent, () => {
      render(
        <AppContainer>
          <RootComponent store={store} history={history} />
        </AppContainer>,
        element
      );
    });
  }
}
