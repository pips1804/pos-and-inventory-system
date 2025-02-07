function showLogoutModal() {
  var logoutModal = new bootstrap.Modal(document.getElementById("logoutModal"));
  logoutModal.show();
}

function showRemoveModal() {
  var removeModal = new bootstrap.Modal(document.getElementById("removeModal"));
  removeModal.show();
}

function disableCheckoutButton() {
  document.getElementById("checkoutButton").disabled = true;
  document.getElementById("checkoutButton").innerText = "Processing...";
}
