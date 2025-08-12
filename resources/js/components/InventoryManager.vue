<template>
    <div class="inventory-manager-vue">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="h3 mb-0">Inventory Management</h1>
                <p class="text-muted">
                    Manage stock levels and perform inventory operations
                </p>
            </div>
        </div>

        <!-- Success/Error Messages -->
        <div v-if="message" class="alert" :class="messageClass" role="alert">
            {{ message }}
            <button
                @click="clearMessage"
                type="button"
                class="btn-close"
            ></button>
        </div>

        <!-- Stock Operations -->
        <div class="row">
            <!-- Stock In Form -->
            <div class="col-lg-4 mb-4">
                <div class="card shadow border-success">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-plus-circle"></i> Stock In
                        </h5>
                    </div>
                    <div class="card-body">
                        <form @submit.prevent="performStockIn">
                            <div class="mb-3">
                                <label class="form-label">Product *</label>
                                <select
                                    v-model="stockInForm.product_id"
                                    class="form-select"
                                    required
                                >
                                    <option value="">Select Product</option>
                                    <option
                                        v-for="product in products"
                                        :key="product.id"
                                        :value="product.id"
                                        :data-warehouse="product.warehouse_id"
                                    >
                                        {{ product.sku }} - {{ product.name }}
                                    </option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Warehouse *</label>
                                <select
                                    v-model="stockInForm.warehouse_id"
                                    class="form-select"
                                    required
                                >
                                    <option value="">Select Warehouse</option>
                                    <option
                                        v-for="warehouse in warehouses"
                                        :key="warehouse.id"
                                        :value="warehouse.id"
                                    >
                                        {{ warehouse.name }}
                                    </option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Quantity *</label>
                                <input
                                    v-model="stockInForm.quantity"
                                    type="number"
                                    class="form-control"
                                    min="1"
                                    required
                                />
                            </div>

                            <div class="mb-3">
                                <label class="form-label"
                                    >Reference Number</label
                                >
                                <input
                                    v-model="stockInForm.reference_number"
                                    type="text"
                                    class="form-control"
                                    placeholder="PO, Invoice, etc."
                                />
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Notes</label>
                                <textarea
                                    v-model="stockInForm.notes"
                                    class="form-control"
                                    rows="2"
                                ></textarea>
                            </div>

                            <button
                                type="submit"
                                class="btn btn-success w-100"
                                :disabled="processing"
                            >
                                <span
                                    v-if="processing"
                                    class="spinner-border spinner-border-sm me-2"
                                ></span>
                                <i class="fas fa-plus"></i> Add Stock
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Stock Out Form -->
            <div class="col-lg-4 mb-4">
                <div class="card shadow border-danger">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-minus-circle"></i> Stock Out
                        </h5>
                    </div>
                    <div class="card-body">
                        <form @submit.prevent="performStockOut">
                            <div class="mb-3">
                                <label class="form-label">Product *</label>
                                <select
                                    v-model="stockOutForm.product_id"
                                    class="form-select"
                                    required
                                >
                                    <option value="">Select Product</option>
                                    <option
                                        v-for="product in products"
                                        :key="product.id"
                                        :value="product.id"
                                        :data-warehouse="product.warehouse_id"
                                    >
                                        {{ product.sku }} -
                                        {{ product.name }} ({{
                                            product.quantity
                                        }}
                                        available)
                                    </option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Warehouse *</label>
                                <select
                                    v-model="stockOutForm.warehouse_id"
                                    class="form-select"
                                    required
                                >
                                    <option value="">Select Warehouse</option>
                                    <option
                                        v-for="warehouse in warehouses"
                                        :key="warehouse.id"
                                        :value="warehouse.id"
                                    >
                                        {{ warehouse.name }}
                                    </option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Quantity *</label>
                                <input
                                    v-model="stockOutForm.quantity"
                                    type="number"
                                    class="form-control"
                                    min="1"
                                    required
                                />
                                <small class="text-muted"
                                    >Available:
                                    {{
                                        getAvailableQuantity(
                                            stockOutForm.product_id
                                        )
                                    }}</small
                                >
                            </div>

                            <div class="mb-3">
                                <label class="form-label"
                                    >Reference Number</label
                                >
                                <input
                                    v-model="stockOutForm.reference_number"
                                    type="text"
                                    class="form-control"
                                    placeholder="SO, Invoice, etc."
                                />
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Notes</label>
                                <textarea
                                    v-model="stockOutForm.notes"
                                    class="form-control"
                                    rows="2"
                                ></textarea>
                            </div>

                            <button
                                type="submit"
                                class="btn btn-danger w-100"
                                :disabled="processing"
                            >
                                <span
                                    v-if="processing"
                                    class="spinner-border spinner-border-sm me-2"
                                ></span>
                                <i class="fas fa-minus"></i> Remove Stock
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Stock Adjustment Form -->
            <div class="col-lg-4 mb-4">
                <div class="card shadow border-warning">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0">
                            <i class="fas fa-edit"></i> Stock Adjustment
                        </h5>
                    </div>
                    <div class="card-body">
                        <form @submit.prevent="performStockAdjustment">
                            <div class="mb-3">
                                <label class="form-label">Product *</label>
                                <select
                                    v-model="adjustForm.product_id"
                                    class="form-select"
                                    required
                                >
                                    <option value="">Select Product</option>
                                    <option
                                        v-for="product in products"
                                        :key="product.id"
                                        :value="product.id"
                                        :data-warehouse="product.warehouse_id"
                                    >
                                        {{ product.sku }} -
                                        {{ product.name }} (Current:
                                        {{ product.quantity }})
                                    </option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Warehouse *</label>
                                <select
                                    v-model="adjustForm.warehouse_id"
                                    class="form-select"
                                    required
                                >
                                    <option value="">Select Warehouse</option>
                                    <option
                                        v-for="warehouse in warehouses"
                                        :key="warehouse.id"
                                        :value="warehouse.id"
                                    >
                                        {{ warehouse.name }}
                                    </option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">New Quantity *</label>
                                <input
                                    v-model="adjustForm.new_quantity"
                                    type="number"
                                    class="form-control"
                                    min="0"
                                    required
                                />
                                <small class="text-muted"
                                    >Current:
                                    {{
                                        getCurrentQuantity(
                                            adjustForm.product_id
                                        )
                                    }}</small
                                >
                            </div>

                            <div class="mb-3">
                                <label class="form-label"
                                    >Reason for Adjustment *</label
                                >
                                <textarea
                                    v-model="adjustForm.notes"
                                    class="form-control"
                                    rows="2"
                                    placeholder="Explain why this adjustment is needed"
                                    required
                                ></textarea>
                            </div>

                            <button
                                type="submit"
                                class="btn btn-warning w-100"
                                :disabled="processing"
                            >
                                <span
                                    v-if="processing"
                                    class="spinner-border spinner-border-sm me-2"
                                ></span>
                                <i class="fas fa-edit"></i> Adjust Stock
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header">
                        <h5 class="mb-0">Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <button
                                    @click="
                                        navigateTo('inventory.transactions')
                                    "
                                    class="btn btn-outline-primary w-100"
                                >
                                    <i class="fas fa-history"></i><br />
                                    View Transaction History
                                </button>
                            </div>
                            <div class="col-md-3 mb-3">
                                <button
                                    @click="navigateTo('products.index')"
                                    class="btn btn-outline-success w-100"
                                >
                                    <i class="fas fa-boxes"></i><br />
                                    Manage Products
                                </button>
                            </div>
                            <div class="col-md-3 mb-3">
                                <button
                                    @click="navigateTo('alerts.index')"
                                    class="btn btn-outline-warning w-100"
                                >
                                    <i class="fas fa-exclamation-triangle"></i
                                    ><br />
                                    View Alerts
                                </button>
                            </div>
                            <div class="col-md-3 mb-3">
                                <button
                                    @click="navigateTo('reports.index')"
                                    class="btn btn-outline-info w-100"
                                >
                                    <i class="fas fa-chart-bar"></i><br />
                                    Generate Reports
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card shadow">
                    <div
                        class="card-header d-flex justify-content-between align-items-center"
                    >
                        <h5 class="mb-0">Recent Transactions</h5>
                        <button
                            @click="loadRecentTransactions"
                            class="btn btn-sm btn-outline-primary"
                            :disabled="loadingTransactions"
                        >
                            <i
                                class="fas fa-sync-alt"
                                :class="{ 'fa-spin': loadingTransactions }"
                            ></i>
                            Refresh
                        </button>
                    </div>
                    <div class="card-body">
                        <div
                            v-if="loadingTransactions"
                            class="text-center py-4"
                        >
                            <div
                                class="spinner-border text-primary"
                                role="status"
                            >
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                        <div
                            v-else-if="recentTransactions.length > 0"
                            class="table-responsive"
                        >
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Type</th>
                                        <th>Quantity</th>
                                        <th>Warehouse</th>
                                        <th>Date</th>
                                        <th>Reference</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="transaction in recentTransactions"
                                        :key="transaction.id"
                                    >
                                        <td>
                                            {{
                                                transaction.product?.name ||
                                                "N/A"
                                            }}
                                        </td>
                                        <td>
                                            <span
                                                class="badge"
                                                :class="
                                                    getTransactionBadgeClass(
                                                        transaction.type
                                                    )
                                                "
                                            >
                                                {{
                                                    formatTransactionType(
                                                        transaction.type
                                                    )
                                                }}
                                            </span>
                                        </td>
                                        <td>{{ transaction.quantity }}</td>
                                        <td>
                                            {{
                                                transaction.warehouse?.name ||
                                                "N/A"
                                            }}
                                        </td>
                                        <td>
                                            {{
                                                formatDate(
                                                    transaction.created_at
                                                )
                                            }}
                                        </td>
                                        <td>
                                            {{
                                                transaction.reference_number ||
                                                "N/A"
                                            }}
                                        </td>
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
        </div>

        <!-- Low Stock Alerts -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card shadow border-warning">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0">
                            <i class="fas fa-exclamation-triangle"></i> Low
                            Stock Alerts
                        </h5>
                    </div>
                    <div class="card-body">
                        <div v-if="lowStockProducts.length > 0">
                            <div class="row">
                                <div
                                    v-for="product in lowStockProducts"
                                    :key="product.id"
                                    class="col-md-6 mb-3"
                                >
                                    <div
                                        class="d-flex justify-content-between align-items-center p-3 border rounded"
                                    >
                                        <div>
                                            <strong>{{ product.name }}</strong
                                            ><br />
                                            <small class="text-muted">{{
                                                product.sku
                                            }}</small>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-warning"
                                                >{{ product.quantity }}
                                                {{ product.unit }}</span
                                            ><br />
                                            <small class="text-muted"
                                                >Min:
                                                {{
                                                    product.min_quantity
                                                }}</small
                                            >
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-4">
                            <p class="text-muted">No low stock alerts</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "InventoryManager",
    data() {
        return {
            loadingTransactions: false,
            processing: false,
            products: [],
            warehouses: [],
            recentTransactions: [],
            lowStockProducts: [],
            message: "",
            messageClass: "",
            stockInForm: {
                product_id: "",
                warehouse_id: "",
                quantity: 1,
                reference_number: "",
                notes: "",
            },
            stockOutForm: {
                product_id: "",
                warehouse_id: "",
                quantity: 1,
                reference_number: "",
                notes: "",
            },
            adjustForm: {
                product_id: "",
                warehouse_id: "",
                new_quantity: 0,
                notes: "",
            },
        };
    },
    mounted() {
        this.loadData();
        this.startAutoRefresh();
    },
    methods: {
        async loadData() {
            try {
                await Promise.all([
                    this.loadProducts(),
                    this.loadWarehouses(),
                    this.loadRecentTransactions(),
                    this.loadLowStockProducts(),
                ]);
            } catch (error) {
                console.error("Error loading data:", error);
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
                        quantity: 25,
                        min_quantity: 5,
                        unit: "pcs",
                        warehouse_id: 1,
                    },
                    {
                        id: 2,
                        name: "Smartphone",
                        sku: "PHN-001",
                        quantity: 50,
                        min_quantity: 10,
                        unit: "pcs",
                        warehouse_id: 1,
                    },
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

        async loadRecentTransactions() {
            try {
                this.loadingTransactions = true;
                const response = await fetch("/api/inventory/transactions");
                if (response.ok) {
                    this.recentTransactions = await response.json();
                }
            } catch (error) {
                console.error("Error loading transactions:", error);
                // Fallback to sample data
                this.recentTransactions = [
                    {
                        id: 1,
                        type: "in",
                        quantity: 10,
                        product: { name: "Laptop Computer" },
                        warehouse: { name: "Main Warehouse" },
                        created_at: new Date(),
                        reference_number: "PO-001",
                    },
                    {
                        id: 2,
                        type: "out",
                        quantity: 5,
                        product: { name: "Smartphone" },
                        warehouse: { name: "Main Warehouse" },
                        created_at: new Date(),
                        reference_number: "SO-001",
                    },
                ];
            } finally {
                this.loadingTransactions = false;
            }
        },

        async loadLowStockProducts() {
            try {
                const response = await fetch("/api/products/low-stock");
                if (response.ok) {
                    this.lowStockProducts = await response.json();
                }
            } catch (error) {
                console.error("Error loading low stock products:", error);
                // Fallback to sample data
                this.lowStockProducts = [
                    {
                        id: 1,
                        name: "Laptop Computer",
                        sku: "LAP-001",
                        quantity: 3,
                        min_quantity: 5,
                        unit: "pcs",
                    },
                    {
                        id: 2,
                        name: "Garden Tool Set",
                        sku: "GRD-001",
                        quantity: 2,
                        min_quantity: 3,
                        unit: "sets",
                    },
                ];
            }
        },

        async performStockIn() {
            try {
                this.processing = true;
                const response = await fetch("/api/inventory/stock-in", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document
                            .querySelector('meta[name="csrf-token"]')
                            ?.getAttribute("content"),
                    },
                    body: JSON.stringify(this.stockInForm),
                });

                if (response.ok) {
                    this.showMessage("Stock added successfully!", "success");
                    this.resetStockInForm();
                    this.loadProducts();
                    this.loadRecentTransactions();
                } else {
                    throw new Error("Failed to add stock");
                }
            } catch (error) {
                console.error("Error adding stock:", error);
                this.showMessage(
                    "Error adding stock. Please try again.",
                    "danger"
                );
            } finally {
                this.processing = false;
            }
        },

        async performStockOut() {
            try {
                this.processing = true;
                const response = await fetch("/api/inventory/stock-out", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document
                            .querySelector('meta[name="csrf-token"]')
                            ?.getAttribute("content"),
                    },
                    body: JSON.stringify(this.stockOutForm),
                });

                if (response.ok) {
                    this.showMessage("Stock removed successfully!", "success");
                    this.resetStockOutForm();
                    this.loadProducts();
                    this.loadRecentTransactions();
                } else {
                    throw new Error("Failed to remove stock");
                }
            } catch (error) {
                console.error("Error removing stock:", error);
                this.showMessage(
                    "Error removing stock. Please try again.",
                    "danger"
                );
            } finally {
                this.processing = false;
            }
        },

        async performStockAdjustment() {
            try {
                this.processing = true;
                const response = await fetch("/api/inventory/adjust", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document
                            .querySelector('meta[name="csrf-token"]')
                            ?.getAttribute("content"),
                    },
                    body: JSON.stringify(this.adjustForm),
                });

                if (response.ok) {
                    this.showMessage("Stock adjusted successfully!", "success");
                    this.resetAdjustForm();
                    this.loadProducts();
                    this.loadRecentTransactions();
                } else {
                    throw new Error("Failed to adjust stock");
                }
            } catch (error) {
                console.error("Error adjusting stock:", error);
                this.showMessage(
                    "Error adjusting stock. Please try again.",
                    "danger"
                );
            } finally {
                this.processing = false;
            }
        },

        resetStockInForm() {
            this.stockInForm = {
                product_id: "",
                warehouse_id: "",
                quantity: 1,
                reference_number: "",
                notes: "",
            };
        },

        resetStockOutForm() {
            this.stockOutForm = {
                product_id: "",
                warehouse_id: "",
                quantity: 1,
                reference_number: "",
                notes: "",
            };
        },

        resetAdjustForm() {
            this.adjustForm = {
                product_id: "",
                warehouse_id: "",
                new_quantity: 0,
                notes: "",
            };
        },

        getAvailableQuantity(productId) {
            const product = this.products.find((p) => p.id == productId);
            return product ? product.quantity : 0;
        },

        getCurrentQuantity(productId) {
            const product = this.products.find((p) => p.id == productId);
            return product ? product.quantity : 0;
        },

        getTransactionBadgeClass(type) {
            const classes = {
                in: "bg-success",
                out: "bg-danger",
                adjustment: "bg-info",
                transfer: "bg-warning",
            };
            return classes[type] || "bg-secondary";
        },

        formatTransactionType(type) {
            return type.charAt(0).toUpperCase() + type.slice(1);
        },

        formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString("en-US", {
                month: "short",
                day: "numeric",
                year: "numeric",
                hour: "2-digit",
                minute: "2-digit",
            });
        },

        showMessage(message, type) {
            this.message = message;
            this.messageClass = `alert-${type}`;
            setTimeout(() => {
                this.clearMessage();
            }, 5000);
        },

        clearMessage() {
            this.message = "";
            this.messageClass = "";
        },

        navigateTo(route) {
            window.location.href = `/${route}`;
        },

        startAutoRefresh() {
            // Refresh data every 30 seconds
            setInterval(() => {
                this.loadLowStockProducts();
            }, 30000);
        },
    },
};
</script>

<style scoped>
.alert {
    margin-bottom: 1rem;
}
</style>
