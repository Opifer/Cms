import React from 'react';
import { Route } from 'react-router';
import dataViews from './dataViews';
import auth from './auth';
import App from './containers/App';

const routes = (
  <Route path="/" component={App}>
    <Route path="/app_dev.php/admin/dataviews" component={auth.components.requireAuthentication(dataViews.components.DataViewIndex)} />
    <Route path="/app_dev.php/admin/dataviews/:id" component={auth.components.requireAuthentication(dataViews.components.DataViewForm)} />
    <Route path="/app_dev.php/admin/login" component={auth.components.Login} />
    <Route path="/admin/dataviews" component={auth.components.requireAuthentication(dataViews.components.DataViewIndex)} />
    <Route path="/admin/dataviews/:id" component={auth.components.requireAuthentication(dataViews.components.DataViewForm)} />
    <Route path="/admin/login" component={auth.components.Login} />
  </Route>
);

export default routes;
