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

(() => {
  const product_stock = document.querySelector("#product_stock");

  const decrease_button = document.querySelector("#decrease-button");
  const increase_button = document.querySelector("#increase-button")

  decrease_button.addEventListener('click', () => {
    if (product_stock.value === '' || Number.isNaN(product_stock.valueAsNumber)) product_stock.value = '0';
    product_stock.stepDown();
    product_stock.dispatchEvent(new Event('input', {bubbles: true}));
    product_stock.dispatchEvent(new Event('change', {bubbles: true}));
  });

  increase_button.addEventListener('click', () => {
    if (product_stock.value === '' || Number.isNaN(product_stock.valueAsNumber)) product_stock.value = '0';
    product_stock.stepUp();
    // we are dispatching events as though we actually changed the value
    product_stock.dispatchEvent(new Event('input', {bubbles: true}));
    product_stock.dispatchEvent(new Event('change', {bubbles: true}));
  });
})();

// burger menu: aria-expanded on the button is the state, CSS shows the nav when it's "true"
const nav_toggle = document.querySelector('.nav-toggle');

nav_toggle?.addEventListener('click', () => {
  const is_open = nav_toggle.getAttribute('aria-expanded') === 'true';
  nav_toggle.setAttribute('aria-expanded', String(!is_open));
});
