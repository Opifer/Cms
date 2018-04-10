import React, { Component, PropTypes } from 'react';
import { connect } from 'react-redux';
import { Modal, ModalHeader, ModalBody } from 'reactstrap';
import MediaManager from './MediaManager';
import { getItems, setSelectedItems, setSelected } from '../actions';

class MediaPicker extends Component {
  static propTypes = {
    name: PropTypes.string,
    multiple: PropTypes.bool,
    value: PropTypes.oneOfType([PropTypes.string, PropTypes.array]),
  };

  static defaultProps = {
    multiple: false,
  }

  constructor(props) {
    super(props);
    this.state = { modal: false };

    this.toggle = this.toggle.bind(this);
    this.add = this.add.bind(this);
  }

  componentDidMount() {
    const { value } = this.props;
    if (value && value.length > 0) {
      this.props.fetchItems(value);
      this.props.setSelected(Array.isArray(value) ? value : [value]);
    }
  }

  toggle() {
    this.setState({ modal: !this.state.modal });
  }

  add(item) {
    const { selected } = this.props;
    selected.push(item.id);

    this.props.setSelected(selected);

    this.toggle();
  }

  remove(item) {
    const { selected } = this.props;

    selected.splice(selected.findIndex(i => i === item.id), 1);

    this.props.setSelected(selected);
  }

  render() {
    const { multiple, name, items } = this.props;

    return (
      <div>
        <div className="picker-selected-items">
          <div className="inner clearfix">
            {(items.length < 1 && (
              <div className="alert alert-info">
                no media selected
              </div>
            ))}
            <div>
              {items.map((item, i) => (
                <div key={i} className={`media media-${item.provider} media-${item.file_type}`}>
                  <div className="media-left media-top">
                    <div className="media-image">
                      <input type="hidden" name={name} value={item.id} />
                      {(item.provider === 'image') && (
                        <img src={item.images.medialibrary} className="media-object" alt={item.alt} />
                      )}
                      {(item.provider === 'youtube' || item.provider === 'vimeo') && (
                        <img src={item.thumb.images.medialibrary} className="media-object" alt={item.alt} />
                      )}
                      {(item.provider === 'file') && (
                        <div className="media-object media-file">
                          <i className="material-icons">description</i>
                        </div>
                      )}
                      
                      <div className="image-wrapper">
                        <div className="controls">
                          <a onClick={() => this.remove(item) } className="btn btn-close"></a>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div className="media-body">
                    <dl className="dl-horizontal">
                      <dt>Name</dt>
                      <dd>{item.name}</dd>
                      {(item.provider === 'image') && (<dt>Alt</dt>)}
                      {(item.provider === 'image') && (<dd>{item.alt}</dd>)}
                      {(item.provider === 'image') && (<dt>Dimensions</dt>)}
                      {(item.provider === 'image') && (<dd>{item.metadata.width}x{item.metadata.height}</dd>)}
                      <dt>Type</dt>
                      <dd>{item.content_type}</dd>
                      <dt>Filesize</dt>
                      <dd>{item.readable_filesize}</dd>
                    </dl>
                  </div>
                  {/*<div className="media-body" ng-show="item.formOpen">
                    <div className="form-group">
                      <input type="text" name="name" ng-model="item.name" className="form-control" />
                    </div>
                    <div className="form-group">
                      <input type="text" name="alt" ng-model="item.alt" className="form-control" placholder="alt" />
                    </div>
                    <a ng-click="saveMedia($index)" className="btn btn-primary">Save</a>
                  </div>*/}
                </div>
              ))}
              
            </div>
          </div>
        </div>

        {(multiple || items.length <= 1) && (
          <div onClick={this.toggle} className="btn btn-default">
            select media
          </div>
        )}

        <Modal
          isOpen={this.state.modal}
          toggle={this.toggle}
          className={this.props.className}
          size="lg"
        >
          <ModalHeader toggle={this.toggle}>
            Medialibrary
          </ModalHeader>
          <ModalBody>
            <MediaManager
              onPick={(item) => this.add(item)}
              picker
            />
          </ModalBody>
        </Modal>
      </div>
    );
  }
}

export default connect(
  (state, ownProps) => ({
    selected: state.media.selected,
    items: state.entities.medias ? state.media.selected.map(id => state.entities.medias[id]) : []
  }),
  (dispatch) => ({
    fetchItems: (ids) => dispatch(getItems({ ids })),
    setSelected: (ids) => dispatch(setSelected(ids))
  })
)(MediaPicker);
