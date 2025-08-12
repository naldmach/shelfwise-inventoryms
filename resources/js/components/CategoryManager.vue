<template>
  <div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
      <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
          <h1 class="h3 mb-0">
            <i class="fas fa-tags text-primary me-2"></i>
            Category Management
          </h1>
          <button @click="showCreateModal = true" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add Category
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
            placeholder="Search categories..."
            @input="filterCategories"
          />
        </div>
      </div>
      <div class="col-md-3">
        <select v-model="statusFilter" class="form-select" @change="filterCategories">
          <option value="">All Status</option>
          <option value="active">Active</option>
          <option value="inactive">Inactive</option>
        </select>
      </div>
      <div class="col-md-3">
        <select v-model="sortBy" class="form-select" @change="sortCategories">
          <option value="name">Sort by Name</option>
          <option value="created_at">Sort by Date</option>
          <option value="products_count">Sort by Products</option>
        </select>
      </div>
    </div>

    <!-- Categories Grid -->
    <div class="row">
      <div v-for="category in filteredCategories" :key="category.id" class="col-lg-4 col-md-6 mb-4">
        <div class="card h-100 shadow-sm">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-3">
              <div class="d-flex align-items-center">
                <div 
                  class="category-color me-3" 
                  :style="{ backgroundColor: category.color }"
                ></div>
                <h5 class="card-title mb-0">{{ category.name }}</h5>
              </div>
              <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                  <i class="fas fa-ellipsis-v"></i>
                </button>
                <ul class="dropdown-menu">
                  <li><a class="dropdown-item" href="#" @click="editCategory(category)">
                    <i class="fas fa-edit me-2"></i>Edit
                  </a></li>
                  <li><a class="dropdown-item" href="#" @click="viewCategory(category)">
                    <i class="fas fa-eye me-2"></i>View
                  </a></li>
                  <li><hr class="dropdown-divider"></li>
                  <li><a class="dropdown-item text-danger" href="#" @click="deleteCategory(category)">
                    <i class="fas fa-trash me-2"></i>Delete
                  </a></li>
                </ul>
              </div>
            </div>
            
            <p class="card-text text-muted mb-3">
              {{ category.description || 'No description available' }}
            </p>
            
            <div class="d-flex justify-content-between align-items-center">
              <span class="badge" :class="category.is_active ? 'bg-success' : 'bg-secondary'">
                {{ category.is_active ? 'Active' : 'Inactive' }}
              </span>
              <small class="text-muted">
                {{ category.products_count || 0 }} products
              </small>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-if="filteredCategories.length === 0" class="text-center py-5">
      <i class="fas fa-tags fa-3x text-muted mb-3"></i>
      <h4 class="text-muted">No categories found</h4>
      <p class="text-muted">Create your first category to get started.</p>
      <button @click="showCreateModal = true" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Add Category
      </button>
    </div>

    <!-- Create/Edit Modal -->
    <div class="modal fade" :class="{ show: showCreateModal || showEditModal }" 
         :style="{ display: (showCreateModal || showEditModal) ? 'block' : 'none' }" 
         tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">
              {{ showEditModal ? 'Edit Category' : 'Create Category' }}
            </h5>
            <button type="button" class="btn-close" @click="closeModal"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="saveCategory">
              <div class="mb-3">
                <label class="form-label">Name *</label>
                <input
                  v-model="form.name"
                  type="text"
                  class="form-control"
                  required
                  placeholder="Enter category name"
                />
              </div>
              
              <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea
                  v-model="form.description"
                  class="form-control"
                  rows="3"
                  placeholder="Enter category description"
                ></textarea>
              </div>
              
              <div class="mb-3">
                <label class="form-label">Color</label>
                <input
                  v-model="form.color"
                  type="color"
                  class="form-control form-control-color"
                />
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
            <button type="button" class="btn btn-primary" @click="saveCategory">
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
            <h5 class="modal-title">Category Details</h5>
            <button type="button" class="btn-close" @click="showViewModal = false"></button>
          </div>
          <div class="modal-body" v-if="selectedCategory">
            <div class="row">
              <div class="col-md-8">
                <h4>{{ selectedCategory.name }}</h4>
                <p class="text-muted">{{ selectedCategory.description || 'No description' }}</p>
                
                <div class="row mb-3">
                  <div class="col-6">
                    <strong>Status:</strong>
                    <span class="badge ms-2" :class="selectedCategory.is_active ? 'bg-success' : 'bg-secondary'">
                      {{ selectedCategory.is_active ? 'Active' : 'Inactive' }}
                    </span>
                  </div>
                  <div class="col-6">
                    <strong>Products:</strong>
                    <span class="ms-2">{{ selectedCategory.products_count || 0 }}</span>
                  </div>
                </div>
                
                <div class="row mb-3">
                  <div class="col-6">
                    <strong>Created:</strong>
                    <span class="ms-2">{{ formatDate(selectedCategory.created_at) }}</span>
                  </div>
                  <div class="col-6">
                    <strong>Updated:</strong>
                    <span class="ms-2">{{ formatDate(selectedCategory.updated_at) }}</span>
                  </div>
                </div>
              </div>
              <div class="col-md-4 text-center">
                <div 
                  class="category-color-large mx-auto mb-3" 
                  :style="{ backgroundColor: selectedCategory.color }"
                ></div>
                <small class="text-muted">{{ selectedCategory.color }}</small>
              </div>
            </div>
          </div>
          <div class="modal-backdrop fade show" v-if="showViewModal"></div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="showViewModal = false">Close</button>
            <button type="button" class="btn btn-primary" @click="editCategory(selectedCategory)">
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
  name: 'CategoryManager',
  data() {
    return {
      categories: [],
      filteredCategories: [],
      searchQuery: '',
      statusFilter: '',
      sortBy: 'name',
      showCreateModal: false,
      showEditModal: false,
      showViewModal: false,
      selectedCategory: null,
      form: {
        name: '',
        description: '',
        color: '#3B82F6',
        is_active: true
      },
      editingId: null
    }
  },
  mounted() {
    this.fetchCategories()
  },
  methods: {
    async fetchCategories() {
      try {
        const response = await fetch('/api/categories')
        if (response.ok) {
          this.categories = await response.json()
          this.filteredCategories = [...this.categories]
        }
      } catch (error) {
        console.error('Error fetching categories:', error)
      }
    },
    
    filterCategories() {
      let filtered = [...this.categories]
      
      if (this.searchQuery) {
        filtered = filtered.filter(category => 
          category.name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
          (category.description && category.description.toLowerCase().includes(this.searchQuery.toLowerCase()))
        )
      }
      
      if (this.statusFilter) {
        const isActive = this.statusFilter === 'active'
        filtered = filtered.filter(category => category.is_active === isActive)
      }
      
      this.sortCategories(filtered)
    },
    
    sortCategories(categories = null) {
      const cats = categories || this.filteredCategories
      
      cats.sort((a, b) => {
        switch (this.sortBy) {
          case 'name':
            return a.name.localeCompare(b.name)
          case 'created_at':
            return new Date(b.created_at) - new Date(a.created_at)
          case 'products_count':
            return (b.products_count || 0) - (a.products_count || 0)
          default:
            return 0
        }
      })
      
      this.filteredCategories = cats
    },
    
    editCategory(category) {
      this.editingId = category.id
      this.form = { ...category }
      this.showEditModal = true
      this.showCreateModal = false
    },
    
    viewCategory(category) {
      this.selectedCategory = category
      this.showViewModal = true
    },
    
    async saveCategory() {
      try {
        const url = this.showEditModal ? `/categories/${this.editingId}` : '/categories'
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
          await this.fetchCategories()
          this.closeModal()
          this.resetForm()
        }
      } catch (error) {
        console.error('Error saving category:', error)
      }
    },
    
    async deleteCategory(category) {
      if (confirm(`Are you sure you want to delete "${category.name}"?`)) {
        try {
          const response = await fetch(`/categories/${category.id}`, {
            method: 'DELETE',
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
          })
          
          if (response.ok) {
            await this.fetchCategories()
          }
        } catch (error) {
          console.error('Error deleting category:', error)
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
        color: '#3B82F6',
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
.category-color {
  width: 20px;
  height: 20px;
  border-radius: 50%;
  border: 2px solid #fff;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.category-color-large {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  border: 4px solid #fff;
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);
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
</style>
