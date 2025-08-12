<template>
  <div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
      <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
          <h1 class="h3 mb-0">
            <i class="fas fa-warehouse text-primary me-2"></i>
            Warehouse Management
          </h1>
          <button @click="showCreateModal = true" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add Warehouse
          </button>
        </div>
      </div>
    </div>

    <!-- Search and Filters -->
    <div class="row mb-4">
      <div class="col-md-6">
        <div class="input-group">
          <span class="input-group-text">
            <i class="fas fa-search"></i>
          </span>
          <input
            v-model="searchQuery"
            type="text"
            class="form-control"
            placeholder="Search warehouses..."
            @input="filterWarehouses"
          />
        </div>
      </div>
      <div class="col-md-3">
        <select v-model="statusFilter" class="form-select" @change="filterWarehouses">
          <option value="">All Status</option>
          <option value="active">Active</option>
          <option value="inactive">Inactive</option>
        </select>
      </div>
      <div class="col-md-3">
        <select v-model="sortBy" class="form-select" @change="sortWarehouses">
          <option value="name">Sort by Name</option>
          <option value="city">Sort by City</option>
          <option value="products_count">Sort by Products</option>
        </select>
      </div>
    </div>

    <!-- Warehouses Grid -->
    <div class="row">
      <div v-for="warehouse in filteredWarehouses" :key="warehouse.id" class="col-lg-6 col-xl-4 mb-4">
        <div class="card h-100 shadow-sm">
          <div class="card-header bg-light">
            <div class="d-flex justify-content-between align-items-center">
              <h5 class="card-title mb-0">
                <i class="fas fa-warehouse text-primary me-2"></i>
                {{ warehouse.name }}
              </h5>
              <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                  <i class="fas fa-ellipsis-v"></i>
                </button>
                <ul class="dropdown-menu">
                  <li><a class="dropdown-item" href="#" @click="editWarehouse(warehouse)">
                    <i class="fas fa-edit me-2"></i>Edit
                  </a></li>
                  <li><a class="dropdown-item" href="#" @click="viewWarehouse(warehouse)">
                    <i class="fas fa-eye me-2"></i>View
                  </a></li>
                  <li><hr class="dropdown-divider"></li>
                  <li><a class="dropdown-item text-danger" href="#" @click="deleteWarehouse(warehouse)">
                    <i class="fas fa-trash me-2"></i>Delete
                  </a></li>
                </ul>
              </div>
            </div>
          </div>
          
          <div class="card-body">
            <p class="card-text text-muted mb-3">
              {{ warehouse.description || 'No description available' }}
            </p>
            
            <div class="row mb-3">
              <div class="col-6">
                <small class="text-muted d-block">
                  <i class="fas fa-map-marker-alt me-1"></i>Location
                </small>
                <strong>{{ warehouse.city }}, {{ warehouse.state }}</strong>
              </div>
              <div class="col-6">
                <small class="text-muted d-block">
                  <i class="fas fa-boxes me-1"></i>Products
                </small>
                <strong>{{ warehouse.products_count || 0 }}</strong>
              </div>
            </div>
            
            <div class="row mb-3">
              <div class="col-6">
                <small class="text-muted d-block">
                  <i class="fas fa-phone me-1"></i>Phone
                </small>
                <strong>{{ warehouse.phone || 'N/A' }}</strong>
              </div>
              <div class="col-6">
                <small class="text-muted d-block">
                  <i class="fas fa-envelope me-1"></i>Email
                </small>
                <strong>{{ warehouse.email || 'N/A' }}</strong>
              </div>
            </div>
            
            <div class="d-flex justify-content-between align-items-center">
              <span class="badge" :class="warehouse.is_active ? 'bg-success' : 'bg-secondary'">
                {{ warehouse.is_active ? 'Active' : 'Inactive' }}
              </span>
              <small class="text-muted">
                {{ warehouse.country }}
              </small>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-if="filteredWarehouses.length === 0" class="text-center py-5">
      <i class="fas fa-warehouse fa-3x text-muted mb-3"></i>
      <h4 class="text-muted">No warehouses found</h4>
      <p class="text-muted">Create your first warehouse to get started.</p>
      <button @click="showCreateModal = true" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Add Warehouse
      </button>
    </div>

    <!-- Create/Edit Modal -->
    <div class="modal fade" :class="{ show: showCreateModal || showEditModal }" 
         :style="{ display: (showCreateModal || showEditModal) ? 'block' : 'none' }" 
         tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">
              {{ showEditModal ? 'Edit Warehouse' : 'Create Warehouse' }}
            </h5>
            <button type="button" class="btn-close" @click="closeModal"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="saveWarehouse">
              <div class="row">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label">Name *</label>
                    <input
                      v-model="form.name"
                      type="text"
                      class="form-control"
                      required
                      placeholder="Enter warehouse name"
                    />
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label">Country</label>
                    <select v-model="form.country" class="form-select">
                      <option value="USA">USA</option>
                      <option value="Canada">Canada</option>
                      <option value="UK">UK</option>
                      <option value="Germany">Germany</option>
                      <option value="France">France</option>
                      <option value="Other">Other</option>
                    </select>
                  </div>
                </div>
              </div>
              
              <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea
                  v-model="form.description"
                  class="form-control"
                  rows="3"
                  placeholder="Enter warehouse description"
                ></textarea>
              </div>
              
              <div class="row">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label">Address *</label>
                    <input
                      v-model="form.address"
                      type="text"
                      class="form-control"
                      required
                      placeholder="Enter street address"
                    />
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label">City *</label>
                    <input
                      v-model="form.city"
                      type="text"
                      class="form-control"
                      required
                      placeholder="Enter city"
                    />
                  </div>
                </div>
              </div>
              
              <div class="row">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label">State/Province *</label>
                    <input
                      v-model="form.state"
                      type="text"
                      class="form-control"
                      required
                      placeholder="Enter state/province"
                    />
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label">ZIP/Postal Code *</label>
                    <input
                      v-model="form.zip_code"
                      type="text"
                      class="form-control"
                      required
                      placeholder="Enter ZIP/postal code"
                    />
                  </div>
                </div>
              </div>
              
              <div class="row">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label">Phone</label>
                    <input
                      v-model="form.phone"
                      type="tel"
                      class="form-control"
                      placeholder="Enter phone number"
                    />
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input
                      v-model="form.email"
                      type="email"
                      class="form-control"
                      placeholder="Enter email address"
                    />
                  </div>
                </div>
              </div>
              
              <div class="mb-3">
                <div class="form-check">
                  <input
                    v-model="form.is_active"
                    class="form-check-input"
                    type="checkbox"
                    id="activeCheck"
                  />
                  <label class="form-check-label" for="activeCheck">
                    Active
                  </label>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-backdrop fade show" v-if="showCreateModal || showEditModal"></div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="closeModal">Cancel</button>
            <button type="button" class="btn btn-primary" @click="saveWarehouse">
              {{ showEditModal ? 'Update' : 'Create' }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- View Modal -->
    <div class="modal fade" :class="{ show: showViewModal }" 
         :style="{ display: showViewModal ? 'block' : 'none' }" 
         tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Warehouse Details</h5>
            <button type="button" class="btn-close" @click="showViewModal = false"></button>
          </div>
          <div class="modal-body" v-if="selectedWarehouse">
            <div class="row">
              <div class="col-md-8">
                <h4>
                  <i class="fas fa-warehouse text-primary me-2"></i>
                  {{ selectedWarehouse.name }}
                </h4>
                <p class="text-muted">{{ selectedWarehouse.description || 'No description' }}</p>
                
                <div class="row mb-3">
                  <div class="col-6">
                    <strong>Status:</strong>
                    <span class="badge ms-2" :class="selectedWarehouse.is_active ? 'bg-success' : 'bg-secondary'">
                      {{ selectedWarehouse.is_active ? 'Active' : 'Inactive' }}
                    </span>
                  </div>
                  <div class="col-6">
                    <strong>Products:</strong>
                    <span class="ms-2">{{ selectedWarehouse.products_count || 0 }}</span>
                  </div>
                </div>
                
                <div class="row mb-3">
                  <div class="col-6">
                    <strong>Address:</strong>
                    <p class="ms-2 mb-1">{{ selectedWarehouse.address }}</p>
                    <p class="ms-2 mb-1">{{ selectedWarehouse.city }}, {{ selectedWarehouse.state }} {{ selectedWarehouse.zip_code }}</p>
                    <p class="ms-2">{{ selectedWarehouse.country }}</p>
                  </div>
                  <div class="col-6">
                    <strong>Contact:</strong>
                    <p class="ms-2 mb-1">
                      <i class="fas fa-phone me-1"></i>
                      {{ selectedWarehouse.phone || 'N/A' }}
                    </p>
                    <p class="ms-2">
                      <i class="fas fa-envelope me-1"></i>
                      {{ selectedWarehouse.email || 'N/A' }}
                    </p>
                  </div>
                </div>
                
                <div class="row mb-3">
                  <div class="col-6">
                    <strong>Created:</strong>
                    <span class="ms-2">{{ formatDate(selectedWarehouse.created_at) }}</span>
                  </div>
                  <div class="col-6">
                    <strong>Updated:</strong>
                    <span class="ms-2">{{ formatDate(selectedWarehouse.updated_at) }}</span>
                  </div>
                </div>
              </div>
              <div class="col-md-4 text-center">
                <div class="warehouse-icon-large mx-auto mb-3">
                  <i class="fas fa-warehouse fa-4x text-primary"></i>
                </div>
                <small class="text-muted">Warehouse ID: {{ selectedWarehouse.id }}</small>
              </div>
            </div>
          </div>
          <div class="modal-backdrop fade show" v-if="showViewModal"></div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="showViewModal = false">Close</button>
            <button type="button" class="btn btn-primary" @click="editWarehouse(selectedWarehouse)">
              <i class="fas fa-edit me-2"></i>Edit
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'WarehouseManager',
  data() {
    return {
      warehouses: [],
      filteredWarehouses: [],
      searchQuery: '',
      statusFilter: '',
      sortBy: 'name',
      showCreateModal: false,
      showEditModal: false,
      showViewModal: false,
      selectedWarehouse: null,
      form: {
        name: '',
        description: '',
        address: '',
        city: '',
        state: '',
        zip_code: '',
        country: 'USA',
        phone: '',
        email: '',
        is_active: true
      },
      editingId: null
    }
  },
  mounted() {
    this.fetchWarehouses()
  },
  methods: {
    async fetchWarehouses() {
      try {
        const response = await fetch('/api/warehouses')
        if (response.ok) {
          this.warehouses = await response.json()
          this.filteredWarehouses = [...this.warehouses]
        }
      } catch (error) {
        console.error('Error fetching warehouses:', error)
      }
    },
    
    filterWarehouses() {
      let filtered = [...this.warehouses]
      
      if (this.searchQuery) {
        filtered = filtered.filter(warehouse => 
          warehouse.name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
          warehouse.city.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
          warehouse.state.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
          (warehouse.description && warehouse.description.toLowerCase().includes(this.searchQuery.toLowerCase()))
        )
      }
      
      if (this.statusFilter) {
        const isActive = this.statusFilter === 'active'
        filtered = filtered.filter(warehouse => warehouse.is_active === isActive)
      }
      
      this.sortWarehouses(filtered)
    },
    
    sortWarehouses(warehouses = null) {
      const whs = warehouses || this.filteredWarehouses
      
      whs.sort((a, b) => {
        switch (this.sortBy) {
          case 'name':
            return a.name.localeCompare(b.name)
          case 'city':
            return a.city.localeCompare(b.city)
          case 'products_count':
            return (b.products_count || 0) - (a.products_count || 0)
          default:
            return 0
        }
      })
      
      this.filteredWarehouses = whs
    },
    
    editWarehouse(warehouse) {
      this.editingId = warehouse.id
      this.form = { ...warehouse }
      this.showEditModal = true
      this.showCreateModal = false
    },
    
    viewWarehouse(warehouse) {
      this.selectedWarehouse = warehouse
      this.showViewModal = true
    },
    
    async saveWarehouse() {
      try {
        const url = this.showEditModal ? `/warehouses/${this.editingId}` : '/warehouses'
        const method = this.showEditModal ? 'PUT' : 'POST'
        
        const response = await fetch(url, {
          method,
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: JSON.stringify(this.form)
        })
        
        if (response.ok) {
          await this.fetchWarehouses()
          this.closeModal()
          this.resetForm()
        }
      } catch (error) {
        console.error('Error saving warehouse:', error)
      }
    },
    
    async deleteWarehouse(warehouse) {
      if (confirm(`Are you sure you want to delete "${warehouse.name}"?`)) {
        try {
          const response = await fetch(`/warehouses/${warehouse.id}`, {
            method: 'DELETE',
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
          })
          
          if (response.ok) {
            await this.fetchWarehouses()
          }
        } catch (error) {
          console.error('Error deleting warehouse:', error)
        }
      }
    },
    
    closeModal() {
      this.showCreateModal = false
      this.showEditModal = false
      this.showViewModal = false
      this.resetForm()
    },
    
    resetForm() {
      this.form = {
        name: '',
        description: '',
        address: '',
        city: '',
        state: '',
        zip_code: '',
        country: 'USA',
        phone: '',
        email: '',
        is_active: true
      }
      this.editingId = null
    },
    
    formatDate(dateString) {
      return new Date(dateString).toLocaleDateString()
    }
  }
}
</script>

<style scoped>
.warehouse-icon-large {
  width: 100px;
  height: 100px;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: #f8f9fa;
  border-radius: 50%;
}

.card {
  transition: transform 0.2s ease-in-out;
}

.card:hover {
  transform: translateY(-2px);
}

.dropdown-item:hover {
  background-color: #f8f9fa;
}

.modal.show {
  background-color: rgba(0, 0, 0, 0.5);
}

.card-header {
  border-bottom: 1px solid #dee2e6;
}
</style>
