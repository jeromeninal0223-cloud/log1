# Picking & Dispatch Database Structure

This document outlines the comprehensive database structure for the Picking and Dispatch management system.

## 📋 Overview

The system consists of 6 main tables that handle the complete workflow from picking items to dispatching them with route planning and tracking.

## 🗄️ Database Tables

### 1. `picking_sessions`
**Purpose**: Manages picking sessions for warehouse workers.

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key |
| `session_id` | string | Unique session identifier (PS-YYYYMMDD-XXXXXX) |
| `user_id` | bigint | Foreign key to users table |
| `status` | string | Session status (active, completed, cancelled) |
| `started_at` | timestamp | When session started |
| `completed_at` | timestamp | When session completed |
| `metadata` | json | Additional session data |
| `created_at` | timestamp | Record creation time |
| `updated_at` | timestamp | Record update time |

**Relationships**:
- `belongsTo` User
- `hasMany` PickingItems

---

### 2. `picking_items`
**Purpose**: Individual items within a picking session.

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key |
| `picking_session_id` | bigint | Foreign key to picking_sessions |
| `item_id` | string | External item reference |
| `item_name` | string | Item name |
| `item_code` | string | Item code/SKU |
| `description` | text | Item description |
| `requested_quantity` | integer | Quantity requested |
| `picked_quantity` | integer | Quantity actually picked |
| `unit` | string | Unit of measurement (pcs, kg, etc.) |
| `location` | string | Warehouse location |
| `status` | string | Item status (pending, picking, picked, cancelled) |
| `priority` | string | Priority level (low, normal, high, urgent) |
| `picked_at` | timestamp | When item was picked |
| `picked_by` | bigint | User who picked the item |
| `notes` | text | Additional notes |
| `metadata` | json | Additional item data |

**Relationships**:
- `belongsTo` PickingSession
- `belongsTo` User (picker)
- `hasOne` DispatchRoute
- `hasMany` DispatchItems

---

### 3. `dispatch_routes`
**Purpose**: Route planning data for dispatch items.

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key |
| `item_id` | string | Reference to picking item |
| `destination` | string | Destination name |
| `pickup_latitude` | decimal(10,8) | Pickup location latitude |
| `pickup_longitude` | decimal(11,8) | Pickup location longitude |
| `pickup_address` | text | Human-readable pickup address |
| `destination_latitude` | decimal(10,8) | Destination latitude |
| `destination_longitude` | decimal(11,8) | Destination longitude |
| `destination_address` | text | Human-readable destination address |
| `distance_km` | decimal(8,2) | Route distance in kilometers |
| `duration_minutes` | integer | Estimated travel time in minutes |
| `is_straight_line` | boolean | Whether route is straight-line or road-based |
| `route_coordinates` | json | GeoJSON route coordinates |
| `route_type` | string | Route type (road, direct, custom) |
| `calculated_at` | timestamp | When route was calculated |
| `created_by` | bigint | User who created the route |

**Relationships**:
- `belongsTo` User (creator)
- `belongsTo` PickingItem
- `hasMany` DispatchItems

---

### 4. `dispatch_schedules`
**Purpose**: Scheduled dispatch runs with multiple items.

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key |
| `schedule_id` | string | Unique schedule identifier (DSP-YYYYMMDD-XXXXXX) |
| `title` | string | Schedule title |
| `scheduled_datetime` | timestamp | When dispatch is scheduled |
| `priority` | string | Priority level (normal, high, urgent) |
| `status` | string | Schedule status (scheduled, in_progress, completed, cancelled, delayed) |
| `driver_name` | string | Assigned driver name |
| `vehicle_info` | string | Vehicle information |
| `special_instructions` | text | Special delivery instructions |
| `total_items` | integer | Total number of items |
| `estimated_duration_minutes` | integer | Estimated total duration |
| `total_distance_km` | decimal(8,2) | Total route distance |
| `started_at` | timestamp | When dispatch started |
| `completed_at` | timestamp | When dispatch completed |
| `created_by` | bigint | User who created the schedule |
| `assigned_to` | bigint | User assigned to execute |
| `metadata` | json | Additional schedule data |

**Relationships**:
- `belongsTo` User (creator)
- `belongsTo` User (assignee)
- `hasMany` DispatchItems
- `hasMany` DispatchTracking

---

