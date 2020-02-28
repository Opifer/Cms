// @babel/polyfill is disabled for now since we already have another instance of it available.
// import '@babel/polyfill';
import React from 'react';
import ReactDOM from 'react-dom';
import { AppContainer } from 'react-hot-loader';
import ContentBrowser from './pages/ContentBrowser';

const elements = document.getElementsByClassName('js-content-browser');

Object.keys(elements).forEach((i) => {
  const element = elements[i];

  const render = (Component) => {
    ReactDOM.render(
      <AppContainer>
        <Component />
      </AppContainer>,
      element
    );
  };

  render(ContentBrowser);

  if (module.hot) {
    module.hot.accept('./pages/ContentBrowser', () => {
      render(require('./pages/ContentBrowser')); // eslint-disable-line global-require
    });
  }
});
