/**
 * shrink images in the browser before they are uploaded.
 *
 * opt in on any file input:
 *   <input type="file" name="product_image_file" accept="image/jpeg, image/png, image/webp"
 *          data-resize-max="512"            longest side in px (keep in sync with the server)
 *          data-preview="#product_preview"> optional <img> to show the picked image in
 *
 * when a file is picked, it is redrawn on a <canvas> at most data-resize-max
 * pixels wide/high, and the smaller copy replaces the original in the input.
 * the form then submits as usual, so the server code doesn't change.
 *
 * why: phone photos are often 3-10 MB, over PHP's upload_max_filesize (2M) and
 * post_max_size (8M). an 800x600-ish JPEG is ~100 KB, so uploads are faster
 * and no longer rejected. the server still checks and resizes everything; this
 * is only a convenience, and if anything fails here the original file is sent.
 */
(() => {
  // file extensions for the formats the server accepts
  const EXTENSIONS = { 'image/jpeg': 'jpg', 'image/png': 'png', 'image/webp': 'webp' };
  const JPEG_QUALITY = 0.85; // 0-1; 0.85 looks the same as the original at a fraction of the size

  document.querySelectorAll('input[type="file"][data-resize-max]').forEach((input) => {
    const max_size = Number(input.dataset.resizeMax);
    const preview = input.dataset.preview ? document.querySelector(input.dataset.preview) : null;
    const original_preview_src = preview?.src; // to put back if the selection is cleared

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
    canvas.getContext('2d').drawImage(bitmap, 0, 0, canvas.width, canvas.height);
    bitmap.close(); // free the full-size image's memory straight away

    // keep the format (PNG keeps transparency); anything else becomes JPEG.
    // a browser that can't encode WebP (older Safari) silently gives PNG,
    // which the server also accepts
    const wanted_type = EXTENSIONS[file.type] ? file.type : 'image/jpeg';
    const blob = await new Promise((resolve, reject) =>
      canvas.toBlob((b) => (b ? resolve(b) : reject(new Error('canvas.toBlob failed'))), wanted_type, JPEG_QUALITY),
    );

    // name it after what it actually is now, e.g. "IMG_1234.jpg"
    const base_name = file.name.replace(/\.[^.]+$/, '');
    return new File([blob], `${base_name}.${EXTENSIONS[blob.type] ?? 'jpg'}`, {
      type: blob.type,
      lastModified: Date.now(),
    });
  }
})();
