import React, { PropTypes, Component } from 'react';
import { Provider } from 'react-redux';
import { Router } from 'react-router';
import routes from '../routes';

class Root extends Component {
  static propTypes = {
    store: PropTypes.object,
    history: PropTypes.object,
  };

  render() {
    const { store, history } = this.props;

    return (
      <Provider store={store}>
        <Router history={history}>
          {routes}
        </Router>
      </Provider>
    );
  }
}

export default Root;
