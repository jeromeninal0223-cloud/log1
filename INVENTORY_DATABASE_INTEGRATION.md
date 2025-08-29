# Inventory Receipt Database Integration with PSM

## Overview

This document describes the database integration between the Inventory Receipt system and the Procurement & Sourcing Management (PSM) module. The integration allows seamless tracking of inventory receipts linked to purchase orders and provides real-time data synchronization.

## Database Schema

### Core Tables

#### 1. `inventory_receipts`
Stores the main receipt information and links to PSM purchase orders.

**Key Fields:**
- `receipt_number` - Auto-generated unique receipt number
- `supplier_name` - Name of the supplier
- `purchase_order_number` - Optional link to PSM purchase order
- `purchase_order_id` - Foreign key to purchase_orders table
- `status` - Receipt status (Pending, Completed, Quality Issue, Cancelled)
- `total_value`, `total_items`, `total_quantity`, `damaged_items` - Calculated totals

#### 2. `inventory_receipt_items`
Stores individual items received in each receipt.

**Key Fields:**
- `inventory_receipt_id` - Foreign key to inventory_receipts
- `item_name`, `description` - Item details
- `quantity`, `unit` - Quantity and unit of measure
- `unit_price`, `total_price` - Pricing information
- `condition` - Item condition (New, Good, Fair, Damaged, Expired)
- `storage_location` - Assigned storage location

#### 3. `inventory_items`
Master inventory catalog that tracks current stock levels.

**Key Fields:**
- `item_code` - Unique item identifier
- `name`, `description`, `category` - Item details
- `current_stock`, `minimum_stock`, `reorder_quantity` - Stock management
- `unit_of_measure`, `unit_price` - Pricing and units
- `storage_location` - Current storage location
- `status` - Item status (Active, Inactive, Discontinued)

## PSM Integration Points

### 1. Purchase Order Linking
- Receipts can be linked to PSM purchase orders via `purchase_order_id`
- When a receipt is completed, the linked purchase order status is updated to "In Progress"
- Purchase order information is displayed in the receipt form dropdown

### 2. Vendor Integration
- Suppliers in receipts are linked to PSM vendor records
- Vendor information is pulled from the `vendors` table for dropdowns
- Ensures consistency between PSM and inventory systems

### 3. Status Synchronization
- Receipt completion triggers purchase order status updates
- Contract completion is checked when all related purchase orders are completed
- Invoice payment status affects contract completion

## API Endpoints

### Inventory Receipt Management
- `POST /api/inventory/store-receipt` - Create new receipt
- `GET /api/inventory/recent-receipts` - Get recent receipts
- `GET /api/inventory/dashboard-stats` - Get dashboard statistics
- `POST /api/inventory/complete-receipt/{id}` - Complete a receipt

### Inventory Item Management
- `POST /api/inventory/add-item` - Add new item to inventory
- `POST /api/inventory/update-stock` - Update stock levels

## Data Flow

### Receipt Creation Process
1. User fills out receipt form with supplier and purchase order information
2. Items are added to the receipt with quantities and conditions
3. Receipt is saved to `inventory_receipts` table
4. Receipt items are saved to `inventory_receipt_items` table
5. Inventory items are updated or created in `inventory_items` table
6. If linked to a purchase order, PO status is updated

### Stock Management
1. When items are received, current stock is automatically updated
2. Low stock alerts are triggered when stock falls below minimum levels
3. Reorder quantities are suggested based on configured values
4. Stock value calculations are updated in real-time

## Features

### Real-time Dashboard
- Today's receipts count
- Weekly receipts summary
- Items awaiting storage
- Total inventory value
- Recent receipts with status indicators

### Purchase Order Integration
- Dropdown selection of available purchase orders
- Automatic status updates when receipts are completed
- Purchase order details displayed in receipt form

### Quality Control
- Item condition tracking (New, Good, Fair, Damaged, Expired)
- Damaged item counting and reporting
- Quality issue status for receipts

### Storage Management
- Storage location assignment for received items
- Warehouse zone organization
- Location-based item tracking

## Setup Instructions

### 1. Run Migrations
```bash
php artisan migrate
```

### 2. Seed Sample Data
```bash
php artisan db:seed --class=InventorySeeder
```

### 3. Verify Integration
- Check that vendors are created in the system
- Verify purchase orders are available in receipt form
- Test receipt creation and linking to purchase orders

## Sample Data

The seeder creates:
- 5 sample vendors (TechCorp Inc., Global Electronics, etc.)
- 5 sample inventory items (Laptops, Mice, USB hubs, etc.)
- 2 sample purchase orders with different statuses
- 3 sample receipts with items and different statuses

## Benefits

1. **End-to-End Tracking**: Complete visibility from purchase order to inventory receipt
2. **Data Consistency**: Single source of truth for vendor and item information
3. **Automated Workflows**: Status updates and notifications based on receipt completion
4. **Real-time Reporting**: Live dashboard with current inventory and receipt statistics
5. **Quality Control**: Comprehensive tracking of item conditions and quality issues

## Future Enhancements

1. **Barcode Integration**: QR code generation for receipts and items
2. **Mobile App**: Receipt scanning and processing on mobile devices
3. **Advanced Analytics**: Predictive analytics for stock levels and supplier performance
4. **Integration APIs**: REST APIs for external system integration
5. **Document Management**: PDF generation and storage for receipts
