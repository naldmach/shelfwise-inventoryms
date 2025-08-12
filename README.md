# ShelfWise Inventory Management System

A comprehensive PHP Laravel application for managing warehouse inventory with real-time stock tracking and automated alerts.

## Features

-   **Stock Management**: Complete inventory tracking with stock in/out operations
-   **Automated Alerts**: Low stock and out-of-stock notifications
-   **Reports**: Comprehensive reporting system for inventory analysis
-   **Multi-user Support**: Role-based access control with user management
-   **Real-time Tracking**: Live inventory updates and transaction history
-   **Category Management**: Organized product categorization
-   **Warehouse Management**: Multiple warehouse support with location tracking

## Technologies Used

-   **Backend**: PHP 8.1+, Laravel 12.x
-   **Database**: MySQL/SQLite
-   **Frontend**: Vue.js 3, Bootstrap 5, Blade Templates
-   **Build Tool**: Vite
-   **Authentication**: Laravel UI with Bootstrap
-   **Permissions**: Spatie Laravel Permission
-   **Excel Export**: Maatwebsite Excel

## Installation

1. **Clone the repository**

    ```bash
    git clone <repository-url>
    cd shelfwise-inventoryms
    ```

2. **Install PHP dependencies**

    ```bash
    composer install
    ```

3. **Install Node.js dependencies**

    ```bash
    npm install
    ```

4. **Environment setup**

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

5. **Database configuration**

    - Update `.env` file with your database credentials
    - For SQLite: `DB_CONNECTION=sqlite` and `DB_DATABASE=database/database.sqlite`

6. **Run migrations and seeders**

    ```bash
    php artisan migrate
    php artisan db:seed
    ```

7. **Compile assets**

    ```bash
    npm run build
    # For development with hot reload:
    npm run dev
    ```

8. **Start the application**
    ```bash
    php artisan serve
    ```

## Development

### Frontend Development

```bash
# Start Vite dev server with hot reload
npm run dev

# Build for production
npm run build

# Watch for changes
npm run watch
```

### Vue.js Component Structure

```
resources/js/components/
├── Dashboard.vue          # Dashboard overview
├── ProductManager.vue     # Product CRUD operations
├── InventoryManager.vue   # Stock management
├── AlertManager.vue       # Alert management
├── ReportGenerator.vue    # Report generation
├── CategoryManager.vue    # Category management
└── WarehouseManager.vue   # Warehouse management
```

## Default Login

-   **Email**: test@example.com
-   **Password**: password

## Database Structure

### Core Tables

-   **users**: User accounts and authentication
-   **categories**: Product categories with color coding
-   **warehouses**: Warehouse locations and details
-   **products**: Product information and stock levels
-   **inventory_transactions**: Stock movement history
-   **alerts**: System notifications and warnings
-   **reports**: Generated reports and exports

### Permission Tables

-   **permissions**: System permissions
-   **roles**: User roles
-   **model_has_permissions**: Permission assignments
-   **model_has_roles**: Role assignments
-   **role_has_permissions**: Role-permission relationships

## Key Features

### Dashboard

-   Real-time statistics and metrics
-   Recent transactions overview
-   Low stock alerts
-   Quick action buttons
-   Interactive Vue.js components with real-time data updates

### Vue.js Components

-   **Dashboard**: Real-time statistics and activity feeds
-   **ProductManager**: Complete CRUD operations with search, filter, and pagination
-   **InventoryManager**: Stock operations with transaction history
-   **AlertManager**: Alert management with priority filtering
-   **ReportGenerator**: Report generation and management
-   **CategoryManager**: Category management with color coding
-   **WarehouseManager**: Warehouse management with location details

### Inventory Management

-   Stock in/out operations
-   Quantity adjustments
-   Transfer between warehouses
-   Transaction history

### Product Management

-   SKU-based product identification
-   Category organization
-   Warehouse assignment
-   Stock level monitoring

### Alert System

-   Low stock notifications
-   Out-of-stock warnings
-   Priority-based alerting
-   Read/unread status tracking

### Reporting

-   Inventory summaries
-   Low stock reports
-   Transaction history
-   Export functionality

## API Endpoints

The system provides RESTful API endpoints for all major operations:

### Web Routes

-   `GET /dashboard` - Dashboard overview
-   `GET /products` - Product listing
-   `POST /products` - Create product
-   `GET /categories` - Category listing
-   `GET /warehouses` - Warehouse listing
-   `GET /inventory` - Inventory overview
-   `POST /inventory/stock-in` - Stock in operation
-   `POST /inventory/stock-out` - Stock out operation
-   `GET /alerts` - Alert listing
-   `GET /reports` - Report generation

### API Routes (for Vue.js components)

-   `GET /api/dashboard/stats` - Dashboard statistics
-   `GET /api/dashboard/transactions` - Recent transactions
-   `GET /api/dashboard/alerts` - Recent alerts
-   `GET /api/dashboard/stock-alerts` - Stock alerts
-   `GET /api/products` - Products with relationships
-   `GET /api/products/low-stock` - Low stock products
-   `GET /api/categories` - Categories with product counts
-   `GET /api/warehouses` - Warehouses with product counts
-   `GET /api/inventory/transactions` - Inventory transactions
-   `GET /api/alerts` - All alerts
-   `GET /api/reports` - Generated reports

## Architecture

### Frontend Architecture

-   **Vue.js 3 Composition API**: Modern reactive components
-   **Component-Based Design**: Modular, reusable components
-   **Real-time Updates**: Live data synchronization with backend
-   **Responsive Design**: Mobile-first approach with Bootstrap 5
-   **State Management**: Local component state with reactive data

### Responsive Features

-   **Mobile-First Design**: Optimized for all screen sizes
-   **Flexible Grid System**: Bootstrap 5 responsive grid
-   **Touch-Friendly Interface**: Mobile-optimized interactions
-   **Adaptive Layouts**: Components that adapt to screen size
-   **Cross-Device Compatibility**: Works on desktop, tablet, and mobile

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests if applicable
5. Submit a pull request

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Support

For support and questions, please open an issue in the repository or contact the development team.

---

**ShelfWise Inventory Management System** - Streamlining warehouse operations with intelligent inventory tracking.
