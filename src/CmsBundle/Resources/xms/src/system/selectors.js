import { createSelector } from 'reselect';

const selectForm = (state, props) => {
  if (state.form[props.formName] === undefined) {
    return {
      submitting: false,
      submitSucceeded: false,
      submitFailed: false,
    };
  }

  return state.form[props.formName];
};

export const submitInProgress = createSelector(
  selectForm,
  form => form.submitting
);

export const submitSucceeded = createSelector(
  selectForm,
  form => !form.submitting && form.submitSucceeded
);

export const submitFailed = createSelector(
  selectForm,
  form => !form.submitting && form.submitFailed
);
