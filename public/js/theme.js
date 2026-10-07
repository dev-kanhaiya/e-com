// Auto data-label mapping for responsive mobile tables
document.querySelectorAll("table").forEach(function(t){
  var h = [].map.call(t.querySelectorAll("thead th"), function(th){ return th.textContent.trim(); });
  t.querySelectorAll("tbody tr").forEach(function(r){
    [].forEach.call(r.children, function(td, i){ td.setAttribute("data-label", h[i] || ""); });
  });
});

// Auto-dismiss alert flash messages after 4 seconds
document.addEventListener("DOMContentLoaded", function() {
  var flashAlerts = document.querySelectorAll(".flash-msg, .alert-auto-dismiss");
  if (flashAlerts.length > 0) {
    setTimeout(function() {
      flashAlerts.forEach(function(el) {
        el.style.transition = "opacity 0.4s ease, transform 0.4s ease";
        el.style.opacity = "0";
        el.style.transform = "translateY(-8px)";
        setTimeout(function() {
          if (el.parentNode) el.parentNode.removeChild(el);
        }, 400);
      });
    }, 4000);
  }
});

// Global Form Submit Loading Overlay with backdrop blur
window.showAppLoader = function(text) {
  var overlay = document.getElementById("app-loader-overlay");
  if (!overlay) {
    overlay = document.createElement("div");
    overlay.id = "app-loader-overlay";
    overlay.className = "app-loader-backdrop";
    overlay.innerHTML = '<div class="app-spinner-box"><div class="app-spinner"></div><div id="app-loader-text" class="app-loader-msg">Processing...</div></div>';
    document.body.appendChild(overlay);
  }
  if (text) {
    var txtEl = document.getElementById("app-loader-text");
    if (txtEl) txtEl.textContent = text;
  }
  overlay.style.display = "flex";
};

window.hideAppLoader = function() {
  var overlay = document.getElementById("app-loader-overlay");
  if (overlay) {
    overlay.style.display = "none";
  }
};
