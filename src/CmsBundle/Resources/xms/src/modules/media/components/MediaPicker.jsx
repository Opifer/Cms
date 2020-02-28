import React, { Component, PropTypes } from 'react';
import { connect } from 'react-redux';
import { Modal, ModalHeader, ModalBody } from 'reactstrap';
import MediaManager from './MediaManager';
import { getItems, setSelectedItems, setSelected, updateFile } from '../actions';

class MediaPicker extends Component {
  static propTypes = {
    ckeditor: PropTypes.object,
    name: PropTypes.string,
    multiple: PropTypes.bool,
    value: PropTypes.oneOfType([PropTypes.string, PropTypes.array]),
  };

  static defaultProps = {
    multiple: false,
  }

  constructor(props) {
    super(props);
    this.state = {
      manager: false,
      form: false,
      item: null,
    };

    this.toggleManager = this.toggleManager.bind(this);
    this.toggleForm = this.toggleForm.bind(this);
    this.handleSubmit = this.handleSubmit.bind(this);
    this.handleInputChange = this.handleInputChange.bind(this);
    this.add = this.add.bind(this);
  }

  componentDidMount() {
    const { value } = this.props;
    if (value && value.length > 0) {
      this.props.fetchItems(value);
      this.props.setSelected(Array.isArray(value) ? value : [value]);
    }
  }

  handleSubmit(event) {
    event.preventDefault();

    const { item } = this.state;

    this.props
      .updateFile(item.id, {
        name: item.name,
        alt: item.alt,
      })
      .then(() => this.toggleForm(null));
  }

  handleInputChange(event) {
    const target = event.target;
    const value = target.value;
    const name = target.name;

    const item = {
      ...this.state.item,
      [name]: value
    };

    this.setState({ item });
  }

  toggleManager() {
    this.setState({ manager: !this.state.manager });
  }

  toggleForm(item) {
    this.setState({
      item,
      form: !this.state.form
    });
  }

  addToCKEditor(item) {
    const { ckeditor: { type, funcNum } } = this.props;
    let location;
    // In case of images, do not pass the original file, but a cached/resized one.
    if (['image/png', 'image/jpeg', 'image/gif'].indexOf(item.contentType) > -1) {
      location = item.images.inline;
    } else {
      location = item.original;
    }

    // If the user is trying to add a link to the file, strip the protocol
    if (type == 'link') {
      location = location.replace(/.*?:\/\//g, '');
    }

    window.opener.CKEDITOR.tools.callFunction(funcNum, location, function() {
      // Get the reference to a dialog window.
      var element, dialog = this.getDialog();
      // Check if this is the Image dialog window.
      if ( dialog.getName() == 'image' ) {
        // Get the reference to a text field that holds the "alt" attribute.
        element = dialog.getContentElement('info', 'txtAlt');
        // Assign the new value.
        if ( element ) {
          element.setValue( item.name );
        }
      }
    });

    window.close();
  }

  add(item) {
    const { selected, ckeditor } = this.props;
    selected.push(item.id);

    this.props.setSelected(selected);

    this.toggleManager();

    if (ckeditor) {
      this.addToCKEditor(item);
    }
  }

  remove(item) {
    const { selected } = this.props;

    selected.splice(selected.findIndex(i => i === item.id), 1);

    this.props.setSelected(selected);
  }

  render() {
    const { multiple, name, items } = this.props;

    if (this.props.ckeditor) {
      return (
        <MediaManager
          onPick={(item) => this.add(item)}
          picker
        />
      );
    }

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
                    {!this.state.form && (
                      <dl className="dl-horizontal" onClick={() => this.toggleForm(item)}>
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
                    )}
                    {this.state.form && (
                      <form onSubmit={this.handleSubmit}>
                        <div className="form-group">
                          <input type="text" name="name" value={this.state.item.name} className="form-control" onChange={this.handleInputChange} />
                        </div>
                        <div className="form-group">
                          <input type="text" name="alt" value={this.state.item.alt} className="form-control" placeholder="alt" onChange={this.handleInputChange} />
                        </div>
                        <button type="submit" className="btn btn-primary">Save</button>
                      </form>
                    )}
                  </div>
                </div>
              ))}
            </div>
          </div>
        </div>

        {(multiple || items.length <= 1) && (
          <div onClick={this.toggleManager} className="btn btn-default">
            select media
          </div>
        )}

        <Modal
          isOpen={this.state.manager}
          toggle={this.toggleManager}
          className={this.props.className}
          size="lg"
        >
          <ModalHeader toggle={this.toggleManager}>
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
    items: state.entities.medias ? state.media.selected.map(id => state.entities.medias[id]).filter(m => m) : []
  }),
  (dispatch) => ({
    fetchItems: (ids) => dispatch(getItems({ ids })),
    setSelected: (ids) => dispatch(setSelected(ids)),
    updateFile: (id, data) => dispatch(updateFile(id, data)),
  })
)(MediaPicker);
