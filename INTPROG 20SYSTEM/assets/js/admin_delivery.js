document.addEventListener('DOMContentLoaded', () => {
  const BASE_URL = window.APP_BASE_URL || '/INTPROG SYSTEM';
  let currentFilter = 'all';
  const container = document.getElementById('ordersContainer');

  document.querySelectorAll('.filter-tab').forEach(tab => {
    tab.addEventListener('click', () => {
      document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
      tab.classList.add('active');
      currentFilter = tab.getAttribute('data-status');
      loadOrders();
    });
  });

  function showNotification(message, type) {
    const existing = document.querySelector('.notification');
    if (existing) existing.remove();
    const n = document.createElement('div');
    n.className = `notification ${type}`;
    n.textContent = message;
    document.body.appendChild(n);
    setTimeout(() => { n.style.opacity = '0'; setTimeout(() => n.remove(), 300); }, 3000);
  }

  async function updateOrderStatus(orderId, newStatus) {
    try {
      const res = await fetch(`${BASE_URL}/api/update_order_status.php`, {
        method: 'POST', headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ order_id: orderId, order_status: newStatus })
      });
      const data = await res.json();
      if (data.success) {
        showNotification('Order status updated successfully!', 'success');
        loadOrders();
        if (newStatus === 'delivered') {
          window.dispatchEvent(new CustomEvent('orderDelivered', { detail: { order_id: orderId } }));
        }
      } else {
        showNotification(data.error || 'Failed to update order status', 'error');
      }
    } catch (e) {
      showNotification('Error updating order status: ' + e.message, 'error');
    }
  }
  window.updateOrderStatus = updateOrderStatus;

  function updateStats(orders) {
    const pending = orders.filter(o => o.order_status === 'pending').length;
    const outForDelivery = orders.filter(o => o.order_status === 'out_for_delivery').length;
    const today = new Date(); today.setHours(0,0,0,0);
    const completedToday = orders.filter(o => {
      if (o.order_status !== 'delivered') return false;
      const d = new Date(o.created_at); d.setHours(0,0,0,0);
      return d.getTime() === today.getTime();
    }).length;
    document.getElementById('pendingDelivery').textContent = pending;
    document.getElementById('outForDelivery').textContent = outForDelivery;
    document.getElementById('completedToday').textContent = completedToday;
  }

  async function loadOrders() {
    try {
      const res = await fetch(`${BASE_URL}/api/admin_orders.php`);
      const data = await res.json();
      const orders = Array.isArray(data.orders) ? data.orders : [];
      if (orders.length === 0) {
        container.innerHTML = '<div class="no-orders"><h2>No orders found</h2><p>No orders match the current filter.</p></div>';
        updateStats([]);
        return;
      }
      updateStats(orders);
      let filtered = currentFilter === 'all' ? orders : orders.filter(o => o.order_status === currentFilter);
      filtered = filtered.filter(o => o.order_status !== 'delivered' && o.order_status !== 'cancelled');

      container.innerHTML = filtered.map(order => {
        const orderDate = new Date(order.created_at).toLocaleString('en-US', {
          year:'numeric', month:'long', day:'numeric', hour:'2-digit', minute:'2-digit'
        });
        const itemsHtml = (order.items || []).map(item => `
          <div class="item-row">
            <div class="item-info">
              <div class="item-name">${item.item_name}</div>
              <div class="item-details">₱${parseFloat(item.item_price).toFixed(2)} × ${item.quantity}</div>
            </div>
            <div class="item-price">₱${parseFloat(item.subtotal).toFixed(2)}</div>
          </div>
        `).join('');

        let statusButtons = '';
        const id = order.order_id;
        if (order.order_status === 'pending') statusButtons = `<button class="status-btn confirm-btn" onclick="updateOrderStatus(${id}, 'confirmed')">✓ Confirm Order</button>`;
        else if (order.order_status === 'confirmed') statusButtons = `<button class="status-btn preparing-btn" onclick="updateOrderStatus(${id}, 'preparing')">👨‍🍳 Start Preparing</button>`;
        else if (order.order_status === 'preparing') statusButtons = `<button class="status-btn ready-btn" onclick="updateOrderStatus(${id}, 'ready')">✓ Mark as Ready</button>`;
        else if (order.order_status === 'ready') statusButtons = `<button class="status-btn delivery-btn" onclick="updateOrderStatus(${id}, 'out_for_delivery')">🚚 Out for Delivery</button>`;
        else if (order.order_status === 'out_for_delivery') statusButtons = `<button class="status-btn complete-btn" onclick="updateOrderStatus(${id}, 'delivered')">✅ Mark as Delivered</button>`;

        return `
          <div class="order-card delivery-order-card">
            <div class="order-header">
              <div>
                <div class="order-number">Order #${order.order_id}</div>
                <div class="order-date">${orderDate}</div>
              </div>
              <span class="order-status ${order.order_status}">${String(order.order_status).replace('_',' ').toUpperCase()}</span>
            </div>
            <div class="customer-info">
              <div class="info-group"><span class="info-label">Customer Name</span><span class="info-value">${order.full_name}</span></div>
              <div class="info-group"><span class="info-label">Phone</span><span class="info-value">${order.phone_number}</span></div>
              <div class="info-group"><span class="info-label">Payment Method</span><span class="info-value">${order.payment_method}</span></div>
            </div>
            <div class="info-group" style="margin-top:15px;"><span class="info-label">Delivery Address</span><span class="info-value">${order.delivery_address}</span></div>
            <div class="order-items-section"><h3>Order Items (${(order.items||[]).length})</h3><div class="items-list">${itemsHtml}</div></div>
            <div class="order-total"><span class="total-label">Total Amount</span><span class="total-value">₱${parseFloat(order.total_amount).toFixed(2)}</span></div>
            <div class="delivery-actions">${statusButtons}</div>
          </div>
        `;
      }).join('');
    } catch (e) {
      container.innerHTML = `<div class="error"><strong>Error:</strong> ${e.message}</div>`;
    }
  }

  window.loadOrders = loadOrders;
  loadOrders();
  setInterval(loadOrders, 30000);
});