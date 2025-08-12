<template>
    <div class="alert-manager-vue">
        <!-- Header -->
        <div class="row mb-4">
            <div
                class="col-12 d-flex justify-content-between align-items-center"
            >
                <div>
                    <h1 class="h3 mb-0">Alerts & Notifications</h1>
                    <p class="text-muted">
                        System alerts and important notifications
                    </p>
                </div>
                <div>
                    <button
                        @click="refreshAlerts"
                        class="btn btn-outline-primary me-2"
                        :disabled="loading"
                    >
                        <i
                            class="fas fa-sync-alt"
                            :class="{ 'fa-spin': loading }"
                        ></i>
                        Refresh
                    </button>
                    <button
                        @click="markAllAsRead"
                        class="btn btn-success"
                        :disabled="loading"
                    >
                        <i class="fas fa-check-double"></i> Mark All as Read
                    </button>
                </div>
            </div>
        </div>

        <!-- Alert Filters -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">Priority</label>
                                <select
                                    v-model="filters.priority"
                                    @change="applyFilters"
                                    class="form-select"
                                >
                                    <option value="">All Priorities</option>
                                    <option value="low">Low</option>
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                    <option value="critical">Critical</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Type</label>
                                <select
                                    v-model="filters.type"
                                    @change="applyFilters"
                                    class="form-select"
                                >
                                    <option value="">All Types</option>
                                    <option value="low_stock">Low Stock</option>
                                    <option value="out_of_stock">
                                        Out of Stock
                                    </option>
                                    <option value="expiry">Expiry</option>
                                    <option value="system">System</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Status</label>
                                <select
                                    v-model="filters.status"
                                    @change="applyFilters"
                                    class="form-select"
                                >
                                    <option value="">All Status</option>
                                    <option value="unread">Unread</option>
                                    <option value="read">Read</option>
                                </select>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button
                                    @click="clearFilters"
                                    class="btn btn-outline-secondary w-100"
                                >
                                    <i class="fas fa-times"></i> Clear Filters
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alerts Summary -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card border-primary text-center">
                    <div class="card-body">
                        <h3 class="text-primary">{{ totalAlerts }}</h3>
                        <p class="mb-0">Total Alerts</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card border-warning text-center">
                    <div class="card-body">
                        <h3 class="text-warning">{{ unreadAlerts }}</h3>
                        <p class="mb-0">Unread</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card border-danger text-center">
                    <div class="card-body">
                        <h3 class="text-danger">{{ criticalAlerts }}</h3>
                        <p class="mb-0">Critical</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card border-info text-center">
                    <div class="card-body">
                        <h3 class="text-info">{{ highPriorityAlerts }}</h3>
                        <p class="mb-0">High Priority</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alerts List -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow">
                    <div
                        class="card-header d-flex justify-content-between align-items-center"
                    >
                        <h5 class="mb-0">System Alerts</h5>
                        <div class="d-flex align-items-center">
                            <span class="me-3 text-muted"
                                >{{ filteredAlerts.length }} alerts</span
                            >
                            <div class="form-check form-switch">
                                <input
                                    v-model="autoRefresh"
                                    class="form-check-input"
                                    type="checkbox"
                                    id="autoRefresh"
                                />
                                <label
                                    class="form-check-label"
                                    for="autoRefresh"
                                    >Auto-refresh</label
                                >
                            </div>
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
                        <div v-else-if="filteredAlerts.length > 0">
                            <div
                                v-for="alert in paginatedAlerts"
                                :key="alert.id"
                                class="alert alert-dismissible fade show mb-3"
                                :class="getAlertClass(alert.priority)"
                            >
                                <div
                                    class="d-flex justify-content-between align-items-start"
                                >
                                    <div class="flex-grow-1">
                                        <div
                                            class="d-flex align-items-center mb-2"
                                        >
                                            <i
                                                :class="
                                                    getAlertIcon(alert.type)
                                                "
                                                class="me-2"
                                            ></i>
                                            <h6 class="alert-heading mb-0">
                                                {{ alert.title }}
                                            </h6>
                                            <span
                                                class="badge ms-2"
                                                :class="
                                                    getPriorityBadgeClass(
                                                        alert.priority
                                                    )
                                                "
                                            >
                                                {{ alert.priority }}
                                            </span>
                                            <span
                                                class="badge ms-2 bg-secondary"
                                                >{{ alert.type }}</span
                                            >
                                        </div>
                                        <p class="mb-1">{{ alert.message }}</p>
                                        <div
                                            class="d-flex align-items-center text-muted small"
                                        >
                                            <span class="me-3">
                                                <i class="fas fa-clock"></i>
                                                {{
                                                    formatDate(alert.created_at)
                                                }}
                                            </span>
                                            <span
                                                v-if="alert.product"
                                                class="me-3"
                                            >
                                                <i class="fas fa-box"></i>
                                                {{ alert.product.name }}
                                            </span>
                                            <span
                                                v-if="alert.warehouse"
                                                class="me-3"
                                            >
                                                <i class="fas fa-warehouse"></i>
                                                {{ alert.warehouse.name }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ms-3 d-flex flex-column">
                                        <button
                                            v-if="!alert.is_read"
                                            @click="markAsRead(alert)"
                                            class="btn btn-sm btn-outline-success mb-2"
                                        >
                                            <i class="fas fa-check"></i> Mark
                                            Read
                                        </button>
                                        <span
                                            v-else
                                            class="badge bg-success mb-2"
                                            >Read</span
                                        >
                                        <button
                                            @click="deleteAlert(alert)"
                                            class="btn btn-sm btn-outline-danger"
                                        >
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
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
                                            :class="{
                                                disabled: currentPage === 1,
                                            }"
                                        >
                                            <button
                                                @click="
                                                    changePage(currentPage - 1)
                                                "
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
                                                @click="
                                                    changePage(currentPage + 1)
                                                "
                                                class="page-link"
                                            >
                                                Next
                                            </button>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                        <div v-else class="text-center py-4">
                            <i
                                class="fas fa-check-circle fa-3x text-success mb-3"
                            ></i>
                            <h5 class="text-success">All Caught Up!</h5>
                            <p class="text-muted">
                                No alerts to display at the moment.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alert Details Modal -->
        <div
            class="modal fade"
            :class="{ show: showDetailModal }"
            :style="{ display: showDetailModal ? 'block' : 'none' }"
            tabindex="-1"
        >
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Alert Details</h5>
                        <button
                            @click="closeDetailModal"
                            type="button"
                            class="btn-close"
                        ></button>
                    </div>
                    <div class="modal-body" v-if="selectedAlert">
                        <div class="row">
                            <div class="col-md-8">
                                <h6>Alert Information</h6>
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Title:</strong></td>
                                        <td>{{ selectedAlert.title }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Message:</strong></td>
                                        <td>{{ selectedAlert.message }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Type:</strong></td>
                                        <td>
                                            <span class="badge bg-secondary">{{
                                                selectedAlert.type
                                            }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Priority:</strong></td>
                                        <td>
                                            <span
                                                class="badge"
                                                :class="
                                                    getPriorityBadgeClass(
                                                        selectedAlert.priority
                                                    )
                                                "
                                            >
                                                {{ selectedAlert.priority }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Status:</strong></td>
                                        <td>
                                            <span
                                                class="badge"
                                                :class="
                                                    selectedAlert.is_read
                                                        ? 'bg-success'
                                                        : 'bg-warning'
                                                "
                                            >
                                                {{
                                                    selectedAlert.is_read
                                                        ? "Read"
                                                        : "Unread"
                                                }}
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-4">
                                <h6>Related Information</h6>
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Created:</strong></td>
                                        <td>
                                            {{
                                                formatDate(
                                                    selectedAlert.created_at
                                                )
                                            }}
                                        </td>
                                    </tr>
                                    <tr v-if="selectedAlert.product">
                                        <td><strong>Product:</strong></td>
                                        <td>
                                            {{ selectedAlert.product.name }}
                                        </td>
                                    </tr>
                                    <tr v-if="selectedAlert.warehouse">
                                        <td><strong>Warehouse:</strong></td>
                                        <td>
                                            {{ selectedAlert.warehouse.name }}
                                        </td>
                                    </tr>
                                    <tr v-if="selectedAlert.expires_at">
                                        <td><strong>Expires:</strong></td>
                                        <td>
                                            {{
                                                formatDate(
                                                    selectedAlert.expires_at
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
            v-if="showDetailModal"
            class="modal-backdrop fade show"
            @click="closeDetailModal"
        ></div>
    </div>
</template>

<script>
export default {
    name: "AlertManager",
    data() {
        return {
            loading: false,
            alerts: [],
            filters: {
                priority: "",
                type: "",
                status: "",
            },
            currentPage: 1,
            itemsPerPage: 10,
            showDetailModal: false,
            selectedAlert: null,
            autoRefresh: true,
        };
    },
    computed: {
        filteredAlerts() {
            let filtered = this.alerts;

            if (this.filters.priority) {
                filtered = filtered.filter(
                    (alert) => alert.priority === this.filters.priority
                );
            }

            if (this.filters.type) {
                filtered = filtered.filter(
                    (alert) => alert.type === this.filters.type
                );
            }

            if (this.filters.status === "read") {
                filtered = filtered.filter((alert) => alert.is_read);
            } else if (this.filters.status === "unread") {
                filtered = filtered.filter((alert) => !alert.is_read);
            }

            return filtered;
        },
        paginatedAlerts() {
            const start = (this.currentPage - 1) * this.itemsPerPage;
            const end = start + this.itemsPerPage;
            return this.filteredAlerts.slice(start, end);
        },
        totalPages() {
            return Math.ceil(this.filteredAlerts.length / this.itemsPerPage);
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
        totalAlerts() {
            return this.alerts.length;
        },
        unreadAlerts() {
            return this.alerts.filter((alert) => !alert.is_read).length;
        },
        criticalAlerts() {
            return this.alerts.filter((alert) => alert.priority === "critical")
                .length;
        },
        highPriorityAlerts() {
            return this.alerts.filter((alert) =>
                ["high", "critical"].includes(alert.priority)
            ).length;
        },
    },
    mounted() {
        this.loadAlerts();
        this.startAutoRefresh();
    },
    methods: {
        async loadAlerts() {
            try {
                this.loading = true;
                const response = await fetch("/api/alerts");
                if (response.ok) {
                    this.alerts = await response.json();
                }
            } catch (error) {
                console.error("Error loading alerts:", error);
                // Fallback to sample data
                this.alerts = [
                    {
                        id: 1,
                        title: "Low Stock Alert",
                        message:
                            'Product "Laptop Computer" is running low on stock. Current quantity: 3 (Min: 5)',
                        type: "low_stock",
                        priority: "high",
                        is_read: false,
                        created_at: new Date(),
                        product: { name: "Laptop Computer" },
                        warehouse: { name: "Main Warehouse" },
                    },
                    {
                        id: 2,
                        title: "Out of Stock Alert",
                        message:
                            'Product "Garden Tool Set" is completely out of stock.',
                        type: "out_of_stock",
                        priority: "critical",
                        is_read: false,
                        created_at: new Date(Date.now() - 86400000),
                        product: { name: "Garden Tool Set" },
                        warehouse: { name: "Main Warehouse" },
                    },
                    {
                        id: 3,
                        title: "System Notification",
                        message:
                            "Daily inventory backup completed successfully.",
                        type: "system",
                        priority: "low",
                        is_read: true,
                        created_at: new Date(Date.now() - 259200000),
                    },
                    {
                        id: 4,
                        title: "Stock Replenished",
                        message:
                            'Product "Smartphone" has been restocked. New quantity: 75',
                        type: "low_stock",
                        priority: "medium",
                        is_read: true,
                        created_at: new Date(Date.now() - 604800000),
                        product: { name: "Smartphone" },
                        warehouse: { name: "Main Warehouse" },
                    },
                ];
            } finally {
                this.loading = false;
            }
        },

        async refreshAlerts() {
            await this.loadAlerts();
        },

        async markAsRead(alert) {
            try {
                const response = await fetch(
                    `/api/alerts/${alert.id}/mark-read`,
                    {
                        method: "POST",
                        headers: {
                            "X-CSRF-TOKEN": document
                                .querySelector('meta[name="csrf-token"]')
                                ?.getAttribute("content"),
                        },
                    }
                );

                if (response.ok) {
                    alert.is_read = true;
                    alert.read_at = new Date();
                }
            } catch (error) {
                console.error("Error marking alert as read:", error);
                // Fallback: mark as read locally
                alert.is_read = true;
                alert.read_at = new Date();
            }
        },

        async markAllAsRead() {
            try {
                const unreadAlerts = this.alerts.filter(
                    (alert) => !alert.is_read
                );
                await Promise.all(
                    unreadAlerts.map((alert) => this.markAsRead(alert))
                );
                this.showMessage("All alerts marked as read!", "success");
            } catch (error) {
                console.error("Error marking all alerts as read:", error);
                this.showMessage("Error marking alerts as read", "danger");
            }
        },

        async deleteAlert(alert) {
            if (!confirm("Are you sure you want to delete this alert?")) {
                return;
            }

            try {
                const response = await fetch(`/api/alerts/${alert.id}`, {
                    method: "DELETE",
                    headers: {
                        "X-CSRF-TOKEN": document
                            .querySelector('meta[name="csrf-token"]')
                            ?.getAttribute("content"),
                    },
                });

                if (response.ok) {
                    const index = this.alerts.findIndex(
                        (a) => a.id === alert.id
                    );
                    if (index > -1) {
                        this.alerts.splice(index, 1);
                    }
                    this.showMessage("Alert deleted successfully!", "success");
                }
            } catch (error) {
                console.error("Error deleting alert:", error);
                this.showMessage("Error deleting alert", "danger");
            }
        },

        viewAlertDetails(alert) {
            this.selectedAlert = alert;
            this.showDetailModal = true;
        },

        closeDetailModal() {
            this.showDetailModal = false;
            this.selectedAlert = null;
        },

        applyFilters() {
            this.currentPage = 1;
        },

        clearFilters() {
            this.filters = {
                priority: "",
                type: "",
                status: "",
            };
            this.currentPage = 1;
        },

        changePage(page) {
            if (page >= 1 && page <= this.totalPages) {
                this.currentPage = page;
            }
        },

        getAlertClass(priority) {
            const classes = {
                critical: "alert-danger",
                high: "alert-warning",
                medium: "alert-info",
                low: "alert-info",
            };
            return classes[priority] || "alert-info";
        },

        getAlertIcon(type) {
            const icons = {
                low_stock: "fas fa-exclamation-triangle",
                out_of_stock: "fas fa-times-circle",
                expiry: "fas fa-clock",
                system: "fas fa-info-circle",
            };
            return icons[type] || "fas fa-bell";
        },

        getPriorityBadgeClass(priority) {
            const classes = {
                critical: "bg-danger",
                high: "bg-warning",
                medium: "bg-info",
                low: "bg-secondary",
            };
            return classes[priority] || "bg-secondary";
        },

        formatDate(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diffTime = Math.abs(now - date);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

            if (diffDays === 1) {
                return "Today";
            } else if (diffDays === 2) {
                return "Yesterday";
            } else if (diffDays <= 7) {
                return `${diffDays - 1} days ago`;
            } else {
                return date.toLocaleDateString("en-US", {
                    month: "short",
                    day: "numeric",
                    year: "numeric",
                });
            }
        },

        showMessage(message, type) {
            // Create and show a Bootstrap alert
            const alertDiv = document.createElement("div");
            alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
            alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      `;

            const container = document.querySelector(".alert-manager-vue");
            container.insertBefore(alertDiv, container.firstChild);

            // Auto-remove after 5 seconds
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
        },

        startAutoRefresh() {
            if (this.autoRefresh) {
                setInterval(() => {
                    if (this.autoRefresh) {
                        this.loadAlerts();
                    }
                }, 30000); // Refresh every 30 seconds
            }
        },
    },
    watch: {
        autoRefresh(newVal) {
            if (newVal) {
                this.startAutoRefresh();
            }
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
.alert {
    margin-bottom: 1rem;
}
</style>