### 5. `dispatch_items`
**Purpose**: Individual items within a dispatch schedule.

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key |
| `dispatch_schedule_id` | bigint | Foreign key to dispatch_schedules |
| `picking_item_id` | bigint | Foreign key to picking_items |
| `dispatch_route_id` | bigint | Foreign key to dispatch_routes |
| `item_id` | string | Reference to original item |
| `item_name` | string | Item name |
| `quantity` | integer | Quantity to dispatch |
| `destination` | string | Destination name |
| `status` | string | Item status (ready, dispatched, delivered, failed) |
| `sequence_order` | integer | Order in delivery route |
| `dispatched_at` | timestamp | When item was dispatched |
| `delivered_at` | timestamp | When item was delivered |
| `delivery_notes` | text | Delivery notes |
| `recipient_name` | string | Name of recipient |
| `recipient_signature` | string | Path to signature file |
| `proof_of_delivery` | json | Photos, documents, etc. |

**Relationships**:
- `belongsTo` DispatchSchedule
- `belongsTo` PickingItem
- `belongsTo` DispatchRoute

---

### 6. `dispatch_tracking`
**Purpose**: Real-time tracking of dispatch progress.

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key |
| `dispatch_schedule_id` | bigint | Foreign key to dispatch_schedules |
| `status` | string | Tracking status (started, en_route, arrived, completed, delayed, failed) |
| `description` | text | Status description |
| `current_latitude` | decimal(10,8) | Current location latitude |
| `current_longitude` | decimal(11,8) | Current location longitude |
| `current_address` | text | Current human-readable address |
| `timestamp` | timestamp | When status was recorded |
| `updated_by` | bigint | User who updated status |
| `additional_data` | json | Photos, notes, vehicle data, etc. |

**Relationships**:
- `belongsTo` DispatchSchedule
- `belongsTo` User (updater)

## 🔄 Workflow

### 1. Picking Phase
1. **Create Picking Session** → `picking_sessions`
2. **Add Items to Pick** → `picking_items`
3. **Update Picked Quantities** → Update `picking_items.picked_quantity`
4. **Complete Session** → Update `picking_sessions.status`

### 2. Route Planning Phase
1. **Plan Routes for Items** → `dispatch_routes`
2. **Calculate Distances/Times** → Update route data
3. **Save Route Coordinates** → Store GeoJSON data

### 3. Dispatch Scheduling Phase
1. **Create Dispatch Schedule** → `dispatch_schedules`
2. **Add Items to Schedule** → `dispatch_items`
3. **Assign Driver/Vehicle** → Update schedule details

### 4. Execution & Tracking Phase
1. **Start Dispatch** → Update schedule status
2. **Track Progress** → `dispatch_tracking`
3. **Update Delivery Status** → Update `dispatch_items`
4. **Complete Dispatch** → Final status updates

## 📊 Key Features

### ✅ **Route Persistence**
- Routes saved to database survive page refreshes
- Both localStorage and database storage
- Route coordinates stored as GeoJSON

### ✅ **Comprehensive Tracking**
- Real-time location tracking
- Status updates with timestamps
- Photo/document attachments

### ✅ **Flexible Scheduling**
- Multiple priority levels
- Driver/vehicle assignment
- Special instructions support

### ✅ **Audit Trail**
- Complete history of all operations
- User attribution for all actions
- Timestamps for all status changes

## 🚀 Usage

### Run Migrations
```bash
php artisan migrate
```

### Seed Sample Data
```bash
php artisan db:seed --class=PickingDispatchSeeder
```

### Include Routes
Add to your `routes/web.php`:
```php
require __DIR__.'/picking_dispatch.php';
```

## 📝 API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/picking-dispatch` | Main page |
| `POST` | `/picking-dispatch/save-route` | Save item route |
| `POST` | `/picking-dispatch/create-schedule` | Create dispatch schedule |
| `POST` | `/picking-dispatch/bulk-dispatch` | Immediate bulk dispatch |
| `GET` | `/picking-dispatch/schedules` | Get dispatch schedules |
| `POST` | `/picking-dispatch/schedules/{id}/tracking` | Update tracking |

## 🔧 Models Available

- `PickingSession`
- `PickingItem`
- `DispatchRoute`
- `DispatchSchedule`
- `DispatchItem`
- `DispatchTracking`

Each model includes:
- ✅ Proper relationships
- ✅ Attribute accessors
- ✅ Helper methods
- ✅ Status management
- ✅ Data formatting

## 📈 Benefits

1. **Data Persistence** - Routes and schedules survive system restarts
2. **Scalability** - Handles multiple concurrent picking sessions
3. **Traceability** - Complete audit trail of all operations
4. **Flexibility** - Supports various dispatch scenarios
5. **Integration Ready** - Easy to integrate with existing systems
6. **Real-time Tracking** - Live updates on dispatch progress
7. **Professional Features** - Driver assignment, vehicle tracking, proof of delivery

This database structure provides a solid foundation for a professional-grade picking and dispatch management system!
