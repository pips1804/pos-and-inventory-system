function showLogoutModal() {
  var logoutModal = new bootstrap.Modal(document.getElementById("logoutModal"));
  logoutModal.show();
}

function showRemoveModal() {
  var removeModal = new bootstrap.Modal(document.getElementById("removeModal"));
  removeModal.show();
}

function showRemoveAllModal() {
  var removeAllModal = new bootstrap.Modal(
    document.getElementById("removeAllModal")
  );
  removeAllModal.show();
}

function disableCheckoutButton() {
  document.getElementById("checkoutButton").disabled = true;
  document.getElementById("checkoutButton").innerText = "Processing...";
}

document.getElementById("searchBox").addEventListener("keyup", function () {
  let searchQuery = this.value.trim();

  let xhr = new XMLHttpRequest();
  xhr.open(
    "GET",
    "./././controllers/search_item.php?search=" +
      encodeURIComponent(searchQuery),
    true
  );

  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4 && xhr.status === 200) {
      document.querySelector(".products-container").innerHTML =
        xhr.responseText;
    }
  };

  xhr.send();
});
