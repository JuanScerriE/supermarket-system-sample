// shared behaviour for every page (loaded once, with `defer`, from layout.php).
// each part is opt-in through an attribute in the HTML, and every page still
// works without JavaScript; this only makes things nicer.

// ---------- .product-image: show the placeholder if an image fails to load ----------
// the server already redirects products without an image to the placeholder;
// this covers anything else (server error, connection dropped...)
{
  const PLACEHOLDER = '/assets/no_image_available.jpg';

  const use_placeholder = (img) => {
    if (!img.src.endsWith(PLACEHOLDER)) img.src = PLACEHOLDER; // never loop on the placeholder itself
  };

  // images that already failed before this script ran (complete, but 0px wide)
  document.querySelectorAll('img.product-image').forEach((img) => {
    if (img.complete && img.naturalWidth === 0 && img.getAttribute('src')) use_placeholder(img);
  });

  // images that fail later (lazy-loaded ones further down the page). "error"
  // doesn't bubble, so listen in the capture phase on the whole document
  document.addEventListener('error', (event) => {
    if (event.target instanceof HTMLImageElement && event.target.matches('.product-image')) {
      use_placeholder(event.target);
    }
  }, true);
}

// ---------- data-confirm="question": ask before submitting (deletes) ----------
document.querySelectorAll('form[data-confirm]').forEach((form) => {
  form.addEventListener('submit', (event) => {
    if (!confirm(form.dataset.confirm)) {
      event.preventDefault();
    }
  });
});

// ---------- data-stepper: big − / + buttons around a number input ----------
// <input type="number" min="0" data-stepper>  becomes  [ − ] [ 12 ] [ + ]
// without JavaScript it stays a normal number input
document.querySelectorAll('input[type="number"][data-stepper]').forEach((input) => {
  const wrapper = document.createElement('div');
  wrapper.className = 'stepper';
  input.replaceWith(wrapper);

  const make_button = (text, label, step) => {
    const button = document.createElement('button');
    button.type = 'button';               // not "submit": pressing it must not send the form
    button.className = 'button button-secondary';
    button.textContent = text;
    button.setAttribute('aria-label', label);
    button.addEventListener('click', () => {
      // an empty or invalid field counts as 0
      if (input.value === '' || Number.isNaN(input.valueAsNumber)) input.value = '0';
      // stepUp/stepDown respect min="0" and step, so stock never goes negative
      step > 0 ? input.stepUp() : input.stepDown();
      // we are dispatching events as though we actually changed the value
      input.dispatchEvent(new Event('input', { bubbles: true }));
      input.dispatchEvent(new Event('change', { bubbles: true }));
    });
    return button;
  };

  wrapper.append(
    make_button('−', 'Decrease by 1', -1),
    input,
    make_button('+', 'Increase by 1', +1),
  );
});

// burger menu: aria-expanded on the button is the state, CSS shows the nav when it's "true"
const nav_toggle = document.querySelector('.nav-toggle');

nav_toggle?.addEventListener('click', () => {
  const is_open = nav_toggle.getAttribute('aria-expanded') === 'true';
  nav_toggle.setAttribute('aria-expanded', String(!is_open));
});
