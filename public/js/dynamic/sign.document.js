var wrapper = document.getElementById("signature-wrapper");
var canvas = wrapper.querySelector("canvas");
var signaturePad = new SignaturePad(canvas, {
  backgroundColor: 'rgb(255, 255, 255)'
});

$("#signature-pad").width($("#signature-col-wrapper").width())

// mobile friendly settings
function resizeCanvas() {
  var ratio = Math.max(window.devicePixelRatio || 1, 1);
  canvas.width = canvas.offsetWidth * ratio;
  canvas.height = canvas.offsetHeight * ratio;
  canvas.getContext("2d").scale(ratio, ratio);
  signaturePad.clear();
}
window.onresize = resizeCanvas;
resizeCanvas();

//save signature to input field
signaturePad.addEventListener("endStroke", () => {
  var signature_code= signaturePad.toDataURL();
  $("#signature_code").val(signature_code);
}, {
  once: false
});