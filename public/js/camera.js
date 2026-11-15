/**
 * take photo from camera on laptop, note this is not available over http, must be https, or a development
 * environment
 */

(() => {
  const take_photo = document.querySelector("#take-photo");
  const photo_modal = document.querySelector("#photo-modal");
  const photo_modal_close_button = document.querySelector("#photo-modal-close");
  const video = document.querySelector('#camera');
  const snap = document.querySelector('#camera-snap');
  const input = document.querySelector('#product_image_file');

  if (!take_photo ||
    !photo_modal ||
    !photo_modal_close_button ||
    !video ||
    !snap ||
    !input
  ) {
    return;
  }

  // browsers without camera support (or plain http): hide the button, the file input still works
  if (!navigator.mediaDevices?.getUserMedia) { take_photo.style.display = "none"; return; }

  let stream = null;


  take_photo.addEventListener("click", async () => {
    try {
      stream = await navigator.mediaDevices.getUserMedia({
        // video: { facingMode: 'user', width: { ideal: 1280 }, height: { ideal: 1280 } }, // 'user' = selfie camera
        video: { width: { ideal: 1280 }, height: { ideal: 1280 } }, // 'user' = selfie camera
        audio: false,
      });
      video.srcObject = stream;
      await video.play();
    } catch (error) {
      // NotAllowedError = user said no, NotFoundError = no camera
      alert(`Couldn't open the camera: ${error.name}`);
      return;
    }

    photo_modal.showModal();
    // block from scrolling the old the whole webpage
    document.body.style.overflow = 'hidden';
  });

  photo_modal_close_button.addEventListener("click", () => {
    photo_modal.returnValue = ""; // reset return value
    photo_modal.close();
  });

  photo_modal.addEventListener("close", () => {
    document.body.style.overflow = 'auto';
  });


  snap.addEventListener('click', async () => {
    // draw the current video frame onto a canvas
    const canvas = document.createElement('canvas');
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    canvas.getContext('2d').drawImage(video, 0, 0);

    const blob = await new Promise((resolve) => canvas.toBlob(resolve, 'image/jpeg', 0.85));
    const file = new File([blob], 'camera.jpg', { type: 'image/jpeg', lastModified: Date.now() });

    // put it in the file input, as image-resize.js does
    const transfer = new DataTransfer();
    transfer.items.add(file);
    input.files = transfer.files;

    // assigning .files doesn't fire "change", so fire it to run the resize + preview
    input.dispatchEvent(new Event('change', { bubbles: true }));

    stream?.getTracks().forEach((track) => track.stop());
    stream = null;
    video.srcObject = null

    // we close the modal accordingly
    photo_modal.returnValue = "";
    photo_modal.close();
  });
})();
