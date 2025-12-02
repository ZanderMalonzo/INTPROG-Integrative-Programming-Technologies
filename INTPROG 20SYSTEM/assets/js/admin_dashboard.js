document.addEventListener('DOMContentLoaded', () => {
  const BASE_URL = window.APP_BASE_URL || '/INTPROG SYSTEM';
  const container = document.getElementById('ordersContainer');
  const completedContainer = document.getElementById('completedOrdersContainer');
  const totalEl = document.getElementById('totalOrders');
  const pendingEl = document.getElementById('pendingOrders');
  const completedEl = document.getElementById('completedOrders');

  async function loadOrders() {
    try {
      const res = await fetch(`${BASE_URL}/api/admin_orders.php`);
      const text = await res.text();
      if (!res.ok || !text) throw new Error(text || 'Failed to load orders');
      const data = JSON.parse(text);
      const orders = Array.isArray(data.orders) ? data.orders : [];

      const activeOrders = orders.filter(o => o.order_status !== 'delivered' && o.order_status !== 'cancelled');
      const deliveredOrders = orders.filter(o => o.order_status === 'delivered');
      const totalOrders = orders.length;
      const pendingOrders = orders.filter(o =>
        ['pending', 'confirmed', 'preparing', 'ready', 'out_for_delivery'].includes(o.order_status)
      ).length;
      const completedOrders = deliveredOrders.length;

      totalEl.textContent = totalOrders;
      pendingEl.textContent = pendingOrders;
      completedEl.textContent = completedOrders;

      if (activeOrders.length === 0) {
        container.innerHTML = '<div class="no-orders"><h2>No active orders</h2><p>All orders have been completed or there are no orders yet.</p></div>';
      } else {
        container.innerHTML = activeOrders.map(order => {
        const orderDate = new Date(order.created_at).toLocaleString('en-US', {
          year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit'
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

        return `
          <div class="order-card">
            <div class="order-header">
              <div>
                <div class="order-number">Order #${order.order_id}</div>
                <div class="order-date">${orderDate}</div>
              </div>
              <span class="order-status ${order.order_status}">${String(order.order_status).replace('_', ' ').toUpperCase()}</span>
            </div>
            <div class="customer-info">
              <div class="info-group"><span class="info-label">Customer Name</span><span class="info-value">${order.full_name}</span></div>
              <div class="info-group"><span class="info-label">Username</span><span class="info-value">${order.username}</span></div>
              <div class="info-group"><span class="info-label">Email</span><span class="info-value">${order.email}</span></div>
              <div class="info-group"><span class="info-label">User ID</span><span class="info-value">#${order.user_id}</span></div>
              <div class="info-group"><span class="info-label">Phone</span><span class="info-value">${order.phone_number}</span></div>
              <div class="info-group"><span class="info-label">Payment Method</span><span class="info-value">${order.payment_method}</span></div>
            </div>
            <div class="info-group" style="margin-top:15px;"><span class="info-label">Delivery Address</span><span class="info-value">${order.delivery_address}</span></div>
            <div class="order-items-section"><h3>Order Items (${(order.items||[]).length})</h3><div class="items-list">${itemsHtml}</div></div>
            <div class="order-total"><span class="total-label">Total Amount</span><span class="total-value">₱${parseFloat(order.total_amount).toFixed(2)}</span></div>
          </div>
        `;
      }).join('');
      }

      if (completedContainer) {
        if (deliveredOrders.length === 0) {
          completedContainer.innerHTML = '<div class="no-orders"><h2>No completed orders</h2><p>Delivered orders will appear here.</p></div>';
        } else {
          completedContainer.innerHTML = deliveredOrders.map(order => {
            const orderDate = new Date(order.created_at).toLocaleString('en-US', {
              year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit'
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

            const detailsHtml = `
              <div class=\"customer-info\">
                <div class=\"info-group\"><span class=\"info-label\">Customer Name</span><span class=\"info-value\">${order.full_name}</span></div>
                <div class=\"info-group\"><span class=\"info-label\">Username</span><span class=\"info-value\">${order.username}</span></div>
                <div class=\"info-group\"><span class=\"info-label\">Email</span><span class=\"info-value\">${order.email}</span></div>
                <div class=\"info-group\"><span class=\"info-label\">User ID</span><span class=\"info-value\">#${order.user_id}</span></div>
                <div class=\"info-group\"><span class=\"info-label\">Phone</span><span class=\"info-value\">${order.phone_number}</span></div>
                <div class=\"info-group\"><span class=\"info-label\">Payment Method</span><span class=\"info-value\">${order.payment_method}</span></div>
              </div>
              <div class=\"info-group\" style=\"margin-top:15px;\"><span class=\"info-label\">Delivery Address</span><span class=\"info-value\">${order.delivery_address}</span></div>
              <div class=\"order-items-section\"><h3>Order Items (${(order.items||[]).length})</h3><div class=\"items-list\">${itemsHtml}</div></div>
            `;

            return `
              <div class=\"order-card\">
                <div class=\"order-header\">
                  <div>
                    <button class=\"order-toggle order-number\" data-order-id=\"${order.order_id}\" style=\"background:none;border:none;color:#1a1a2e;cursor:pointer;padding:0;text-align:left;\">Order #${order.order_id}</button>
                    <div class=\"order-date\">${orderDate}</div>
                  </div>
                  <span class=\"order-status delivered\">DELIVERED</span>
                </div>
                <div class=\"order-total\"><span class=\"total-label\">Total Amount</span><span class=\"total-value\">₱${parseFloat(order.total_amount).toFixed(2)}</span></div>
                <div class=\"order-details\" id=\"details-${order.order_id}\" style=\"display:none;\">${detailsHtml}</div>
              </div>
            `;
          }).join('');
        }
      }
    } catch (e) {
      container.innerHTML = `<div class="error"><strong>Error:</strong> ${e.message}</div>`;
    }
  }

  window.loadOrders = loadOrders;
  if (completedContainer) {
    completedContainer.addEventListener('click', (e) => {
      const t = e.target.closest('.order-toggle');
      if (!t) return;
      const id = t.dataset.orderId;
      const el = document.getElementById(`details-${id}`);
      if (el) {
        el.style.display = el.style.display === 'none' ? 'block' : 'none';
      }
    });
  }
  loadOrders();
  setInterval(loadOrders, 30000);
});