import "./bootstrap";
import { createApp } from "vue";

// Import Vue components
import Dashboard from "./components/Dashboard.vue";
import ProductManager from "./components/ProductManager.vue";
import InventoryManager from "./components/InventoryManager.vue";
import AlertManager from "./components/AlertManager.vue";
import ReportGenerator from "./components/ReportGenerator.vue";
import CategoryManager from "./components/CategoryManager.vue";
import WarehouseManager from "./components/WarehouseManager.vue";

// Initialize Vue app
document.addEventListener("DOMContentLoaded", () => {
    // Dashboard component
    const dashboardApp = document.getElementById("dashboard-app");
    if (dashboardApp) {
        createApp(Dashboard).mount("#dashboard-app");
    }

    // Product manager component
    const productApp = document.getElementById("product-manager");
    if (productApp) {
        createApp(ProductManager).mount("#product-manager");
    }

    // Inventory manager component
    const inventoryApp = document.getElementById("inventory-manager");
    if (inventoryApp) {
        createApp(InventoryManager).mount("#inventory-manager");
    }

    // Alert manager component
    const alertApp = document.getElementById("alert-manager");
    if (alertApp) {
        createApp(AlertManager).mount("#alert-manager");
    }

    // Report generator component
    const reportApp = document.getElementById("report-generator");
    if (reportApp) {
        createApp(ReportGenerator).mount("#report-generator");
    }

    // Category manager component
    const categoryApp = document.getElementById("category-manager");
    if (categoryApp) {
        createApp(CategoryManager).mount("#category-manager");
    }

    // Warehouse manager component
    const warehouseApp = document.getElementById("warehouse-manager");
    if (warehouseApp) {
        createApp(WarehouseManager).mount("#warehouse-manager");
    }
});
