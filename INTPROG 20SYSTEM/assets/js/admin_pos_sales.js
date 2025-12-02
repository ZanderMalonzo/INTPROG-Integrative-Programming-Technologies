document.addEventListener('DOMContentLoaded', () => {
  const BASE_URL = window.APP_BASE_URL || '/INTPROG SYSTEM';
  let currentDateFilter = 'today';
  let currentDate = new Date().toISOString().split('T')[0];
  const lastUpdate = document.getElementById('lastUpdate');
  const refreshBtn = document.getElementById('refreshBtn');

  function setDate(filter) {
    currentDateFilter = filter;
    document.querySelectorAll('.date-btn').forEach(btn => btn.classList.remove('active'));
    const today = new Date();
    let startDate, endDate;
    if (filter === 'today') { startDate = endDate = today.toISOString().split('T')[0]; document.getElementById('btnToday').classList.add('active'); }
    else if (filter === 'yesterday') { const y = new Date(today); y.setDate(y.getDate()-1); startDate = endDate = y.toISOString().split('T')[0]; document.getElementById('btnYesterday').classList.add('active'); }
    else if (filter === 'week') { const ws = new Date(today); ws.setDate(ws.getDate()-ws.getDay()); startDate = ws.toISOString().split('T')[0]; endDate = today.toISOString().split('T')[0]; document.getElementById('btnWeek').classList.add('active'); }
    else if (filter === 'month') { const ms = new Date(today.getFullYear(), today.getMonth(), 1); startDate = ms.toISOString().split('T')[0]; endDate = today.toISOString().split('T')[0]; document.getElementById('btnMonth').classList.add('active'); }
    currentDate = endDate;
    loadSalesData(startDate, endDate);
  }
  window.setDate = setDate;

  function setCustomDate() {
    const date = document.getElementById('customDate').value;
    if (date) {
      currentDateFilter = 'custom';
      currentDate = date;
      document.querySelectorAll('.date-btn').forEach(btn => btn.classList.remove('active'));
      loadSalesData(date, date);
    }
  }
  window.setCustomDate = setCustomDate;

  function setLoadingState(loading) {
    if (refreshBtn) {
      refreshBtn.disabled = loading;
      refreshBtn.textContent = loading ? '⏳ Loading...' : 'Refresh';
    }
  }

  function showNotification(message, type) {
    const n = document.createElement('div');
    n.className = `notification ${type}`;
    n.textContent = message;
    document.body.appendChild(n);
    setTimeout(() => { n.style.opacity = '0'; setTimeout(() => n.remove(), 300); }, 3000);
  }

  async function loadSalesData(startDate = null, endDate = null) {
    setLoadingState(true);
    document.getElementById('paymentBreakdown').innerHTML = '<div class="loading">Loading payment data...</div>';
    document.getElementById('recentOrders').innerHTML = '<div class="loading">Loading orders...</div>';
    try {
      if (!startDate || !endDate) {
        const t = new Date().toISOString().split('T')[0];
        startDate = endDate = t;
      }
      const res = await fetch(`${BASE_URL}/api/sales_data.php?start_date=${startDate}&end_date=${endDate}`);
      const text = await res.text();
      if (!res.ok || !text) throw new Error(text || 'Failed to load sales data');
      const data = JSON.parse(text);

      const totalSales = parseFloat(data.total_sales || 0) || 0;
      const totalOrders = parseInt(data.total_orders || 0) || 0;
      const avgOrderValue = parseFloat(data.avg_order_value || 0) || 0;
      const completedOrders = parseInt(data.completed_orders || 0) || 0;

      document.getElementById('totalSales').textContent = `₱${totalSales.toFixed(2)}`;
      document.getElementById('totalOrders').textContent = totalOrders;
      document.getElementById('avgOrderValue').textContent = `₱${avgOrderValue.toFixed(2)}`;
      document.getElementById('completedOrders').textContent = completedOrders;

      const salesChangeEl = document.getElementById('salesChange');
      if (data.sales_change !== undefined && data.sales_change !== null && !isNaN(data.sales_change)) {
        const change = parseFloat(data.sales_change);
        salesChangeEl.textContent = `${change >= 0 ? '+' : ''}${change.toFixed(1)}%`;
        salesChangeEl.className = `stat-change ${change >= 0 ? 'positive' : 'negative'}`;
      } else {
        salesChangeEl.textContent = '-';
        salesChangeEl.className = 'stat-change';
      }

      if (data.payment_methods && Object.keys(data.payment_methods).length > 0) {
        const breakdownHtml = Object.entries(data.payment_methods).map(([method, amount]) => `
          <div class="payment-item">
            <div class="payment-method">${method}</div>
            <div class="payment-amount">₱${parseFloat(amount || 0).toFixed(2)}</div>
          </div>
        `).join('');
        document.getElementById('paymentBreakdown').innerHTML = breakdownHtml;
      } else {
        document.getElementById('paymentBreakdown').innerHTML = '<p style="text-align:center;color:#999;padding:20px;">No payment data available</p>';
      }

      if (data.recent_orders && data.recent_orders.length > 0) {
        const ordersHtml = data.recent_orders.map(order => {
          const orderDate = new Date(order.created_at).toLocaleString('en-US', { month:'short', day:'numeric', hour:'2-digit', minute:'2-digit' });
          return `
            <div class="sales-order-card">
              <div class="sales-order-header">
                <div>
                  <div class="sales-order-number">Order #${order.order_id}</div>
                  <div class="sales-order-date">${orderDate}</div>
                </div>
                <div class="sales-order-amount">₱${parseFloat(order.total_amount).toFixed(2)}</div>
              </div>
              <div class="sales-order-details">
                <span class="sales-customer">${order.full_name}</span>
                <span class="sales-status ${order.order_status}">${String(order.order_status).replace('_',' ').toUpperCase()}</span>
                <span class="sales-payment">${order.payment_method}</span>
              </div>
            </div>
          `;
        }).join('');
        document.getElementById('recentOrders').innerHTML = ordersHtml;
      } else {
        document.getElementById('recentOrders').innerHTML = '<div class="no-orders"><h2>No orders found</h2><p>No orders for the selected period.</p></div>';
      }

      if (lastUpdate) {
        const now = new Date();
        lastUpdate.textContent = `Last updated: ${now.toLocaleTimeString()}`;
      }
    } catch (e) {
      showNotification('Error loading sales data: ' + e.message, 'error');
      document.getElementById('paymentBreakdown').innerHTML = `<p style="text-align:center;color:#c33;padding:20px;">Error: ${e.message}</p>`;
      document.getElementById('recentOrders').innerHTML = `<div class="error"><strong>Error:</strong> ${e.message}</div>`;
    } finally {
      setLoadingState(false);
    }
  }
  window.loadSalesData = loadSalesData;

  setDate('today');
  setInterval(() => { if (currentDateFilter === 'today') loadSalesData(); }, 10000);
});