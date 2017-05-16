import React, { Component, PropTypes } from 'react';
import { connect } from 'react-redux';

class DirectoryCreateItem extends Component {
  static propTypes = {
  };

  createDirectory() {
    console.log('CREATE DIRECTORY');
  }

  render() {

    return (
      <div className="item thumbnail" onClick={this.createDirectory}>
        <i className="fa fa-plus"></i>
      </div>
    );
  }
}

export default connect(
  null,
  (dispatch, ownProps) => ({
    // openDirectory: () => {
    //   console.log('OPEN DIRECTORY', ownProps.id);
    //   // dispatch();
    // }
  })
)(DirectoryCreateItem);
