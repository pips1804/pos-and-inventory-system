    <!-- Logout Confirmation Modal -->
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"> <!-- Added modal-dialog-centered -->
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="logoutModalLabel">Confirm Logout</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to log out?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn " data-bs-dismiss="modal">Cancel</button>
                    <a href="controllers/logout.php" class="btn">Logout</a>
                </div>
            </div>
        </div>
    </div>


    <!-- Remove Item Confirmation Modal -->
    <div class="modal fade" id="removeModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="logoutModalLabel">Confirm Removing of Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to remove this item from the order list?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn " data-bs-dismiss="modal">Cancel</button>
                    <form action="./controllers/remove_from_cart.php" method="post">
                        <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                        <button type="submit" class="btn">Remove</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Checkout Message Modal -->
    <div class="modal fade" id="checkoutModal" tabindex="-1" aria-labelledby="checkoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="checkoutModalLabel">Order Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="checkoutMessage"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Remove All Cart Items Message Modal -->
    <div class="modal fade" id="removeCartModal" tabindex="-1" aria-labelledby="removeCartLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="removeModalLabel">WARNING!</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="removeCartMessage"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="removeAllModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="logoutModalLabel">Confirm Removing of Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to remove all the items from the order list?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn " data-bs-dismiss="modal">Cancel</button>
                    <form action="./controllers/remove_cart.php" method="post">
                        <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                        <button type="submit" class="btn">Remove</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Script for modals -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var checkoutMessage = <?php echo isset($_SESSION['checkout_message']) ? json_encode($_SESSION['checkout_message']) : 'null'; ?>;
            if (checkoutMessage) {
                document.getElementById("checkoutMessage").innerText = checkoutMessage;
                var checkoutModal = new bootstrap.Modal(document.getElementById('checkoutModal'));
                checkoutModal.show();
                <?php unset($_SESSION['checkout_message']); ?> // Clear the message after displaying
            }
        });

        document.addEventListener("DOMContentLoaded", function() {
            var removeCartMessage = <?php echo isset($_SESSION['remove_message']) ? json_encode($_SESSION['remove_message']) : 'null'; ?>;
            if (removeCartMessage) {
                document.getElementById("removeCartMessage").innerText = removeCartMessage;
                var removeCartModal = new bootstrap.Modal(document.getElementById('removeCartModal'));
                removeCartModal.show();
                <?php unset($_SESSION['remove_message']); ?> // Clear the message after displaying
            }
        });
    </script>
