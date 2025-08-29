-- Create inventory_receipts table
CREATE TABLE IF NOT EXISTS `inventory_receipts` (
    `id` bigint unsigned NOT NULL AUTO_INCREMENT,
    `receipt_number` varchar(255) NOT NULL,
    `receipt_date` date NOT NULL,
    `supplier_name` varchar(255) NOT NULL,
    `purchase_order_number` varchar(255) DEFAULT NULL,
    `delivery_date` date NOT NULL,
    `invoice_number` varchar(255) DEFAULT NULL,
    `warehouse_location` varchar(255) NOT NULL,
    `received_by` varchar(255) NOT NULL,
    `notes` text,
    `status` varchar(255) NOT NULL DEFAULT 'Pending',
    `total_value` decimal(15,2) NOT NULL DEFAULT '0.00',
    `total_items` int NOT NULL DEFAULT '0',
    `total_quantity` int NOT NULL DEFAULT '0',
    `damaged_items` int NOT NULL DEFAULT '0',
    `created_by` bigint unsigned NOT NULL,
    `purchase_order_id` bigint unsigned DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `receipt_number` (`receipt_number`),
    KEY `receipt_date_status` (`receipt_date`,`status`),
    KEY `supplier_name` (`supplier_name`),
    KEY `purchase_order_number` (`purchase_order_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create inventory_items table
CREATE TABLE IF NOT EXISTS `inventory_items` (
    `id` bigint unsigned NOT NULL AUTO_INCREMENT,
    `item_code` varchar(255) NOT NULL,
    `name` varchar(255) NOT NULL,
    `description` text,
    `category` varchar(255) NOT NULL,
    `supplier` varchar(255) DEFAULT NULL,
    `current_stock` int NOT NULL DEFAULT '0',
    `minimum_stock` int NOT NULL DEFAULT '0',
    `reorder_quantity` int NOT NULL DEFAULT '0',
    `unit_of_measure` varchar(255) NOT NULL,
    `unit_price` decimal(10,2) NOT NULL DEFAULT '0.00',
    `storage_location` varchar(255) DEFAULT NULL,
    `status` varchar(255) NOT NULL DEFAULT 'Active',
    `notes` text,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `item_code` (`item_code`),
    KEY `category` (`category`),
    KEY `supplier` (`supplier`),
    KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create inventory_receipt_items table
CREATE TABLE IF NOT EXISTS `inventory_receipt_items` (
    `id` bigint unsigned NOT NULL AUTO_INCREMENT,
    `inventory_receipt_id` bigint unsigned NOT NULL,
    `item_name` varchar(255) NOT NULL,
    `description` text,
    `quantity` int NOT NULL,
    `unit` varchar(255) NOT NULL,
    `unit_price` decimal(10,2) NOT NULL DEFAULT '0.00',
    `total_price` decimal(10,2) NOT NULL DEFAULT '0.00',
    `condition` varchar(255) NOT NULL,
    `storage_location` varchar(255) NOT NULL,
    `batch_number` varchar(255) DEFAULT NULL,
    `expiry_date` date DEFAULT NULL,
    `item_notes` text,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `inventory_receipt_id` (`inventory_receipt_id`),
    KEY `item_name` (`item_name`),
    KEY `condition` (`condition`),
    KEY `storage_location` (`storage_location`),
    CONSTRAINT `inventory_receipt_items_inventory_receipt_id_foreign` 
        FOREIGN KEY (`inventory_receipt_id`) REFERENCES `inventory_receipts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
