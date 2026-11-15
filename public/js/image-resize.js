/**
 * shrink images in the browser before they are uploaded.
 */

// we are putting all of this in a closure to make sure that it is scoped,so constants like JPEG_QUALITY
// don't leak out into the global namespace
(() => {
  const JPEG_QUALITY = 0.85;

  document.querySelectorAll('input[type="file"][data-resize-max]').forEach((input) => {

    /* https://developer.mozilla.org/en-US/docs/Web/API/HTMLElement/dataset#in_javascript, gives an
     * explanation as to how to use the data- elements in the browser
     */
    const max_size = Number(input.dataset.resizeMax);
    const preview = input.dataset.preview ? document.querySelector(input.dataset.preview) : null;

    // useful for when we actually no longer an image
    const original_preview_src = preview?.src;

    input.addEventListener('change', async () => {
      const file = input.files[0];

      if (!file) {
        if (preview) preview.src = original_preview_src;

        return;
      }

      // block submitting while we work, so the original isn't sent by accident
      const buttons = input.form?.querySelectorAll('button[type="submit"]') ?? [];
      buttons.forEach((button) => (button.disabled = true));

      try {
        const resized = await resize_image(file, max_size);

        if (resized !== file) {
          // an <input type="file">'s files can't be edited directly, but can be
          // replaced with a new FileList built through DataTransfer.
          // (assigning .files does not fire "change" again, so no loop)

          const transfer = new DataTransfer();
          transfer.items.add(resized);
          input.files = transfer.files;
        }

        if (preview) preview.src = URL.createObjectURL(input.files[0]);
      } catch (error) {
        // e.g. a format this browser can't decode: keep the original file and
        // let the server decide whether to accept it
        console.warn('image resize skipped:', error);
        if (preview) preview.src = URL.createObjectURL(file);
      } finally {
        buttons.forEach((button) => (button.disabled = false));
      }
    });
  });

  /**
   * @param {File} file the picked image
   * @param {number} max_size longest side in px
   * @returns {Promise<File>} a smaller copy, or the same file if it is already small enough
   */
  async function resize_image(file, max_size) {
    // createImageBitmap applies the photo's EXIF rotation, so portrait phone
    // photos don't come out sideways
    const bitmap = await createImageBitmap(file, { imageOrientation: 'from-image' });

    const scale = max_size / Math.max(bitmap.width, bitmap.height);
    if (scale >= 1) {
      bitmap.close();
      return file; // already small enough: send it untouched
    }

    const canvas = document.createElement('canvas');
    canvas.width = Math.round(bitmap.width * scale);
    canvas.height = Math.round(bitmap.height * scale);
    // refer to https://developer.mozilla.org/en-US/docs/Web/API/CanvasRenderingContext2D/drawImage
    // and specifically dWidth and dHeight, using this allows the product to be scaled
    canvas.getContext('2d').drawImage(bitmap, 0, 0, canvas.width, canvas.height);
    bitmap.close(); // free the full-size image's memory straight away

    // we are always encoding as a JPEG
    /** @type {Blob} */
    const blob = await new Promise((resolve, reject) =>
      canvas.toBlob(
        (blob) => {
          (blob ? resolve(blob) : reject(new Error('canvas.toBlob failed')))
        },
        'image/jpeg',
        JPEG_QUALITY
      ),
    );

    // if the name is "IMAGE.png" strip the .png
    const base_name = file.name.replace(/\.[^.]+$/, ''); return new File(
      [blob],
      `${base_name}.jpg`,
      {
        type: blob.type,
        lastModified: Date.now(),
      }
    );
  }
})();
