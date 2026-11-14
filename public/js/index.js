// forms marked with data-auto-submit submit as soon as a <select> changes,
// so the user doesn't have to press the button. without JavaScript the
// button is still there and the form works the same way.
document.querySelectorAll('form[data-auto-submit]').forEach((form) => {
  // form.querySelectorAll('button[type="submit"]').forEach((button) => {
  //   button.hidden = true;
  // });

  form.addEventListener('change', (event) => {
    if (event.target instanceof HTMLSelectElement) {
      form.requestSubmit(); // unlike submit(), this runs validation (e.g. required)
    }
  });
});
