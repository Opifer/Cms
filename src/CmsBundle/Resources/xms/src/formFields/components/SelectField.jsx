import React, { PropTypes } from 'react';
import classNames from 'classnames';

const SelectField = (props) => {
  const { input, name, label, labelClassName, inputClassNames, inputAttributes, meta: { touched, error }, helpText, options } = props;

  const classNameInputGroup = classNames({
    'input-group': true,
  });

  const classNameFormControl = classNames({
    ...inputClassNames,
    'form-control-danger': (touched && error),
    'custom-select': true,
  });

  return (
    <div className={(touched && error) ? 'form-group pb-1 has-danger' : 'form-group pb-1'}>
      <label className={labelClassName}>{label}</label>
      <div className={classNameInputGroup}>
        <select
          {...input}
          {...inputAttributes}
          className={classNameFormControl}
          name={name}
        >
          {Object.keys(options).map(i => (
            <option value={options[i].value} key={i}>{options[i].text}</option>
          ))}
        </select>
      </div>
      {touched && error && <div className="form-control-feedback">{error}</div>}
      {helpText && <small className="form-text text-muted">{helpText}</small>}
    </div>
  );
};

  input: PropTypes.object,
  block: PropTypes.object,
  meta: PropTypes.object,
  name: PropTypes.string,
  label: PropTypes.string,
  options: PropTypes.object,
  helpText: PropTypes.string,
  labelClassName: PropTypes.string,
SelectField.propTypes = {
  inputAttributes: PropTypes.object,
  inputClassNames: PropTypes.object,
};

export default SelectField;
