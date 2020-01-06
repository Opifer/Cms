import React, { PropTypes } from 'react';
import { FormGroup, Label, Input, FormFeedback, FormText } from 'reactstrap';

const CheckboxField = (props) => {
  const { input, name, label, meta: { touched, error }, helpText } = props;

  return (
    <FormGroup check color={(touched && error) ? 'danger' : null}>
      <Label check className="custom-control custom-checkbox">
        <Input type="checkbox" {...input} name={name} checked={input.value && 'checked'} state={(touched && error) ? 'danger' : null} className="custom-control-input" />{' '}
        <span className="custom-control-indicator" />
        <span className="custom-control-description">{label}</span>
      </Label>
      {touched && error && <FormFeedback>{error}</FormFeedback>}
      {helpText && <FormText color="muted">{helpText}</FormText>}
    </FormGroup>
  );
};

CheckboxField.propTypes = {
  input: PropTypes.object,
  meta: PropTypes.object,
  name: PropTypes.string,
  label: PropTypes.string,
  helpText: PropTypes.string,
};

export default CheckboxField;
