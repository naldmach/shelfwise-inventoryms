<template>
  <div class="dashboard-vue">
    <!-- Statistics Cards -->
    <div class="row mb-4">
      <div class="col-xl-3 col-md-6 mb-4" v-for="stat in stats" :key="stat.id">
        <div class="card border-left-primary shadow h-100 py-2" :class="stat.borderClass">
          <div class="card-body">
            <div class="row no-gutters align-items-center">
              <div class="col mr-2">
                <div class="text-xs font-weight-bold text-uppercase mb-1" :class="stat.textClass">
                  {{ stat.label }}
                </div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ stat.value }}</div>
              </div>
              <div class="col-auto">
                <i :class="stat.icon" class="fa-2x text-gray-300"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Transactions and Alerts -->
    <div class="row mb-4">
      <div class="col-lg-8">
        <div class="card shadow mb-4">
          <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Recent Transactions</h6>
            <button @click="refreshTransactions" class="btn btn-sm btn-primary" :disabled="loading">
              <i class="fas fa-sync-alt" :class="{ 'fa-spin': loading }"></i> Refresh
            </button>
          </div>
          <div class="card-body">
            <div v-if="loading" class="text-center py-4">
              <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
              </div>
            </div>
            <div v-else-if="recentTransactions.length > 0" class="table-responsive">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>Product</th>
                    <th>Type</th>
                    <th>Quantity</th>
                    <th>Warehouse</th>
                    <th>Date</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="transaction in recentTransactions" :key="transaction.id">
                    <td>{{ transaction.product?.name || 'N/A' }}</td>
                    <td>
                      <span class="badge" :class="getTransactionBadgeClass(transaction.type)">
                        {{ formatTransactionType(transaction.type) }}
                      </span>
                    </td>
                    <td>{{ transaction.quantity }}</td>
                    <td>{{ transaction.warehouse?.name || 'N/A' }}</td>
                    <td>{{ formatDate(transaction.created_at) }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div v-else class="text-center py-4">
              <p class="text-muted">No recent transactions</p>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="card shadow mb-4">
          <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-warning">Recent Alerts</h6>
            <button @click="refreshAlerts" class="btn btn-sm btn-warning" :disabled="loadingAlerts">
              <i class="fas fa-sync-alt" :class="{ 'fa-spin': loadingAlerts }"></i> Refresh
            </button>
          </div>
          <div class="card-body">
            <div v-if="loadingAlerts" class="text-center py-4">
              <div class="spinner-border text-warning" role="status">
                <span class="visually-hidden">Loading...</span>
              </div>
            </div>
            <div v-else-if="recentAlerts.length > 0">
              <div v-for="alert in recentAlerts" :key="alert.id" 
                   class="alert alert-sm mb-2" :class="getAlertClass(alert.priority)">
                <strong>{{ alert.title }}</strong><br>
                <small>{{ truncateText(alert.message, 50) }}</small>
              </div>
            </div>
            <div v-else class="text-center py-4">
              <p class="text-muted">No recent alerts</p>
            </div>
          </div>
        </div>

        <!-- Quick Actions -->
        <div class="card shadow mb-4">
          <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
          </div>
          <div class="card-body">
            <div class="d-grid gap-2">
              <button @click="navigateTo('products.create')" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Add Product
              </button>
              <button @click="navigateTo('inventory.index')" class="btn btn-success btn-sm">
                <i class="fas fa-boxes"></i> Manage Inventory
              </button>
              <button @click="navigateTo('reports.index')" class="btn btn-info btn-sm">
                <i class="fas fa-chart-bar"></i> Generate Report
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Real-time Updates -->
    <div class="row">
      <div class="col-12">
        <div class="card shadow">
          <div class="card-header">
            <h6 class="mb-0">Real-time Updates</h6>
            <small class="text-muted">Last updated: {{ lastUpdated }}</small>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <h6>Low Stock Items</h6>
                <div v-if="lowStockItems.length > 0">
                  <div v-for="item in lowStockItems" :key="item.id" class="d-flex justify-content-between align-items-center mb-2">
                    <span>{{ item.name }}</span>
                    <span class="badge bg-warning">{{ item.quantity }} {{ item.unit }}</span>
                  </div>
                </div>
                <p v-else class="text-muted">No low stock items</p>
              </div>
              <div class="col-md-6">
                <h6>Out of Stock Items</h6>
                <div v-if="outOfStockItems.length > 0">
                  <div v-for="item in outOfStockItems" :key="item.id" class="d-flex justify-content-between align-items-center mb-2">
                    <span>{{ item.name }}</span>
                    <span class="badge bg-danger">Out of Stock</span>
                  </div>
                </div>
                <p v-else class="text-muted">No out of stock items</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Dashboard',
  data() {
    return {
      loading: false,
      loadingAlerts: false,
      stats: [
        { id: 1, label: 'Total Products', value: 0, icon: 'fas fa-box', borderClass: 'border-left-primary', textClass: 'text-primary' },
        { id: 2, label: 'Categories', value: 0, icon: 'fas fa-tags', borderClass: 'border-left-success', textClass: 'text-success' },
        { id: 3, label: 'Warehouses', value: 0, icon: 'fas fa-warehouse', borderClass: 'border-left-info', textClass: 'text-info' },
        { id: 4, label: 'Low Stock Items', value: 0, icon: 'fas fa-exclamation-triangle', borderClass: 'border-left-warning', textClass: 'text-warning' }
      ],
      recentTransactions: [],
      recentAlerts: [],
      lowStockItems: [],
      outOfStockItems: [],
      lastUpdated: new Date().toLocaleTimeString()
    }
  },
  mounted() {
    this.loadDashboardData();
    this.startAutoRefresh();
  },
  methods: {
    async loadDashboardData() {
      try {
        this.loading = true;
        this.loadingAlerts = true;
        
        // Load statistics
        await this.loadStats();
        
        // Load recent transactions
        await this.loadRecentTransactions();
        
        // Load recent alerts
        await this.loadRecentAlerts();
        
        // Load stock alerts
        await this.loadStockAlerts();
        
        this.lastUpdated = new Date().toLocaleTimeString();
      } catch (error) {
        console.error('Error loading dashboard data:', error);
      } finally {
        this.loading = false;
        this.loadingAlerts = false;
      }
    },
    
    async loadStats() {
      try {
        const response = await fetch('/api/dashboard/stats');
        if (response.ok) {
          const data = await response.json();
          this.stats[0].value = data.total_products || 0;
          this.stats[1].value = data.total_categories || 0;
          this.stats[2].value = data.total_warehouses || 0;
          this.stats[3].value = data.low_stock_products || 0;
        }
      } catch (error) {
        console.error('Error loading stats:', error);
        // Fallback to sample data
        this.stats[0].value = 5;
        this.stats[1].value = 6;
        this.stats[2].value = 3;
        this.stats[3].value = 2;
      }
    },
    
    async loadRecentTransactions() {
      try {
        const response = await fetch('/api/dashboard/transactions');
        if (response.ok) {
          this.recentTransactions = await response.json();
        }
      } catch (error) {
        console.error('Error loading transactions:', error);
        // Fallback to sample data
        this.recentTransactions = [
          { id: 1, type: 'in', quantity: 10, product: { name: 'Laptop Computer' }, warehouse: { name: 'Main Warehouse' }, created_at: new Date() },
          { id: 2, type: 'out', quantity: 5, product: { name: 'Smartphone' }, warehouse: { name: 'Main Warehouse' }, created_at: new Date() }
        ];
      }
    },
    
    async loadRecentAlerts() {
      try {
        const response = await fetch('/api/dashboard/alerts');
        if (response.ok) {
          this.recentAlerts = await response.json();
        }
      } catch (error) {
        console.error('Error loading alerts:', error);
        // Fallback to sample data
        this.recentAlerts = [
          { id: 1, title: 'Low Stock Alert', message: 'Product "Laptop Computer" is running low on stock.', priority: 'high' },
          { id: 2, title: 'Out of Stock Alert', message: 'Product "Garden Tool Set" is completely out of stock.', priority: 'critical' }
        ];
      }
    },
    
    async loadStockAlerts() {
      try {
        const response = await fetch('/api/dashboard/stock-alerts');
        if (response.ok) {
          const data = await response.json();
          this.lowStockItems = data.low_stock || [];
          this.outOfStockItems = data.out_of_stock || [];
        }
      } catch (error) {
        console.error('Error loading stock alerts:', error);
        // Fallback to sample data
        this.lowStockItems = [
          { id: 1, name: 'Laptop Computer', quantity: 3, unit: 'pcs' },
          { id: 2, name: 'Garden Tool Set', quantity: 2, unit: 'sets' }
        ];
        this.outOfStockItems = [
          { id: 3, name: 'Programming Book', unit: 'pcs' }
        ];
      }
    },
    
    refreshTransactions() {
      this.loadRecentTransactions();
    },
    
    refreshAlerts() {
      this.loadRecentAlerts();
    },
    
    startAutoRefresh() {
      // Refresh data every 30 seconds
      setInterval(() => {
        this.loadDashboardData();
      }, 30000);
    },
    
    getTransactionBadgeClass(type) {
      const classes = {
        'in': 'badge-success',
        'out': 'badge-danger',
        'adjustment': 'badge-info',
        'transfer': 'badge-warning'
      };
      return classes[type] || 'badge-secondary';
    },
    
    formatTransactionType(type) {
      return type.charAt(0).toUpperCase() + type.slice(1);
    },
    
    getAlertClass(priority) {
      const classes = {
        'critical': 'alert-danger',
        'high': 'alert-warning',
        'medium': 'alert-info',
        'low': 'alert-info'
      };
      return classes[priority] || 'alert-info';
    },
    
    formatDate(dateString) {
      const date = new Date(dateString);
      return date.toLocaleDateString('en-US', { 
        month: 'short', 
        day: 'numeric', 
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      });
    },
    
    truncateText(text, length) {
      if (text.length <= length) return text;
      return text.substring(0, length) + '...';
    },
    
    navigateTo(route) {
      window.location.href = `/${route}`;
    }
  }
}
</script>

<style scoped>
.border-left-primary {
  border-left: 0.25rem solid #4e73df !important;
}
.border-left-success {
  border-left: 0.25rem solid #1cc88a !important;
}
.border-left-info {
  border-left: 0.25rem solid #36b9cc !important;
}
.border-left-warning {
  border-left: 0.25rem solid #f6c23e !important;
}
.text-gray-300 {
  color: #dddfeb !important;
}
.text-gray-800 {
  color: #5a5c69 !important;
}
.font-weight-bold {
  font-weight: 700 !important;
}
.text-xs {
  font-size: 0.7rem !important;
}
.alert-sm {
  padding: 0.5rem;
  font-size: 0.875rem;
}
</style>
