<template>
    <div class="report-generator-vue">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="h3 mb-0">Report Generator</h1>
                <p class="text-muted">Generate and manage inventory reports</p>
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

        <!-- Quick Reports -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header">
                        <h5 class="mb-0">Quick Reports</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <div class="card border-primary h-100">
                                    <div class="card-body text-center">
                                        <i
                                            class="fas fa-chart-pie fa-2x text-primary mb-2"
                                        ></i>
                                        <h6>Inventory Summary</h6>
                                        <p class="text-muted small">
                                            Overview of all inventory items
                                        </p>
                                        <button
                                            @click="
                                                generateQuickReport(
                                                    'inventory_summary'
                                                )
                                            "
                                            class="btn btn-outline-primary btn-sm"
                                            :disabled="generating"
                                        >
                                            <span
                                                v-if="
                                                    generating &&
                                                    currentReportType ===
                                                        'inventory_summary'
                                                "
                                                class="spinner-border spinner-border-sm me-2"
                                            ></span>
                                            Generate Report
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 mb-3">
                                <div class="card border-warning h-100">
                                    <div class="card-body text-center">
                                        <i
                                            class="fas fa-exclamation-triangle fa-2x text-warning mb-2"
                                        ></i>
                                        <h6>Low Stock Report</h6>
                                        <p class="text-muted small">
                                            Products below minimum levels
                                        </p>
                                        <button
                                            @click="
                                                generateQuickReport('low_stock')
                                            "
                                            class="btn btn-outline-warning btn-sm"
                                            :disabled="generating"
                                        >
                                            <span
                                                v-if="
                                                    generating &&
                                                    currentReportType ===
                                                        'low_stock'
                                                "
                                                class="spinner-border spinner-border-sm me-2"
                                            ></span>
                                            Generate Report
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 mb-3">
                                <div class="card border-info h-100">
                                    <div class="card-body text-center">
                                        <i
                                            class="fas fa-history fa-2x text-info mb-2"
                                        ></i>
                                        <h6>Transaction History</h6>
                                        <p class="text-muted small">
                                            Recent inventory movements
                                        </p>
                                        <button
                                            @click="
                                                generateQuickReport(
                                                    'transaction_history'
                                                )
                                            "
                                            class="btn btn-outline-info btn-sm"
                                            :disabled="generating"
                                        >
                                            <span
                                                v-if="
                                                    generating &&
                                                    currentReportType ===
                                                        'transaction_history'
                                                "
                                                class="spinner-border spinner-border-sm me-2"
                                            ></span>
                                            Generate Report
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3 mb-3">
                                <div class="card border-success h-100">
                                    <div class="card-body text-center">
                                        <i
                                            class="fas fa-warehouse fa-2x text-success mb-2"
                                        ></i>
                                        <h6>Warehouse Summary</h6>
                                        <p class="text-muted small">
                                            Inventory by warehouse
                                        </p>
                                        <button
                                            @click="
                                                generateQuickReport(
                                                    'warehouse_summary'
                                                )
                                            "
                                            class="btn btn-outline-success btn-sm"
                                            :disabled="generating"
                                        >
                                            <span
                                                v-if="
                                                    generating &&
                                                    currentReportType ===
                                                        'warehouse_summary'
                                                "
                                                class="spinner-border spinner-border-sm me-2"
                                            ></span>
                                            Generate Report
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Custom Report Builder -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header">
                        <h5 class="mb-0">Custom Report Builder</h5>
                    </div>
                    <div class="card-body">
                        <form @submit.prevent="generateCustomReport">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label"
                                        >Report Name *</label
                                    >
                                    <input
                                        v-model="customReport.name"
                                        type="text"
                                        class="form-control"
                                        required
                                    />
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label"
                                        >Report Type *</label
                                    >
                                    <select
                                        v-model="customReport.type"
                                        class="form-select"
                                        required
                                    >
                                        <option value="">
                                            Select Report Type
                                        </option>
                                        <option value="inventory_summary">
                                            Inventory Summary
                                        </option>
                                        <option value="low_stock">
                                            Low Stock
                                        </option>
                                        <option value="transaction_history">
                                            Transaction History
                                        </option>
                                        <option value="warehouse_summary">
                                            Warehouse Summary
                                        </option>
                                        <option value="category_analysis">
                                            Category Analysis
                                        </option>
                                        <option value="value_analysis">
                                            Value Analysis
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Date Range</label>
                                    <select
                                        v-model="customReport.date_range"
                                        class="form-select"
                                    >
                                        <option value="today">Today</option>
                                        <option value="week">This Week</option>
                                        <option value="month">
                                            This Month
                                        </option>
                                        <option value="quarter">
                                            This Quarter
                                        </option>
                                        <option value="year">This Year</option>
                                        <option value="custom">
                                            Custom Range
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label"
                                        >Category Filter</label
                                    >
                                    <select
                                        v-model="customReport.category_id"
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
                                <div class="col-md-4 mb-3">
                                    <label class="form-label"
                                        >Warehouse Filter</label
                                    >
                                    <select
                                        v-model="customReport.warehouse_id"
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
                            </div>

                            <div
                                v-if="customReport.date_range === 'custom'"
                                class="row"
                            >
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Start Date</label>
                                    <input
                                        v-model="customReport.start_date"
                                        type="date"
                                        class="form-control"
                                    />
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">End Date</label>
                                    <input
                                        v-model="customReport.end_date"
                                        type="date"
                                        class="form-control"
                                    />
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label"
                                    >Additional Parameters</label
                                >
                                <textarea
                                    v-model="customReport.parameters"
                                    class="form-control"
                                    rows="3"
                                    placeholder="Any additional parameters or notes for the report"
                                ></textarea>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button
                                    type="submit"
                                    class="btn btn-primary"
                                    :disabled="generating"
                                >
                                    <span
                                        v-if="generating"
                                        class="spinner-border spinner-border-sm me-2"
                                    ></span>
                                    <i class="fas fa-chart-bar"></i> Generate
                                    Custom Report
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Generated Reports -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow">
                    <div
                        class="card-header d-flex justify-content-between align-items-center"
                    >
                        <h5 class="mb-0">Generated Reports</h5>
                        <button
                            @click="loadReports"
                            class="btn btn-sm btn-outline-primary"
                            :disabled="loadingReports"
                        >
                            <i
                                class="fas fa-sync-alt"
                                :class="{ 'fa-spin': loadingReports }"
                            ></i>
                            Refresh
                        </button>
                    </div>
                    <div class="card-body">
                        <div v-if="loadingReports" class="text-center py-4">
                            <div
                                class="spinner-border text-primary"
                                role="status"
                            >
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                        <div
                            v-else-if="reports.length > 0"
                            class="table-responsive"
                        >
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Report Name</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Generated</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="report in reports"
                                        :key="report.id"
                                    >
                                        <td>{{ report.name }}</td>
                                        <td>
                                            <span class="badge bg-secondary">{{
                                                report.type
                                            }}</span>
                                        </td>
                                        <td>
                                            <span
                                                class="badge"
                                                :class="
                                                    getStatusBadgeClass(
                                                        report.status
                                                    )
                                                "
                                            >
                                                {{ report.status }}
                                            </span>
                                        </td>
                                        <td>
                                            {{
                                                formatDate(report.generated_at)
                                            }}
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button
                                                    @click="viewReport(report)"
                                                    class="btn btn-sm btn-info"
                                                >
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button
                                                    v-if="
                                                        report.status ===
                                                            'completed' &&
                                                        report.file_path
                                                    "
                                                    @click="
                                                        downloadReport(report)
                                                    "
                                                    class="btn btn-sm btn-success"
                                                >
                                                    <i
                                                        class="fas fa-download"
                                                    ></i>
                                                </button>
                                                <button
                                                    @click="
                                                        deleteReport(report)
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
                            <i
                                class="fas fa-chart-bar fa-3x text-muted mb-3"
                            ></i>
                            <h5 class="text-muted">No Reports Generated</h5>
                            <p class="text-muted">
                                Generate your first report using the options
                                above.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Report Preview Modal -->
        <div
            class="modal fade"
            :class="{ show: showPreviewModal }"
            :style="{ display: showPreviewModal ? 'block' : 'none' }"
            tabindex="-1"
        >
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            {{ selectedReport?.name }} - Preview
                        </h5>
                        <button
                            @click="closePreviewModal"
                            type="button"
                            class="btn-close"
                        ></button>
                    </div>
                    <div class="modal-body">
                        <div v-if="selectedReport && reportData">
                            <!-- Report Content -->
                            <div class="report-content">
                                <div
                                    v-if="
                                        selectedReport.type ===
                                        'inventory_summary'
                                    "
                                    class="inventory-summary"
                                >
                                    <h6>Inventory Summary Report</h6>
                                    <div class="row">
                                        <div class="col-md-3 mb-3">
                                            <div class="card text-center">
                                                <div class="card-body">
                                                    <h4 class="text-primary">
                                                        {{
                                                            reportData.total_products
                                                        }}
                                                    </h4>
                                                    <p class="mb-0">
                                                        Total Products
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <div class="card text-center">
                                                <div class="card-body">
                                                    <h4 class="text-success">
                                                        ${{
                                                            formatPrice(
                                                                reportData.total_value
                                                            )
                                                        }}
                                                    </h4>
                                                    <p class="mb-0">
                                                        Total Value
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <div class="card text-center">
                                                <div class="card-body">
                                                    <h4 class="text-warning">
                                                        {{
                                                            reportData.low_stock_count
                                                        }}
                                                    </h4>
                                                    <p class="mb-0">
                                                        Low Stock Items
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <div class="card text-center">
                                                <div class="card-body">
                                                    <h4 class="text-danger">
                                                        {{
                                                            reportData.out_of_stock_count
                                                        }}
                                                    </h4>
                                                    <p class="mb-0">
                                                        Out of Stock
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div
                                    v-else-if="
                                        selectedReport.type === 'low_stock'
                                    "
                                    class="low-stock-report"
                                >
                                    <h6>Low Stock Report</h6>
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Product</th>
                                                    <th>Category</th>
                                                    <th>Warehouse</th>
                                                    <th>Current Stock</th>
                                                    <th>Min Required</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr
                                                    v-for="product in reportData"
                                                    :key="product.id"
                                                >
                                                    <td>{{ product.name }}</td>
                                                    <td>
                                                        {{
                                                            product.category
                                                                ?.name
                                                        }}
                                                    </td>
                                                    <td>
                                                        {{
                                                            product.warehouse
                                                                ?.name
                                                        }}
                                                    </td>
                                                    <td>
                                                        {{ product.quantity }}
                                                        {{ product.unit }}
                                                    </td>
                                                    <td>
                                                        {{
                                                            product.min_quantity
                                                        }}
                                                        {{ product.unit }}
                                                    </td>
                                                    <td>
                                                        <span
                                                            class="badge bg-warning"
                                                            >Low Stock</span
                                                        >
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div
                                    v-else-if="
                                        selectedReport.type ===
                                        'transaction_history'
                                    "
                                    class="transaction-report"
                                >
                                    <h6>Transaction History Report</h6>
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Product</th>
                                                    <th>Type</th>
                                                    <th>Quantity</th>
                                                    <th>Warehouse</th>
                                                    <th>User</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr
                                                    v-for="transaction in reportData"
                                                    :key="transaction.id"
                                                >
                                                    <td>
                                                        {{
                                                            formatDate(
                                                                transaction.created_at
                                                            )
                                                        }}
                                                    </td>
                                                    <td>
                                                        {{
                                                            transaction.product
                                                                ?.name
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
                                                    <td>
                                                        {{
                                                            transaction.quantity
                                                        }}
                                                    </td>
                                                    <td>
                                                        {{
                                                            transaction
                                                                .warehouse?.name
                                                        }}
                                                    </td>
                                                    <td>
                                                        {{
                                                            transaction.user
                                                                ?.name
                                                        }}
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div
                                    v-else-if="
                                        selectedReport.type ===
                                        'warehouse_summary'
                                    "
                                    class="warehouse-report"
                                >
                                    <h6>Warehouse Summary Report</h6>
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Warehouse</th>
                                                    <th>Total Products</th>
                                                    <th>Total Value</th>
                                                    <th>Low Stock Items</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr
                                                    v-for="warehouse in reportData"
                                                    :key="warehouse.id"
                                                >
                                                    <td>
                                                        {{ warehouse.name }}
                                                    </td>
                                                    <td>
                                                        {{
                                                            warehouse.products_count
                                                        }}
                                                    </td>
                                                    <td>
                                                        ${{
                                                            formatPrice(
                                                                warehouse.total_value ||
                                                                    0
                                                            )
                                                        }}
                                                    </td>
                                                    <td>
                                                        {{
                                                            warehouse.low_stock_count ||
                                                            0
                                                        }}
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div v-else class="generic-report">
                                    <h6>{{ selectedReport.name }}</h6>
                                    <p class="text-muted">
                                        Report data preview not available for
                                        this report type.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button
                            @click="closePreviewModal"
                            type="button"
                            class="btn btn-secondary"
                        >
                            Close
                        </button>
                        <button
                            v-if="
                                selectedReport?.status === 'completed' &&
                                selectedReport?.file_path
                            "
                            @click="downloadReport(selectedReport)"
                            class="btn btn-success"
                        >
                            <i class="fas fa-download"></i> Download Report
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Backdrop -->
        <div
            v-if="showPreviewModal"
            class="modal-backdrop fade show"
            @click="closePreviewModal"
        ></div>
    </div>
</template>

<script>
export default {
    name: "ReportGenerator",
    data() {
        return {
            loadingReports: false,
            generating: false,
            currentReportType: "",
            reports: [],
            categories: [],
            warehouses: [],
            message: "",
            messageClass: "",
            showPreviewModal: false,
            selectedReport: null,
            reportData: null,
            customReport: {
                name: "",
                type: "",
                date_range: "month",
                category_id: "",
                warehouse_id: "",
                start_date: "",
                end_date: "",
                parameters: "",
            },
        };
    },
    mounted() {
        this.loadData();
    },
    methods: {
        async loadData() {
            try {
                await Promise.all([
                    this.loadReports(),
                    this.loadCategories(),
                    this.loadWarehouses(),
                ]);
            } catch (error) {
                console.error("Error loading data:", error);
            }
        },

        async loadReports() {
            try {
                this.loadingReports = true;
                const response = await fetch("/api/reports");
                if (response.ok) {
                    this.reports = await response.json();
                }
            } catch (error) {
                console.error("Error loading reports:", error);
                // Fallback to sample data
                this.reports = [
                    {
                        id: 1,
                        name: "Monthly Inventory Summary",
                        type: "inventory_summary",
                        status: "completed",
                        generated_at: new Date(),
                        file_path: "/reports/inventory_summary_2024_01.pdf",
                    },
                    {
                        id: 2,
                        name: "Low Stock Alert Report",
                        type: "low_stock",
                        status: "completed",
                        generated_at: new Date(Date.now() - 86400000),
                        file_path: "/reports/low_stock_2024_01.pdf",
                    },
                ];
            } finally {
                this.loadingReports = false;
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
                    { id: 1, name: "Electronics" },
                    { id: 2, name: "Clothing" },
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

        async generateQuickReport(type) {
            try {
                this.generating = true;
                this.currentReportType = type;

                const response = await fetch(`/api/reports/generate/${type}`, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document
                            .querySelector('meta[name="csrf-token"]')
                            ?.getAttribute("content"),
                    },
                });

                if (response.ok) {
                    const report = await response.json();
                    this.showMessage(
                        `Report "${report.name}" generated successfully!`,
                        "success"
                    );
                    this.loadReports();
                } else {
                    throw new Error("Failed to generate report");
                }
            } catch (error) {
                console.error("Error generating report:", error);
                this.showMessage(
                    "Error generating report. Please try again.",
                    "danger"
                );
            } finally {
                this.generating = false;
                this.currentReportType = "";
            }
        },

        async generateCustomReport() {
            try {
                this.generating = true;

                const response = await fetch("/api/reports", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document
                            .querySelector('meta[name="csrf-token"]')
                            ?.getAttribute("content"),
                    },
                    body: JSON.stringify(this.customReport),
                });

                if (response.ok) {
                    const report = await response.json();
                    this.showMessage(
                        `Custom report "${report.name}" created successfully!`,
                        "success"
                    );
                    this.resetCustomReportForm();
                    this.loadReports();
                } else {
                    throw new Error("Failed to create custom report");
                }
            } catch (error) {
                console.error("Error creating custom report:", error);
                this.showMessage(
                    "Error creating custom report. Please try again.",
                    "danger"
                );
            } finally {
                this.generating = false;
            }
        },

        async viewReport(report) {
            try {
                const response = await fetch(`/api/reports/${report.id}`);
                if (response.ok) {
                    const reportData = await response.json();
                    this.selectedReport = report;
                    this.reportData = reportData.data || [];
                    this.showPreviewModal = true;
                }
            } catch (error) {
                console.error("Error loading report data:", error);
                this.showMessage("Error loading report data", "danger");
            }
        },

        async downloadReport(report) {
            try {
                window.open(report.file_path, "_blank");
            } catch (error) {
                console.error("Error downloading report:", error);
                this.showMessage("Error downloading report", "danger");
            }
        },

        async deleteReport(report) {
            if (!confirm(`Are you sure you want to delete "${report.name}"?`)) {
                return;
            }

            try {
                const response = await fetch(`/api/reports/${report.id}`, {
                    method: "DELETE",
                    headers: {
                        "X-CSRF-TOKEN": document
                            .querySelector('meta[name="csrf-token"]')
                            ?.getAttribute("content"),
                    },
                });

                if (response.ok) {
                    this.showMessage("Report deleted successfully!", "success");
                    this.loadReports();
                } else {
                    throw new Error("Failed to delete report");
                }
            } catch (error) {
                console.error("Error deleting report:", error);
                this.showMessage("Error deleting report", "danger");
            }
        },

        closePreviewModal() {
            this.showPreviewModal = false;
            this.selectedReport = null;
            this.reportData = null;
        },

        resetCustomReportForm() {
            this.customReport = {
                name: "",
                type: "",
                date_range: "month",
                category_id: "",
                warehouse_id: "",
                start_date: "",
                end_date: "",
                parameters: "",
            };
        },

        getStatusBadgeClass(status) {
            const classes = {
                pending: "bg-warning",
                processing: "bg-info",
                completed: "bg-success",
                failed: "bg-danger",
            };
            return classes[status] || "bg-secondary";
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
            if (!dateString) return "N/A";
            const date = new Date(dateString);
            return date.toLocaleDateString("en-US", {
                month: "short",
                day: "numeric",
                year: "numeric",
                hour: "2-digit",
                minute: "2-digit",
            });
        },

        formatPrice(price) {
            return parseFloat(price).toFixed(2);
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
.report-content {
    max-height: 70vh;
    overflow-y: auto;
}
</style>
