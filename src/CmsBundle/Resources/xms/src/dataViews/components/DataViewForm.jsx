import React, { Component, PropTypes } from 'react';
import { connect } from 'react-redux';
import { Field, FieldArray, reduxForm } from 'redux-form';

import { Header, FormActions } from '../../system/components';
import { CodeField, TextField, CheckboxField, SelectField } from '../../formFields/components';
import { fetchDataViewsIfNeeded, saveDataView } from '../actions';
import { selectEditItem, getDisplayName, getEditId } from '../selectors';
import * as c from '../constants';

const viewPlaceholders = ({ fields, meta: { touched, error } }) => (
  <div>
    {fields.map((placeholder, index) =>
      <div className="form-inline" key={index}>
        <div className="form-group">
          <Field
            name={`${placeholder}.name`}
            type="text"
            component={TextField}
            label="Name"
            inputAttributes={{ placeholder: 'Name', size: 16 }}
            labelClassName="sr-only"
          />{' '}
          <button
            type="button"
            className="btn btn-link text-danger btn-sm"
            onClick={() => fields.remove(index)}
          >
            Remove
          </button>
        </div>
      </div>
    )}
    <div className="row mb-2">
      <div className="col-xs-12">
        <button type="button" onClick={() => fields.push({})} className="btn btn-sm btn-outline-primary">Add placeholder</button>
        {touched && error && <span>{error}</span>}
      </div>
    </div>
  </div>
);

const optionFields = ({ fields, meta: { touched, error } }) => (
  <div className="pl-2">
    {fields.map((field, index) =>
      <div className="form-inline" key={index}>
        <div className="form-group">
          <Field
            name={`${field}.key`}
            type="text"
            component={TextField}
            label="Key"
            inputAttributes={{ placeholder: 'Key' }}
            labelClassName="sr-only"
          />{' '}
          <Field
            name={`${field}.value`}
            type="text"
            component={TextField}
            label="Value"
            inputAttributes={{ placeholder: 'Value' }}
            labelClassName="sr-only"
          />{' '}
          <button
            type="button"
            className="btn btn-link text-danger btn-sm"
            onClick={() => fields.remove(index)}
          >
            Remove
          </button>
        </div>
      </div>
    )}
    <div className="row mb-2">
      <div className="col-xs-12">
        <button type="button" onClick={() => fields.push({})} className="btn btn-sm btn-outline-primary">Add option</button>
        {touched && error && <span>{error}</span>}
      </div>
    </div>
  </div>
);     

const viewFields = ({ fields, meta: { touched, error } }) => (
  <div>
    {fields.map((field, index) =>
      <div className="form-inline" key={index}>
        <div className="form-group">
          <Field
            name={`${field}.type`}
            component={SelectField}
            options={{
              text: 'Text',
              number: 'Number',
              textarea: 'Textarea',
              checkbox: 'Checkbox',
              html: 'HTML',
              select: 'Select',
              media: 'Media',
              contentItem: 'Content Item',
              contentItems: 'Content Items',
            }}
            label="Type"
            inputAttributes={{ placeholder: 'Type' }}
            className="form-control"
            labelClassName="sr-only"
          />{' '}
          <Field
            name={`${field}.name`}
            type="text"
            component={TextField}
            label="Name"
            inputAttributes={{ placeholder: 'Name' }}
            labelClassName="sr-only"
          />{' '}
          <Field
            name={`${field}.display_name`}
            type="text"
            component={TextField}
            label="Display name"
            inputAttributes={{ placeholder: 'Display name' }}
            labelClassName="sr-only"
          />{' '}
          <Field
            name={`${field}.sort`}
            type="text"
            component={TextField}
            label="Sort"
            inputAttributes={{ placeholder: 'Sort', size: 5 }}
            labelClassName="sr-only"
          />{' '}
          <button
            type="button"
            className="btn btn-link text-danger btn-sm"
            onClick={() => fields.remove(index)}
          >
            Remove
          </button>
          {fields.get(index).type === 'select' && (
            <FieldArray
              name={`${field}.options`}
              component={optionFields}
            />
          )}
        </div>
      </div>
    )}
    <div className="row mb-2">
      <div className="col-xs-12">
        <button type="button" onClick={() => fields.push({})} className="btn btn-sm btn-outline-primary">Add field</button>
        {touched && error && <span>{error}</span>}
      </div>
    </div>
  </div>
);

class DataViewForm extends Component {
  componentDidMount() {
    this.props.dispatch(fetchDataViewsIfNeeded());
  }

  render() {
    const { handleSubmit, displayName, id } = this.props;

    return (
      <div className="col-xs-12 col-md-6 offset-md-3">
        <Header title={id !== '0' ? displayName : 'New'} secondaryText={'Dataviews'} formName={c.NAME} />
        <form onSubmit={handleSubmit}>
          <Field name="active" component={CheckboxField} label="Active" helpText="Inactive dataviews are not show in the tool section. Warning: inactive dataviews do show up as content!" />
          <Field name="name" component={TextField} label="Name" helpText="For application purposes: use only letters and numbers, no spaces allowed a-z0-9_" />
          <Field name="display_name" component={TextField} label="Display name" helpText="Assign the dataview a useful human-readable name to distinguish this" />
          <Field name="description" component={TextField} label="Description" helpText="Short single line description of what this dataview does" />
          <Field name="icon_type" component={TextField} label="Icon Type" helpText="Material icon abbrevation like 'check' or 'developer_board'" />
          <Field name="view_reference" component={CheckboxField} label="Reference" helpText="This data view refers to like-named component in block map" />
          <Field name="view_code" component={CodeField} label="Code view" />
          <h4>Placeholders</h4>
          <FieldArray name="placeholders" component={viewPlaceholders} />
          <h4>Fields</h4>
          <FieldArray name="fields" component={viewFields} />
          <FormActions formName={c.NAME} cancelLink="/admin/dataviews" />
        </form>
      </div>
    );
  }
}

DataViewForm.propTypes = {
  dispatch: PropTypes.func.isRequired,
  handleSubmit: PropTypes.func.isRequired,
  params: PropTypes.object,
  initialValues: PropTypes.object,
  change: PropTypes.func.isRequired,
  displayName: PropTypes.string,
  id: PropTypes.string,
};

const mapStateToProps = (state, ownProps) => ({
  initialValues: selectEditItem(state, ownProps),
  displayName: getDisplayName(state, ownProps),
  id: getEditId(state, ownProps),
});

export default connect(mapStateToProps)(reduxForm({
  form: c.NAME,
  onSubmit: saveDataView,
})(DataViewForm));
