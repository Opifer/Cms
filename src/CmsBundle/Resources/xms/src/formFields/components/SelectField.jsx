import React, { PropTypes } from 'react';
import classNames from 'classnames';

const SelectField = (props) => {
  const { input, placeholder, label, labelClassName, options, meta: { touched, error } } = props;

  const classNameInputGroup = classNames({
    'input-group': true,
  });

  return (
    <div className={(touched && error) ? 'form-group pb-1 has-danger' : 'form-group pb-1'}>
      <label className={labelClassName}>{label}</label>
      <div className={classNameInputGroup}>
        <select
          {...input}
          className={`custom-select ${(touched && error) ? 'form-control form-control-danger' : 'form-control'}`}
        >
          {(placeholder && (
            <option value="">{placeholder}</option>
          ))}
          {Object.keys(options).map(key => (
            <option value={key} key={key}>{options[key]}</option>
          ))}
        </select>
        {touched && error && <div className="form-control-feedback">{error}</div>}
      </div>
    </div>
  );
};

SelectField.propTypes = {
  classNameInputGroup: PropTypes.string,
  input: PropTypes.object,
  label: PropTypes.string,
  labelClassName: PropTypes.string,
  meta: PropTypes.object,
  name: PropTypes.string,
  options: PropTypes.object,
  placeholder: PropTypes.string,
};

export default SelectField;
