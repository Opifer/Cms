import React, { PropTypes } from 'react';
import { connect } from 'react-redux';
import { Link } from 'react-router';
import { Navbar, Nav, NavItem, NavLink } from 'reactstrap';
import MdDashboard from 'react-icons/lib/md/dashboard';

import '../stylesheets/fonts.scss';
import '../stylesheets/main.scss';

class App extends React.Component {
  render() {
    const { children, isAuthenticated } = this.props;

    // const id = params.id || 'index';
    if (!isAuthenticated) {
      return (
        <div>
          {children}
        </div>
      );
    }

    return (
      <div>
        <Navbar color="inverse" dark full>
          <Nav navbar>
            <NavItem>
              <NavLink href="/admin"><MdDashboard size={24} /></NavLink>
            </NavItem>
            <NavItem>
              <NavLink href="/admin/content">Content</NavLink>
            </NavItem>
            <NavItem>
              <NavLink href="/admin/media">Media</NavLink>
            </NavItem>
            <NavItem>
              <NavLink to="/admin/dataviews" tag={Link}>Data views</NavLink>
            </NavItem>
          </Nav>
        </Navbar>
        <div className="pt-2">
          {children}
        </div>
      </div>
    );
  }
}

App.propTypes = {
  params: PropTypes.object, // Passed by react-router
  children: PropTypes.object,
};

const mapStateToProps = (state) => ({
  isAuthenticated: state.user.isAuthenticated,
});

export default connect(mapStateToProps)(App);
