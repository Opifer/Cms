import React, { PropTypes } from 'react';
import classNames from 'classnames';
import CodeMirror from 'react-codemirror';
import 'codemirror/lib/codemirror.css';
import 'codemirror/mode/jsx/jsx';

const CodeField = (props) => {
  const { input, name, label, meta: { touched, error }, helpText } = props;

  const classNameInputGroup = classNames({
    'input-group': true,
  });

  return (
    <div className={(touched && error) ? 'form-group pb-1 has-danger' : 'form-group pb-1'}>
      <label>{label}</label>
      <div className={classNameInputGroup}>
        <CodeMirror
          options={{ lineNumbers: true, mode: 'jsx' }}
          {...input}
          name={name}
          // onChange={(value) => { props.change('view_code', value); }}
          className={(touched && error) ? 'form-control form-control-danger' : 'form-control'}
        />
      </div>
      {touched && error && <div className="form-control-feedback">{error}</div>}
      {helpText && <small className="form-text text-muted">{helpText}</small>}
    </div>
  );
};

CodeField.propTypes = {
  input: PropTypes.object,
  block: PropTypes.object,
  meta: PropTypes.object,
  name: PropTypes.string,
  label: PropTypes.string,
  unitOfMeasure: PropTypes.string,
  helpText: PropTypes.string,
};

export default CodeField;
