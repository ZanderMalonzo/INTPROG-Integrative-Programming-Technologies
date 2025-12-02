document.addEventListener("DOMContentLoaded", () => {
  const BASE_URL = window.APP_BASE_URL || '/INTPROG SYSTEM';
  const addButtons = document.querySelectorAll(".add-btn");
  const cartModal = document.getElementById("cartModal");
  const closeCartBtn = document.querySelector(".close-cart");
  const cartItemsContainer = document.querySelector(".cart-items");
  const cartTotal = document.getElementById("cartTotal");
  const cartCount = document.querySelector(".cart-count");
  const cartIcon = document.querySelector(".floating-cart");

  const paymentModal = document.getElementById("paymentModal");
  const closePayment = document.querySelector(".close-payment");
  const paymentForm = document.getElementById("paymentForm");
  const checkoutBtn = document.querySelector(".checkout-btn");

  let cart = [];

  // --- ADD TO CART ---
  addButtons.forEach(button => {
    button.addEventListener("click", () => {
      const itemCard = button.closest(".menu-card, .snack-card");
      const name = itemCard.querySelector("h3").textContent;
      const price = parseFloat(itemCard.querySelector("p").textContent.replace("₱", ""));

      const existingItem = cart.find(item => item.name === name);
      if (existingItem) {
        existingItem.quantity++;
      } else {
        cart.push({ name, price, quantity: 1 });
      }

      updateCart();
      showToast("✅ Order added to cart!");
    });
  });

  // --- UPDATE CART DISPLAY ---
  function updateCart() {
    cartItemsContainer.innerHTML = "";
    let total = 0;
    let itemCount = 0;

    cart.forEach((item, index) => {
      const itemTotal = item.price * item.quantity;
      total += itemTotal;
      itemCount += item.quantity;

      const cartItem = document.createElement("div");
      cartItem.classList.add("cart-item");
      cartItem.innerHTML = `
        <p>${item.name}</p>
        <div class="quantity-controls">
          <button class="decrease">−</button>
          <span>${item.quantity}</span>
          <button class="increase">+</button>
          <p>₱${itemTotal.toFixed(2)}</p>
          <button class="remove-btn">❌</button>
        </div>
      `;

      // Increase / Decrease / Remove
      cartItem.querySelector(".increase").addEventListener("click", () => {
        item.quantity++;
        updateCart();
      });

      cartItem.querySelector(".decrease").addEventListener("click", () => {
        if (item.quantity > 1) item.quantity--;
        else cart.splice(index, 1);
        updateCart();
      });

      cartItem.querySelector(".remove-btn").addEventListener("click", () => {
        cart.splice(index, 1);
        updateCart();
      });

      cartItemsContainer.appendChild(cartItem);
    });

    cartTotal.textContent = total.toFixed(2);
    cartCount.textContent = itemCount;
  }

  // --- CART OPEN/CLOSE ---
  cartIcon.addEventListener("click", () => {
    cartModal.style.display = "flex";
  });

  closeCartBtn.addEventListener("click", () => {
    cartModal.style.display = "none";
  });

  window.addEventListener("click", e => {
    if (e.target === cartModal) cartModal.style.display = "none";
    if (e.target === paymentModal) paymentModal.style.display = "none";
  });

  // --- TOAST MESSAGE ---
  function showToast(message) {
    const toast = document.getElementById("toast");
    toast.textContent = message;
    toast.className = "toast show";
    setTimeout(() => toast.className = "toast", 2000);
  }

  // --- CHECKOUT BUTTON ---
  checkoutBtn.addEventListener("click", async () => {
    if (cart.length === 0) {
      showToast("🛒 Your cart is empty!");
      return;
    }

    cartModal.style.display = "none";
    paymentModal.style.display = "flex";
    
    // Check payment method availability
    await updatePaymentMethodStatus();
  });

  // --- UPDATE PAYMENT METHOD STATUS ---
  async function updatePaymentMethodStatus() {
    try {
      const response = await fetch(`${BASE_URL}/api/check_payment.php?payment_method=all`);
      const data = await response.json();
      
      const gcashRadio = document.querySelector('input[value="GCash"]');
      const creditRadio = document.querySelector('input[value="Credit Card"]');
      const codRadio = document.querySelector('input[value="Cash on Delivery"]');
      const gcashLabel = gcashRadio ? gcashRadio.closest('label') : null;
      const creditLabel = creditRadio ? creditRadio.closest('label') : null;
      const codLabel = codRadio ? codRadio.closest('label') : null;
      
      // Remove existing warnings
      const existingWarnings = document.querySelectorAll('.payment-warning');
      existingWarnings.forEach(warning => warning.remove());
      
      // Add warnings if payment methods are not set up
      if (!data.has_gcash && gcashLabel) {
        const warning = document.createElement('span');
        warning.className = 'payment-warning';
        warning.textContent = ' (Not set up)';
        warning.style.color = '#ff6b6b';
        warning.style.fontSize = '0.85em';
        warning.style.marginLeft = '5px';
        gcashLabel.appendChild(warning);
      }
      
      if (!data.has_credit && creditLabel) {
        const warning = document.createElement('span');
        warning.className = 'payment-warning';
        warning.textContent = ' (Not set up)';
        warning.style.color = '#ff6b6b';
        warning.style.fontSize = '0.85em';
        warning.style.marginLeft = '5px';
        creditLabel.appendChild(warning);
      }
      
      if (!data.has_address && codLabel) {
        const warning = document.createElement('span');
        warning.className = 'payment-warning';
        warning.textContent = ' (Address not set)';
        warning.style.color = '#ff6b6b';
        warning.style.fontSize = '0.85em';
        warning.style.marginLeft = '5px';
        codLabel.appendChild(warning);
      }
    } catch (error) {
      console.error('Error checking payment methods:', error);
    }
  }

  // --- PAYMENT MODAL HANDLING ---
  closePayment.addEventListener("click", () => {
    paymentModal.style.display = "none";
  });

  paymentForm.addEventListener("submit", async (e) => {
    e.preventDefault();

    const selectedPayment = document.querySelector('input[name="payment"]:checked');
    if (!selectedPayment) {
      showToast("⚠️ Please select a payment method!");
      return;
    }

    const paymentMethod = selectedPayment.value;

    // Validate payment method information if needed
    if (paymentMethod === 'GCash' || paymentMethod === 'Credit Card' || paymentMethod === 'Cash on Delivery') {
      try {
        const response = await fetch(`${BASE_URL}/api/check_payment.php?payment_method=${encodeURIComponent(paymentMethod)}`);
        const data = await response.json();

        // Check if payment method is valid
        if (!data.valid) {
          showToast(`⚠️ ${data.message}. Please update your profile first.`);
          setTimeout(() => {
            if (confirm('Would you like to set up your payment information now?')) {
              window.location.href = `${BASE_URL}/public/pages/personal_info.php`;
            }
          }, 2000);
          return;
        }
      } catch (error) {
        console.error('Error checking payment method:', error);
        showToast('⚠️ Error validating payment method. Please try again.');
        return;
      }
    }

    // Validate cart is not empty
    if (cart.length === 0) {
      showToast("⚠️ Your cart is empty! Please add items first.");
      return;
    }

    // Calculate total
    let total = 0;
    cart.forEach(item => {
      total += item.price * item.quantity;
    });

    // Validate total is greater than 0
    if (total <= 0) {
      showToast("⚠️ Invalid order total. Please check your cart items.");
      return;
    }

    // Save order to database
    try {
      const orderData = {
        items: cart.map(item => ({
          name: item.name,
          price: item.price,
          quantity: item.quantity
        })),
        payment_method: paymentMethod,
        total: total
      };

      // Validate order data
      if (!orderData.items || orderData.items.length === 0) {
        showToast("⚠️ No items in order. Please add items to cart.");
        return;
      }

      const saveResponse = await fetch(`${BASE_URL}/api/orders_proxy.php`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(orderData)
      });

      let saveResult;
      let responseText;
      
      try {
        responseText = await saveResponse.text();
        if (!responseText) {
          throw new Error('Empty response from server');
        }
        saveResult = JSON.parse(responseText);
      } catch (parseError) {
        console.error('JSON parse error:', parseError);
        console.error('Response text:', responseText);
        showToast(`⚠️ Server error: ${responseText || 'Invalid response from server'}`);
        return;
      }

      if (!saveResponse.ok) {
        const errorMsg = saveResult.error || saveResult.message || 'Failed to save order';
        console.error('Order save error:', errorMsg);
        showToast(`⚠️ ${errorMsg}`);
        
        // If address is missing, offer to redirect
        if (errorMsg.includes('Address not found') || errorMsg.includes('address')) {
          setTimeout(() => {
            if (confirm('Would you like to set your address now?')) {
              window.location.href = `${BASE_URL}/public/pages/personal_info.php`;
            }
          }, 2000);
        }
        return;
      }

      // Check if order_id exists in response
      if (!saveResult.order_id) {
        console.error('No order_id in response:', saveResult);
        showToast('⚠️ Order saved but no order ID returned. Please check your orders.');
      } else {
        showToast(`✅ Order placed successfully!`);
      }

      paymentModal.style.display = "none";

      // Clear cart
      cart = [];
      updateCart();
    } catch (error) {
      console.error('Error saving order:', error);
      showToast(`⚠️ Error: ${error.message || 'Failed to save order. Please try again.'}`);
    }
  });
});
