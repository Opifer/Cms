import React, { PropTypes } from 'react';
import classNames from 'classnames';

const TextField = (props) => {
  const { input, name, label, labelClassName, inputAttributes, meta: { touched, error }, helpText, unitOfMeasure } = props;

  const classNameInputGroup = classNames({
    'input-group': true,
    'input-group-has-addon': unitOfMeasure,
  });

  return (
    <div className={(touched && error) ? 'form-group pb-1 has-danger' : 'form-group pb-1'}>
      <label className={labelClassName}>{label}</label>
      <div className={classNameInputGroup}>
        <input
          type="text"
          {...input}
          {...inputAttributes}
          className={(touched && error) ? 'form-control form-control-danger' : 'form-control'}
          name={name}
        />
        {unitOfMeasure && <div className="input-group-addon">{unitOfMeasure}</div>}
      </div>
      {touched && error && <div className="form-control-feedback">{error}</div>}
      {helpText && <small className="form-text text-muted">{helpText}</small>}
    </div>
  );
};

TextField.propTypes = {
  input: PropTypes.object,
  block: PropTypes.object,
  meta: PropTypes.object,
  name: PropTypes.string,
  label: PropTypes.string,
  unitOfMeasure: PropTypes.string,
  helpText: PropTypes.string,
  labelClassName: PropTypes.string,
};

export default TextField;
