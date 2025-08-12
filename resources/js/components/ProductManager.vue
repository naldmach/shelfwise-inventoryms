<template>
    <div class="product-manager-vue">
        <!-- Header -->
        <div class="row mb-4">
            <div
                class="col-12 d-flex justify-content-between align-items-center"
            >
                <h1 class="h3 mb-0">Products Management</h1>
                <button @click="showCreateModal = true" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add Product
                </button>
            </div>
        </div>

        <!-- Search and Filters -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <input
                                    v-model="searchQuery"
                                    @input="filterProducts"
                                    type="text"
                                    class="form-control"
                                    placeholder="Search products..."
                                />
                            </div>
                            <div class="col-md-3">
                                <select
                                    v-model="selectedCategory"
                                    @change="filterProducts"
                                    class="form-select"
                                >
                                    <option value="">All Categories</option>
                                    <option
                                        v-for="category in categories"
                                        :key="category.id"
                                        :value="category.id"
                                    >
                                        {{ category.name }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select
                                    v-model="selectedWarehouse"
                                    @change="filterProducts"
                                    class="form-select"
                                >
                                    <option value="">All Warehouses</option>
                                    <option
                                        v-for="warehouse in warehouses"
                                        :key="warehouse.id"
                                        :value="warehouse.id"
                                    >
                                        {{ warehouse.name }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button
                                    @click="resetFilters"
                                    class="btn btn-outline-secondary w-100"
                                >
                                    <i class="fas fa-times"></i> Clear
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products Table -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow">
                    <div
                        class="card-header d-flex justify-content-between align-items-center"
                    >
                        <h5 class="mb-0">Products List</h5>
                        <div class="d-flex align-items-center">
                            <span class="me-3 text-muted"
                                >{{ filteredProducts.length }} products</span
                            >
                            <button
                                @click="refreshProducts"
                                class="btn btn-sm btn-outline-primary"
                                :disabled="loading"
                            >
                                <i
                                    class="fas fa-sync-alt"
                                    :class="{ 'fa-spin': loading }"
                                ></i>
                                Refresh
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div v-if="loading" class="text-center py-4">
                            <div
                                class="spinner-border text-primary"
                                role="status"
                            >
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                        <div
                            v-else-if="filteredProducts.length > 0"
                            class="table-responsive"
                        >
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th
                                            @click="sortBy('sku')"
                                            style="cursor: pointer"
                                        >
                                            SKU
                                            <i
                                                class="fas fa-sort"
                                                :class="getSortIcon('sku')"
                                            ></i>
                                        </th>
                                        <th
                                            @click="sortBy('name')"
                                            style="cursor: pointer"
                                        >
                                            Name
                                            <i
                                                class="fas fa-sort"
                                                :class="getSortIcon('name')"
                                            ></i>
                                        </th>
                                        <th>Category</th>
                                        <th>Warehouse</th>
                                        <th
                                            @click="sortBy('quantity')"
                                            style="cursor: pointer"
                                        >
                                            Quantity
                                            <i
                                                class="fas fa-sort"
                                                :class="getSortIcon('quantity')"
                                            ></i>
                                        </th>
                                        <th
                                            @click="sortBy('price')"
                                            style="cursor: pointer"
                                        >
                                            Price
                                            <i
                                                class="fas fa-sort"
                                                :class="getSortIcon('price')"
                                            ></i>
                                        </th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="product in paginatedProducts"
                                        :key="product.id"
                                    >
                                        <td>
                                            <strong>{{ product.sku }}</strong>
                                        </td>
                                        <td>{{ product.name }}</td>
                                        <td>
                                            <span
                                                class="badge"
                                                :style="{
                                                    backgroundColor:
                                                        product.category
                                                            ?.color ||
                                                        '#6c757d',
                                                }"
                                            >
                                                {{
                                                    product.category?.name ||
                                                    "N/A"
                                                }}
                                            </span>
                                        </td>
                                        <td>
                                            {{
                                                product.warehouse?.name || "N/A"
                                            }}
                                        </td>
                                        <td>
                                            <span
                                                class="badge"
                                                :class="
                                                    getQuantityBadgeClass(
                                                        product
                                                    )
                                                "
                                            >
                                                {{ product.quantity }}
                                                {{ product.unit }}
                                            </span>
                                        </td>
                                        <td>
                                            ${{ formatPrice(product.price) }}
                                        </td>
                                        <td>
                                            <span
                                                class="badge"
                                                :class="
                                                    product.is_active
                                                        ? 'bg-success'
                                                        : 'bg-secondary'
                                                "
                                            >
                                                {{
                                                    product.is_active
                                                        ? "Active"
                                                        : "Inactive"
                                                }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button
                                                    @click="
                                                        viewProduct(product)
                                                    "
                                                    class="btn btn-sm btn-info"
                                                >
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button
                                                    @click="
                                                        editProduct(product)
                                                    "
                                                    class="btn btn-sm btn-warning"
                                                >
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button
                                                    @click="
                                                        deleteProduct(product)
                                                    "
                                                    class="btn btn-sm btn-danger"
                                                >
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div v-else class="text-center py-4">
                            <i class="fas fa-box fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Products Found</h5>
                            <p class="text-muted">
                                Try adjusting your search criteria or add a new
                                product.
                            </p>
                        </div>

                        <!-- Pagination -->
                        <div
                            v-if="totalPages > 1"
                            class="d-flex justify-content-center mt-4"
                        >
                            <nav>
                                <ul class="pagination">
                                    <li
                                        class="page-item"
                                        :class="{ disabled: currentPage === 1 }"
                                    >
                                        <button
                                            @click="changePage(currentPage - 1)"
                                            class="page-link"
                                        >
                                            Previous
                                        </button>
                                    </li>
                                    <li
                                        v-for="page in visiblePages"
                                        :key="page"
                                        class="page-item"
                                        :class="{
                                            active: page === currentPage,
                                        }"
                                    >
                                        <button
                                            @click="changePage(page)"
                                            class="page-link"
                                        >
                                            {{ page }}
                                        </button>
                                    </li>
                                    <li
                                        class="page-item"
                                        :class="{
                                            disabled:
                                                currentPage === totalPages,
                                        }"
                                    >
                                        <button
                                            @click="changePage(currentPage + 1)"
                                            class="page-link"
                                        >
                                            Next
                                        </button>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create/Edit Product Modal -->
        <div
            class="modal fade"
            :class="{ show: showCreateModal || showEditModal }"
            :style="{
                display: showCreateModal || showEditModal ? 'block' : 'none',
            }"
            tabindex="-1"
        >
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            {{ isEditing ? "Edit Product" : "Add New Product" }}
                        </h5>
                        <button
                            @click="closeModal"
                            type="button"
                            class="btn-close"
                        ></button>
                    </div>
                    <div class="modal-body">
                        <form @submit.prevent="saveProduct">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label"
                                        >Product Name *</label
                                    >
                                    <input
                                        v-model="productForm.name"
                                        type="text"
                                        class="form-control"
                                        required
                                    />
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">SKU *</label>
                                    <input
                                        v-model="productForm.sku"
                                        type="text"
                                        class="form-control"
                                        required
                                    />
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea
                                    v-model="productForm.description"
                                    class="form-control"
                                    rows="3"
                                ></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Category *</label>
                                    <select
                                        v-model="productForm.category_id"
                                        class="form-select"
                                        required
                                    >
                                        <option value="">
                                            Select Category
                                        </option>
                                        <option
                                            v-for="category in categories"
                                            :key="category.id"
                                            :value="category.id"
                                        >
                                            {{ category.name }}
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label"
                                        >Warehouse *</label
                                    >
                                    <select
                                        v-model="productForm.warehouse_id"
                                        class="form-select"
                                        required
                                    >
                                        <option value="">
                                            Select Warehouse
                                        </option>
                                        <option
                                            v-for="warehouse in warehouses"
                                            :key="warehouse.id"
                                            :value="warehouse.id"
                                        >
                                            {{ warehouse.name }}
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Price *</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input
                                            v-model="productForm.price"
                                            type="number"
                                            class="form-control"
                                            step="0.01"
                                            min="0"
                                            required
                                        />
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Quantity *</label>
                                    <input
                                        v-model="productForm.quantity"
                                        type="number"
                                        class="form-control"
                                        min="0"
                                        required
                                    />
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Unit *</label>
                                    <input
                                        v-model="productForm.unit"
                                        type="text"
                                        class="form-control"
                                        required
                                    />
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label"
                                        >Min Quantity *</label
                                    >
                                    <input
                                        v-model="productForm.min_quantity"
                                        type="number"
                                        class="form-control"
                                        min="0"
                                        required
                                    />
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label"
                                        >Max Quantity *</label
                                    >
                                    <input
                                        v-model="productForm.max_quantity"
                                        type="number"
                                        class="form-control"
                                        min="0"
                                        required
                                    />
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Barcode</label>
                                <input
                                    v-model="productForm.barcode"
                                    type="text"
                                    class="form-control"
                                />
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input
                                        v-model="productForm.is_active"
                                        class="form-check-input"
                                        type="checkbox"
                                        id="activeCheck"
                                    />
                                    <label
                                        class="form-check-label"
                                        for="activeCheck"
                                    >
                                        Product is active
                                    </label>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button
                            @click="closeModal"
                            type="button"
                            class="btn btn-secondary"
                        >
                            Cancel
                        </button>
                        <button
                            @click="saveProduct"
                            type="button"
                            class="btn btn-primary"
                            :disabled="saving"
                        >
                            <span
                                v-if="saving"
                                class="spinner-border spinner-border-sm me-2"
                            ></span>
                            {{ isEditing ? "Update" : "Create" }} Product
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product View Modal -->
        <div
            class="modal fade"
            :class="{ show: showViewModal }"
            :style="{ display: showViewModal ? 'block' : 'none' }"
            tabindex="-1"
        >
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Product Details</h5>
                        <button
                            @click="showViewModal = false"
                            type="button"
                            class="btn-close"
                        ></button>
                    </div>
                    <div class="modal-body" v-if="selectedProduct">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Basic Information</h6>
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Name:</strong></td>
                                        <td>{{ selectedProduct.name }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>SKU:</strong></td>
                                        <td>{{ selectedProduct.sku }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Description:</strong></td>
                                        <td>
                                            {{
                                                selectedProduct.description ||
                                                "N/A"
                                            }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Category:</strong></td>
                                        <td>
                                            <span
                                                class="badge"
                                                :style="{
                                                    backgroundColor:
                                                        selectedProduct.category
                                                            ?.color ||
                                                        '#6c757d',
                                                }"
                                            >
                                                {{
                                                    selectedProduct.category
                                                        ?.name || "N/A"
                                                }}
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6>Inventory Details</h6>
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Warehouse:</strong></td>
                                        <td>
                                            {{
                                                selectedProduct.warehouse
                                                    ?.name || "N/A"
                                            }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Current Stock:</strong></td>
                                        <td>
                                            <span
                                                class="badge"
                                                :class="
                                                    getQuantityBadgeClass(
                                                        selectedProduct
                                                    )
                                                "
                                            >
                                                {{ selectedProduct.quantity }}
                                                {{ selectedProduct.unit }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Min Quantity:</strong></td>
                                        <td>
                                            {{ selectedProduct.min_quantity }}
                                            {{ selectedProduct.unit }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Max Quantity:</strong></td>
                                        <td>
                                            {{ selectedProduct.max_quantity }}
                                            {{ selectedProduct.unit }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Price:</strong></td>
                                        <td>
                                            ${{
                                                formatPrice(
                                                    selectedProduct.price
                                                )
                                            }}
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Backdrop -->
        <div
            v-if="showCreateModal || showEditModal || showViewModal"
            class="modal-backdrop fade show"
            @click="closeModal"
        ></div>
    </div>
</template>

<script>
export default {
    name: "ProductManager",
    data() {
        return {
            loading: false,
            saving: false,
            products: [],
            categories: [],
            warehouses: [],
            searchQuery: "",
            selectedCategory: "",
            selectedWarehouse: "",
            sortField: "name",
            sortDirection: "asc",
            currentPage: 1,
            itemsPerPage: 10,
            showCreateModal: false,
            showEditModal: false,
            showViewModal: false,
            isEditing: false,
            selectedProduct: null,
            productForm: {
                name: "",
                sku: "",
                description: "",
                price: 0,
                quantity: 0,
                min_quantity: 0,
                max_quantity: 1000,
                unit: "pcs",
                barcode: "",
                category_id: "",
                warehouse_id: "",
                is_active: true,
            },
        };
    },
    computed: {
        filteredProducts() {
            let filtered = this.products;

            if (this.searchQuery) {
                const query = this.searchQuery.toLowerCase();
                filtered = filtered.filter(
                    (product) =>
                        product.name.toLowerCase().includes(query) ||
                        product.sku.toLowerCase().includes(query) ||
                        (product.description &&
                            product.description.toLowerCase().includes(query))
                );
            }

            if (this.selectedCategory) {
                filtered = filtered.filter(
                    (product) => product.category_id == this.selectedCategory
                );
            }

            if (this.selectedWarehouse) {
                filtered = filtered.filter(
                    (product) => product.warehouse_id == this.selectedWarehouse
                );
            }

            // Sort products
            filtered.sort((a, b) => {
                let aVal = a[this.sortField];
                let bVal = b[this.sortField];

                if (typeof aVal === "string") {
                    aVal = aVal.toLowerCase();
                    bVal = bVal.toLowerCase();
                }

                if (aVal < bVal) return this.sortDirection === "asc" ? -1 : 1;
                if (aVal > bVal) return this.sortDirection === "asc" ? 1 : -1;
                return 0;
            });

            return filtered;
        },
        paginatedProducts() {
            const start = (this.currentPage - 1) * this.itemsPerPage;
            const end = start + this.itemsPerPage;
            return this.filteredProducts.slice(start, end);
        },
        totalPages() {
            return Math.ceil(this.filteredProducts.length / this.itemsPerPage);
        },
        visiblePages() {
            const pages = [];
            const start = Math.max(1, this.currentPage - 2);
            const end = Math.min(this.totalPages, this.currentPage + 2);

            for (let i = start; i <= end; i++) {
                pages.push(i);
            }
            return pages;
        },
    },
    mounted() {
        this.loadData();
    },
    methods: {
        async loadData() {
            try {
                this.loading = true;
                await Promise.all([
                    this.loadProducts(),
                    this.loadCategories(),
                    this.loadWarehouses(),
                ]);
            } catch (error) {
                console.error("Error loading data:", error);
            } finally {
                this.loading = false;
            }
        },

        async loadProducts() {
            try {
                const response = await fetch("/api/products");
                if (response.ok) {
                    this.products = await response.json();
                }
            } catch (error) {
                console.error("Error loading products:", error);
                // Fallback to sample data
                this.products = [
                    {
                        id: 1,
                        name: "Laptop Computer",
                        sku: "LAP-001",
                        description: "High-performance laptop",
                        price: 999.99,
                        quantity: 25,
                        min_quantity: 5,
                        max_quantity: 100,
                        unit: "pcs",
                        category_id: 1,
                        warehouse_id: 1,
                        is_active: true,
                        category: { name: "Electronics", color: "#3B82F6" },
                        warehouse: { name: "Main Warehouse" },
                    },
                    {
                        id: 2,
                        name: "Smartphone",
                        sku: "PHN-001",
                        description: "Latest smartphone",
                        price: 699.99,
                        quantity: 50,
                        min_quantity: 10,
                        max_quantity: 200,
                        unit: "pcs",
                        category_id: 1,
                        warehouse_id: 1,
                        is_active: true,
                        category: { name: "Electronics", color: "#3B82F6" },
                        warehouse: { name: "Main Warehouse" },
                    },
                ];
            }
        },

        async loadCategories() {
            try {
                const response = await fetch("/api/categories");
                if (response.ok) {
                    this.categories = await response.json();
                }
            } catch (error) {
                console.error("Error loading categories:", error);
                // Fallback to sample data
                this.categories = [
                    { id: 1, name: "Electronics", color: "#3B82F6" },
                    { id: 2, name: "Clothing", color: "#10B981" },
                ];
            }
        },

        async loadWarehouses() {
            try {
                const response = await fetch("/api/warehouses");
                if (response.ok) {
                    this.warehouses = await response.json();
                }
            } catch (error) {
                console.error("Error loading warehouses:", error);
                // Fallback to sample data
                this.warehouses = [
                    { id: 1, name: "Main Warehouse" },
                    { id: 2, name: "West Coast Distribution" },
                ];
            }
        },

        filterProducts() {
            this.currentPage = 1;
        },

        resetFilters() {
            this.searchQuery = "";
            this.selectedCategory = "";
            this.selectedWarehouse = "";
            this.currentPage = 1;
        },

        sortBy(field) {
            if (this.sortField === field) {
                this.sortDirection =
                    this.sortDirection === "asc" ? "desc" : "asc";
            } else {
                this.sortField = field;
                this.sortDirection = "asc";
            }
        },

        getSortIcon(field) {
            if (this.sortField !== field) return "fa-sort";
            return this.sortDirection === "asc" ? "fa-sort-up" : "fa-sort-down";
        },

        changePage(page) {
            if (page >= 1 && page <= this.totalPages) {
                this.currentPage = page;
            }
        },

        refreshProducts() {
            this.loadProducts();
        },

        viewProduct(product) {
            this.selectedProduct = product;
            this.showViewModal = true;
        },

        editProduct(product) {
            this.isEditing = true;
            this.selectedProduct = product;
            this.productForm = { ...product };
            this.showEditModal = true;
        },

        createProduct() {
            this.isEditing = false;
            this.productForm = {
                name: "",
                sku: "",
                description: "",
                price: 0,
                quantity: 0,
                min_quantity: 0,
                max_quantity: 1000,
                unit: "pcs",
                barcode: "",
                category_id: "",
                warehouse_id: "",
                is_active: true,
            };
            this.showCreateModal = true;
        },

        async saveProduct() {
            try {
                this.saving = true;
                const url = this.isEditing
                    ? `/api/products/${this.selectedProduct.id}`
                    : "/api/products";
                const method = this.isEditing ? "PUT" : "POST";

                const response = await fetch(url, {
                    method: method,
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document
                            .querySelector('meta[name="csrf-token"]')
                            ?.getAttribute("content"),
                    },
                    body: JSON.stringify(this.productForm),
                });

                if (response.ok) {
                    this.closeModal();
                    this.loadProducts();
                    // Show success message
                    this.showAlert("Product saved successfully!", "success");
                } else {
                    throw new Error("Failed to save product");
                }
            } catch (error) {
                console.error("Error saving product:", error);
                this.showAlert(
                    "Error saving product. Please try again.",
                    "danger"
                );
            } finally {
                this.saving = false;
            }
        },

        async deleteProduct(product) {
            if (
                !confirm(`Are you sure you want to delete "${product.name}"?`)
            ) {
                return;
            }

            try {
                const response = await fetch(`/api/products/${product.id}`, {
                    method: "DELETE",
                    headers: {
                        "X-CSRF-TOKEN": document
                            .querySelector('meta[name="csrf-token"]')
                            ?.getAttribute("content"),
                    },
                });

                if (response.ok) {
                    this.loadProducts();
                    this.showAlert("Product deleted successfully!", "success");
                } else {
                    throw new Error("Failed to delete product");
                }
            } catch (error) {
                console.error("Error deleting product:", error);
                this.showAlert(
                    "Error deleting product. Please try again.",
                    "danger"
                );
            }
        },

        closeModal() {
            this.showCreateModal = false;
            this.showEditModal = false;
            this.showViewModal = false;
            this.selectedProduct = null;
            this.isEditing = false;
        },

        getQuantityBadgeClass(product) {
            if (product.quantity <= product.min_quantity) return "bg-warning";
            if (product.quantity === 0) return "bg-danger";
            return "bg-success";
        },

        formatPrice(price) {
            return parseFloat(price).toFixed(2);
        },

        showAlert(message, type) {
            // Create and show a Bootstrap alert
            const alertDiv = document.createElement("div");
            alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
            alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      `;

            const container = document.querySelector(".product-manager-vue");
            container.insertBefore(alertDiv, container.firstChild);

            // Auto-remove after 5 seconds
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
        },
    },
};
</script>

<style scoped>
.modal.show {
    display: block !important;
}
.modal-backdrop.show {
    opacity: 0.5;
}
</style>
