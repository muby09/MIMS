# MIMS - Meter Installation Management System

A comprehensive web-based system for managing electricity meter installations, inventory, scheduling, and customer records for power distribution companies.

## 📋 Table of Contents

- [Project Overview](#project-overview)
- [Features](#features)
- [Technology Stack](#technology-stack)
- [System Requirements](#system-requirements)
- [System Architecture](#system-architecture)
- [Installation & Setup](#installation--setup)
- [Project Structure](#project-structure)
- [User Roles & Permissions](#user-roles--permissions)
- [Key Workflows](#key-workflows)
- [Bulk Installation Upload](#bulk-installation-upload)
- [Database Schema](#database-schema)
- [API Endpoints](#api-endpoints)
- [Configuration](#configuration)
- [Troubleshooting](#troubleshooting)
- [Contributing](#contributing)
- [Support](#support)

---

## 🎯 Project Overview

**MIMS** (Meter Installation Management System) is a role-based Laravel 11 application designed for power distribution companies to manage the complete lifecycle of electricity meter installations.

### Core Purpose
- Manage meter inventory across regions
- Schedule installation appointments
- Assign installation teams
- Record installation details with GPS coordinates
- Track complaint and replacement records
- Generate reports and analytics

### Key Statistics
- **40+ data fields** per installation record
- **8 user roles** with granular permissions
- **5-step workflow** from meter upload to installation completion
- **Bulk import capability** for multiple installations
- **Regional multi-tenancy** support

---

## ✨ Features

### 1. **Meter Management**
- Upload meter inventory via CSV files
- Track meter status (Available, Assigned, Installed)
- Store meter specifications (type, brand, phase, serial)
- Search and filter meter lists
- Meter list by region and business unit

### 2. **Installation Scheduling**
- Upload scheduled installations from Excel files
- Link customer information to meters
- Track scheduled vs. completed installations
- Download installation templates

### 3. **Installation Recording**
- **Single Entry**: Form-based individual installation recording
- **Bulk Import**: Upload multiple installations via Excel
- GPS coordinate recording (X, Y coordinates)
- Capture seal numbers and infrastructure details
- Automatic supervisor assignment via team lookup
- Comprehensive validation

### 4. **Team Management**
- Create and manage installation teams
- Assign team members (installers, supervisors)
- Track team performance metrics
- Team-based work allocation

### 5. **Complaint & Replacement Tracking**
- Record customer complaints
- Log meter replacements
- Track resolution status
- Historical complaint records

### 6. **Reports & Analytics**
- Daily installation metrics
- Monthly installation trends
- Regional performance reports
- Export installation data by date range
- Customer billing information

---

## 🛠 Technology Stack

### Backend
- **Framework**: Laravel 11 with Inertia.js
- **Language**: PHP 8.2+
- **Database**: MySQL/MariaDB
- **ORM**: Eloquent
- **Excel Handling**: Maatwebsite/Excel v3.1
- **Authentication**: Laravel Sanctum (API) + Breeze (Web)
- **Authorization**: Spatie Laravel Permission (RBAC)
- **Validation**: Laravel Request/Form Request validation
- **Task Queue**: Laravel Queue (Database/Redis driver)
- **Caching**: Redis/File-based cache

### Frontend
- **Framework**: Vue.js 3.4+ with Inertia.js
- **Build Tool**: Vite 5.0+
- **CSS**: Tailwind CSS 3.2+
- **Icons**: FontAwesome 6.6+
- **State Management**: Vuex 4.1+
- **Charts**: Vue Google Charts
- **HTTP Client**: Axios
- **Date/Time**: Moment.js

### Development Tools
- **PHP Version**: 8.2+
- **Node.js**: 16+ (for build tools)
- **Composer**: Latest
- **NPM**: 10+
- **Code Quality**: Laravel Pint, PHPStan (optional)

### Infrastructure
- **Web Server**: Apache/Nginx with PHP-FPM
- **Session Storage**: Database/Redis
- **File Storage**: Local filesystem (S3 optional)
- **Logging**: Monolog with file rotation
- **Task Scheduling**: Laravel Scheduler (cron)

---

## 📦 System Requirements

- PHP 8.2 or higher
- MySQL 5.7+ or MariaDB 10.3+
- Node.js 16+ and npm 10+
- Composer
- 2GB RAM minimum
- 500MB disk space

---

## 🏗️ System Architecture

### High-Level Architecture

MIMS follows a modern **three-tier architecture** with clear separation of concerns:

```
┌─────────────────────────────────────────┐
│         Presentation Layer              │
│  (Vue.js SPA with Inertia.js)           │
│  - Components, Pages, Layouts            │
│  - Real-time UI updates                  │
└──────────────┬──────────────────────────┘
               │ HTTP/REST API
┌──────────────▼──────────────────────────┐
│       Application Layer                  │
│      (Laravel Backend)                   │
│  - Controllers, Services, Models        │
│  - Business Logic & Validation          │
│  - Authentication & Authorization       │
└──────────────┬──────────────────────────┘
               │ SQL Queries
┌──────────────▼──────────────────────────┐
│        Data Layer                        │
│    (MySQL/MariaDB)                       │
│  - 40+ tables with relationships        │
│  - ACID transactions, indexes            │
└─────────────────────────────────────────┘
```

### Component Architecture

**Backend Components:**
- **Controllers**: Handle HTTP requests, route logic to services
- **Models**: Eloquent models with relationships (Installation, MeterList, Schedule)
- **Services**: Encapsulate business logic (InstallationService, ValidationService)
- **Repositories**: Optional abstraction layer for data access
- **Events**: Dispatch events for webhooks and notifications
- **Jobs**: Async task processing (imports, notifications)
- **Middleware**: Authentication, authorization, rate limiting

**Frontend Components:**
- **Pages**: Route-based components (Installations, Dashboard, Inventory)
- **Components**: Reusable UI elements (Forms, Tables, Modals)
- **Services**: API client, state management, utilities
- **Layouts**: Page structure and navigation

### Data Flow Architecture

```
User Action (e.g., Record Installation)
    ↓
Form Validation (Frontend)
    ↓
HTTP Request to Controller
    ↓
Request Validation (Backend)
    ↓
Authorization Check
    ↓
Business Logic Processing
    ↓
Database Transaction
    ↓
Event Dispatch (for webhooks, notifications)
    ↓
Async Job Queue Processing
    ↓
Response Generation
    ↓
Frontend Update
```

### Security Architecture

**Authentication & Authorization:**
- **Web**: Session-based (Laravel Breeze)
- **API**: Token-based (Laravel Sanctum)
- **Authorization**: Role-Based Access Control (RBAC) with Spatie Permissions
- **Validation**: Input sanitization, type checking, format validation
- **Protection**: SQL injection prevention, CSRF tokens, XSS protection

**Data Protection:**
- Passwords: bcrypt hashing with salt
- API tokens: Encrypted storage
- Sensitive fields: Access restricted by role
- Audit trail: Creator/timestamp tracking

### Performance Architecture

**Optimization Strategies:**
- **Database Indexing**: PKs, UNIQUEs, FKs, and composite indexes on frequently queried fields
- **Query Optimization**: Eager loading relationships, pagination, column selection
- **Caching**: Query result caching (1-24 hour TTLs), view fragment caching
- **Asset Optimization**: Minification, compression (gzip), lazy loading
- **Async Processing**: Queue heavy operations, webhook delivery in background

**Target Metrics:**
- Page load time: < 2 seconds
- API response time: < 500ms
- Database query time: < 100ms
- Bulk import (10K records): < 30 seconds

### Scalability Architecture

**Vertical Scaling:**
- Increase server RAM and CPU
- Database query optimization
- Connection pooling

**Horizontal Scaling:**
- Load balancer across multiple app servers
- Read replicas for database reporting
- Redis for distributed caching/sessions
- Multiple queue workers for async jobs

**Data Scaling:**
- Archive old records (> 2 years)
- Database partitioning by region/date
- Separate reporting database (optional)
- Index maintenance

### Module Organization

```
app/
├── Models/
│   ├── Installation/          # Core installation entities
│   │   ├── Installation.php   # Main model (40+ fields)
│   │   ├── Complain.php       # Complaints
│   │   └── Replacement.php    # Replacements
│   ├── Inventory/             # Inventory management
│   │   ├── MeterList.php      # Meter inventory
│   │   ├── Item.php           # Equipment items
│   │   └── DamagedItem.php    # Damaged items tracking
│   ├── Region/                # Regional organization
│   │   ├── Region.php
│   │   ├── Team.php
│   │   ├── Schedule.php
│   │   └── Feeder*.php        # 33kV & 11kV feeders
│   ├── Admin/                 # System configuration
│   │   └── Meter/             # Meter types & brands
│   └── User.php               # User accounts & authentication
│
├── Http/
│   ├── Controllers/
│   │   ├── Api/               # REST API controllers
│   │   ├── Region/            # Regional operations
│   │   ├── Inventory/         # Inventory operations
│   │   └── Staff/             # Staff management
│   ├── Middleware/            # Auth, CORS, rate limiting
│   └── Requests/              # Form request validation
│
├── Services/                  # Business logic layer
│   ├── InstallationService.php
│   ├── MeterService.php
│   └── ValidationService.php
│
├── Events/                    # Event dispatching
│   ├── InstallationCompleted.php
│   ├── ComplaintReported.php
│   └── ReplacementRecorded.php
│
├── Jobs/                      # Async tasks
│   ├── ProcessImport.php
│   ├── SendNotification.php
│   └── TriggerWebhook.php
│
├── Imports/                   # Excel/CSV import logic
│   ├── ImportMeterList.php
│   ├── ImportScheduleList.php
│   └── ImportInstallationList.php
│
└── Providers/                 # Service providers
    └── AppServiceProvider.php
```

### Deployment Architecture

**Development:**
- Local development server
- Hot reload for assets
- SQLite or local MySQL
- Sample data seeding

**Staging:**
- Production-like environment
- SSL/TLS certificates
- Real MySQL database
- Queue workers running
- Scheduled tasks enabled

**Production:**
- Load-balanced servers
- Database replication
- Redis caching/sessions
- Queue processing
- Regular backups
- Monitoring & alerting
- Disaster recovery

### Database Relationships

```
users
 ├─ hasMany(Installation) [as creator]
 ├─ hasMany(Team)
 ├─ hasMany(UserDetail)
 └─ belongsToMany(Role)

Installation (Core Entity - ~40 fields)
 ├─ belongsTo(Region)
 ├─ belongsTo(Team)
 ├─ belongsTo(State)
 ├─ belongsTo(Feeder33)
 ├─ belongsTo(Feeder11)
 ├─ hasMany(Complain)
 ├─ hasMany(Replacement)
 └─ belongsTo(User) [as creator]

Team
 ├─ hasMany(User) [members]
 ├─ hasMany(Installation)
 ├─ belongsTo(Region)
 └─ belongsToMany(Supervisor)

MeterList
 ├─ belongsTo(Region)
 ├─ hasMany(Installation)
 └─ belongsToMany(Team) [team_assigned_meters]

Region
 ├─ hasMany(Installation)
 ├─ hasMany(Team)
 ├─ hasMany(MeterList)
 ├─ hasMany(Schedule)
 ├─ hasMany(Feeder33)
 └─ hasMany(Feeder11)

Schedule
 ├─ belongsTo(Region)
 └─ hasMany(Installation)
```

**See [SYSTEM_ARCHITECTURE.md](SYSTEM_ARCHITECTURE.md) for comprehensive technical architecture documentation.**

---

## 🚀 Installation & Setup

### Step 1: Clone Repository
```bash
git clone <repository-url>
cd mims
```

### Step 2: Install Dependencies

**PHP Dependencies:**
```bash
composer install
```

**JavaScript Dependencies:**
```bash
npm install
```

### Step 3: Environment Configuration
```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` and configure:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mims
DB_USERNAME=root
DB_PASSWORD=
```

### Step 4: Database Setup
```bash
php artisan migrate
php artisan db:seed
php artisan storage:link
```

### Step 5: Build Frontend Assets
```bash
npm run build
```

For development with hot reload:
```bash
npm run dev
```

### Step 6: Start Application
```bash
php artisan serve
# Application runs at http://127.0.0.1:8000
```

---

## 📁 Project Structure

```
mims/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── DependencyController.php      # Schedules, zones, tariffs
│   │   │   ├── Inventory/MeterController.php # Installation management
│   │   │   └── Region/FeederController.php   # Feeder management
│   │   ├── Middleware/
│   │   └── Requests/
│   ├── Imports/
│   │   ├── ImportMeterList.php               # Meter CSV import
│   │   ├── ImportScheduleList.php            # Schedule Excel import
│   │   └── ImportInstallationList.php        # Installation bulk import
│   ├── Models/
│   │   ├── Installation/
│   │   │   ├── Installation.php              # Installation model
│   │   │   ├── Complain.php
│   │   │   └── Replacement.php
│   │   ├── Region/
│   │   │   ├── Region.php
│   │   │   ├── Schedule.php
│   │   │   └── Team.php
│   │   └── Inventory/
│   │       └── MeterList.php
│   └── Providers/
├── bootstrap/custom/
│   ├── constants.php                         # Global constants
│   ├── helper.php                            # Helper functions
│   └── dbQuery.php                           # Database queries
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── mims.sql                              # Database dump
├── resources/
│   ├── js/
│   │   ├── Pages/
│   │   │   ├── Region/Installations.vue      # Main installation page
│   │   │   ├── Staff/Staff.vue
│   │   │   ├── Inventory/MeterList.vue
│   │   │   └── ...
│   │   ├── Components/
│   │   │   ├── Tabs/
│   │   │   │   ├── InstallationListTab.vue   # Individual form entry
│   │   │   │   ├── InstallationUploadTab.vue # Bulk upload
│   │   │   │   ├── InstalledTab.vue          # View installations
│   │   │   │   └── ...
│   │   │   └── Layouts/MainLayout.vue
│   │   └── app.js
│   ├── css/
│   └── views/
├── routes/
│   ├── web.php                               # Main routes
│   ├── auth.php
│   └── console.php
├── public/
│   ├── files/
│   │   ├── Installation_Template.csv
│   │   ├── Approved Shedule template.xlsx
│   │   └── images/
│   └── build/
├── storage/
├── tests/
└── composer.json
```

---

## 👥 User Roles & Permissions

### 8 System Roles

| Role | Access | Responsibilities |
|------|--------|------------------|
| **Super Admin** | Full system access | System configuration, role management, global reporting |
| **Management** | Overview/Monitoring | High-level dashboards, statistics, status monitoring |
| **Region Admin** | Region-level | Teams, staff, schedule uploads, regional configuration |
| **Supervisor** | Team supervision | Team assignment, installer management, monitoring |
| **Data Entry** | Installation recording | Record installations (individual & bulk), search installed |
| **Staff** | Limited access | View-only access to assigned data |
| **Installer** | Execution | Access to assigned meter lists and team work |
| **Store Manager** | Inventory | Upload meter lists, manage inventory |

---

## 🔄 Key Workflows

### Workflow 1: Standard Installation Process

```
1. STORE MANAGER: Upload Meter List (CSV)
   └─ Meters added with status = 1 (Available)

2. REGION ADMIN: Upload Schedule (Excel)
   └─ Installation appointments created

3. DATA ENTRY: View "Record From List"
   └─ Displays scheduled installations from Schedule table

4. DATA ENTRY: Click "Form" on schedule item
   └─ Opens modal with pre-filled customer data
   └─ Completes required meter installation fields
   └─ Submits → Meter status changes to 3 (Installed)

5. DATA ENTRY: View "Installed List"
   └─ Completed installations displayed
   └─ Can search, filter, or export
```

### Workflow 2: Bulk Installation Upload (NEW)

```
1. DATA ENTRY: Navigate to /installations
   └─ Click "Upload Excel" tab

2. Download Template (CSV/Excel)
   └─ 32 columns (all installation fields)

3. Fill Template with Multiple Records
   └─ One row per installation
   └─ All required fields must be populated

4. Upload File
   └─ System processes each row
   └─ Validates against meter DB, seal uniqueness, etc.
   └─ Updates meter status to 3 on success
   └─ Shows success count + error details

5. Review Results
   └─ Success message with count
   └─ Error table with row numbers and issues
```

---

## 📤 Bulk Installation Upload

### How to Use

1. **Navigate to Installation Page**
   - URL: `/installations`
   - Login as "Data Entry" role

2. **Select "Upload Excel" Tab**
   - Second tab at top of page

3. **Download Template**
   - Click "📥 Download Template" button
   - Opens `Installation_Template.csv`

4. **Fill Template**
   - Each row = one installation
   - Headers must match template exactly
   - All **bold** fields are required

5. **Upload File**
   - Click "Choose File"
   - Select completed CSV/Excel file
   - Click "Upload File" button

6. **View Results**
   - ✓ Success message shows records imported
   - ✗ Error table shows failed rows with issues
   - Click details to expand error information

### Template Fields (32 Columns)

**Required Fields:**
- meter_number, pole, tariff, advtariff, fullname, gsm, premises, phase, address
- feeder_33kv, feeder_11kv, meter_type, meter_brand, account_no, business_unit
- x_cordinate, y_cordinate, installer, seal, state, preload

**Optional Fields:**
- email, remarks, meter_tech, estimated, rf_channel, dt_name, dt_code, zone
- upriser, din, doi

### Validation Rules

| Field | Rule |
|-------|------|
| meter_number | Must exist in meter_lists, not already installed |
| seal | Must be unique globally |
| gsm | Exactly 11 digits |
| x_cordinate, y_cordinate | Numeric values |
| pole | Numeric required |
| state, tariff, advtariff | Required, must exist |
| feeder_33kv, feeder_11kv | Required, linked to zone |
| installer | Required, must have supervisor assigned |

### Error Examples

```
Row 2: "Meter number (METER001) already installed"
Row 3: "Seal (12345) already used by another customer"
Row 5: "Phone number must be 11 digits"
Row 7: "No supervisor assigned to installer team"
```

---

## 🗄 Database Schema

### Core Tables

#### `installations` (40+ fields)
- Stores complete meter installation records
- Fields: meter_number, seal, fullname, gsm, address, coordinates, etc.
- Status: Tracks by creator, region, date

#### `meter_lists`
- Inventory of all available meters
- Fields: meter_number, phase, type, brand, status
- Status codes: 1=Available, 2=Assigned, 3=Installed

#### `schedules`
- Installation schedule/appointments
- Links customer to allocated meter
- Pre-populated with account information

#### `teams`
- Installation teams in region
- Links installers to supervisors
- Used for team-based assignment

#### Related Tables
- `complains` - Customer complaints for installations
- `replacements` - Meter replacement records
- `regions` - Regional boundaries
- `feeders` (33kv & 11kv) - Electrical infrastructure
- `trading_zones` - Zone groupings
- `users` - Staff/user accounts

### Key Relationships

```
Installation
├─ BelongsTo Region
├─ BelongsTo Feeder33kv
├─ BelongsTo Feeder11kv
└─ BelongsTo Team

MeterList
├─ HasMany Installations
└─ BelongsTo Region

Schedule
├─ BelongsTo Region
└─ HasMany Installations
```

---

## 🔌 API Endpoints & Integration

### API Overview

MIMS provides comprehensive REST API endpoints for system integration with third-party applications. All API requests require authentication via **Laravel Sanctum** tokens.

#### Authentication

**Generate API Token:**
```php
// Programmatically in application
$token = $user->createToken('api-token')->plainTextToken;
```

**Using Token in Requests:**
```bash
curl -H "Authorization: Bearer YOUR_API_TOKEN" \
     -H "Accept: application/json" \
     https://your-domain/api/installations
```

### Base URL
```
https://your-domain/api/v1
```

### Content Types
- Request: `application/json`
- Response: `application/json`

---

### 📥 Installation Management API

#### 1. Create Single Installation
```
POST /api/v1/installations
```

**Request:**
```json
{
  "meter_number": "METER001",
  "pole": 5,
  "tariff": "Residential",
  "advtariff": "Single Phase",
  "fullname": "John Doe",
  "gsm": "08012345678",
  "premises": "No. 5",
  "phase": "Single Phase",
  "address": "Lagos Island, Lagos",
  "feeder_33kv": "FDR-001",
  "feeder_11kv": "FDR-11-001",
  "meter_type": "Digital",
  "meter_brand": "Siemens",
  "account_no": "ACC12345",
  "business_unit": "LCCN",
  "x_cordinate": 6.4549,
  "y_cordinate": 3.3574,
  "installer": "TEAM-001",
  "seal": "SEAL123456",
  "state": "Lagos",
  "preload": 0,
  "email": "john@example.com",
  "remarks": "Installation completed successfully",
  "region_id": 1
}
```

**Response (201 Created):**
```json
{
  "status": "success",
  "message": "Installation recorded successfully",
  "data": {
    "id": 1001,
    "meter_number": "METER001",
    "seal": "SEAL123456",
    "installation_date": "2026-05-11T14:30:00Z",
    "status": 3,
    "created_by": "user_id",
    "region_id": 1
  }
}
```

**Error Response (422 Unprocessable Entity):**
```json
{
  "status": "error",
  "message": "Validation failed",
  "errors": {
    "meter_number": ["Meter must be unique"],
    "seal": ["This seal is already used"],
    "gsm": ["Phone must be 11 digits"]
  }
}
```

---

#### 2. Bulk Installation Upload
```
POST /api/v1/installations/bulk
```

**Request (multipart/form-data):**
```
file: [CSV/Excel file with installation records]
region_id: 1
created_by: user_id
```

**Response (201 Created):**
```json
{
  "status": "success",
  "message": "Bulk upload completed",
  "data": {
    "total_records": 150,
    "successful": 148,
    "failed": 2,
    "errors": [
      {
        "row": 5,
        "meter_number": "METER005",
        "error": "Meter already installed"
      },
      {
        "row": 12,
        "seal": "SEAL999",
        "error": "Seal already in use"
      }
    ]
  }
}
```

---

#### 3. Get Installation Details
```
GET /api/v1/installations/{id}
```

**Response (200 OK):**
```json
{
  "status": "success",
  "data": {
    "id": 1001,
    "meter_number": "METER001",
    "fullname": "John Doe",
    "gsm": "08012345678",
    "address": "Lagos Island",
    "seal": "SEAL123456",
    "installation_date": "2026-05-11",
    "status": 3,
    "coordinates": {
      "x": 6.4549,
      "y": 3.3574
    },
    "team": {
      "id": 1,
      "name": "Team Alpha",
      "supervisor": "Ahmed Malik"
    },
    "region": {
      "id": 1,
      "name": "Lagos Region"
    }
  }
}
```

---

#### 4. List Installations (with Filters)
```
GET /api/v1/installations?region_id=1&status=3&date_from=2026-05-01&date_to=2026-05-31&page=1&per_page=50
```

**Response (200 OK):**
```json
{
  "status": "success",
  "data": [
    {
      "id": 1001,
      "meter_number": "METER001",
      "fullname": "John Doe",
      "installation_date": "2026-05-11",
      "status": 3
    }
  ],
  "pagination": {
    "total": 1250,
    "per_page": 50,
    "current_page": 1,
    "last_page": 25
  }
}
```

---

#### 5. Search Installations
```
GET /api/v1/installations/search?q={query}
```

**Query Parameters:**
- `q`: Search by meter number, seal, customer name, or phone
- `type`: Filter by field type (meter, seal, customer)
- `region_id`: Region filter

**Response (200 OK):**
```json
{
  "status": "success",
  "data": [
    {
      "id": 1001,
      "meter_number": "METER001",
      "fullname": "John Doe",
      "seal": "SEAL123456",
      "region": "Lagos"
    }
  ],
  "total": 1
}
```

---

#### 6. Export Installations
```
POST /api/v1/installations/export
```

**Request:**
```json
{
  "region_id": 1,
  "date_from": "2026-05-01",
  "date_to": "2026-05-31",
  "format": "csv"
}
```

**Response:**
- Returns file download (CSV/Excel/PDF)

---

### 📊 Meter Management API

#### Get Meter List
```
GET /api/v1/meters?region_id=1&status=1&page=1&per_page=100
```

**Response:**
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "meter_number": "METER001",
      "phase": "Single Phase",
      "type": "Digital",
      "brand": "Siemens",
      "status": 1,
      "status_label": "Available"
    }
  ],
  "pagination": {
    "total": 5000,
    "per_page": 100,
    "current_page": 1
  }
}
```

#### Upload Meter List
```
POST /api/v1/meters/upload
```

**Request (multipart/form-data):**
```
file: [CSV file with meter records]
region_id: 1
```

---

### 📅 Schedule Management API

#### Get Schedule List
```
GET /api/v1/schedules?region_id=1&status=pending
```

**Response:**
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "meter_number": "METER001",
      "customer": "John Doe",
      "phone": "08012345678",
      "scheduled_date": "2026-05-15",
      "status": "pending"
    }
  ]
}
```

#### Upload Schedule
```
POST /api/v1/schedules/upload
```

---

### 👥 Team & Staff API

#### Get Teams
```
GET /api/v1/teams?region_id=1
```

**Response:**
```json
{
  "status": "success",
  "data": [
    {
      "id": 1,
      "name": "Team Alpha",
      "region_id": 1,
      "supervisor": {
        "id": 10,
        "name": "Ahmed Malik"
      },
      "members": [
        {
          "id": 11,
          "name": "Installer 1"
        },
        {
          "id": 12,
          "name": "Installer 2"
        }
      ]
    }
  ]
}
```

#### Create Team
```
POST /api/v1/teams
```

**Request:**
```json
{
  "name": "Team Beta",
  "region_id": 1,
  "supervisor_id": 10,
  "members": [11, 12, 13]
}
```

---

### 🚨 Complaints & Replacements API

#### Report Complaint
```
POST /api/v1/complaints
```

**Request:**
```json
{
  "installation_id": 1001,
  "complaint_type": "Meter not working",
  "description": "Meter shows zero consumption",
  "customer_name": "John Doe",
  "phone": "08012345678",
  "priority": "high"
}
```

**Response:**
```json
{
  "status": "success",
  "data": {
    "id": 5001,
    "installation_id": 1001,
    "complaint_date": "2026-05-11T14:30:00Z",
    "status": "open"
  }
}
```

#### Get Complaints
```
GET /api/v1/complaints?region_id=1&status=open
```

#### Record Replacement
```
POST /api/v1/replacements
```

**Request:**
```json
{
  "installation_id": 1001,
  "reason": "Meter faulty",
  "old_meter": "METER001",
  "new_meter": "METER002",
  "new_seal": "SEAL789"
}
```

---

### 📍 Regional Data API

#### Get Regions
```
GET /api/v1/regions
```

#### Get Feeders
```
GET /api/v1/feeders/{region_id}
```

#### Get Trading Zones
```
GET /api/v1/zones?state={state}
```

---

### 🔌 System Integration API

#### Sync with External Billing System
```
POST /api/v1/integrations/billing/sync
```

**Request:**
```json
{
  "meter_number": "METER001",
  "account_number": "ACC12345",
  "customer_name": "John Doe",
  "installation_date": "2026-05-11",
  "meter_type": "Digital"
}
```

**Response:**
```json
{
  "status": "success",
  "message": "Sync completed",
  "external_reference": "BILL-2026-005001"
}
```

#### Sync with GPS Tracking System
```
POST /api/v1/integrations/gps/sync
```

**Request:**
```json
{
  "installation_id": 1001,
  "x_coordinate": 6.4549,
  "y_coordinate": 3.3574,
  "timestamp": "2026-05-11T14:30:00Z"
}
```

---

### Error Handling & Status Codes

| Code | Meaning | Example |
|------|---------|---------|
| 200 | OK | Successful GET request |
| 201 | Created | Successful POST request |
| 204 | No Content | Successful DELETE |
| 400 | Bad Request | Invalid parameters |
| 401 | Unauthorized | Missing/invalid token |
| 403 | Forbidden | Insufficient permissions |
| 404 | Not Found | Resource doesn't exist |
| 422 | Unprocessable | Validation failed |
| 429 | Too Many Requests | Rate limit exceeded |
| 500 | Server Error | Internal error |

**Error Response Format:**
```json
{
  "status": "error",
  "message": "Error description",
  "errors": {
    "field": ["error message"]
  }
}
```

---

### 🔐 Rate Limiting

- **Authenticated Users**: 1000 requests per hour
- **Rate Limit Headers**:
  ```
  X-RateLimit-Limit: 1000
  X-RateLimit-Remaining: 999
  X-RateLimit-Reset: 1620000000
  ```

---

### 📨 Webhooks

Enable real-time notifications by subscribing to events:

#### Subscribe to Webhook
```
POST /api/v1/webhooks/subscribe
```

**Request:**
```json
{
  "event": "installation.completed",
  "url": "https://your-system.com/webhook",
  "auth_token": "your_secret_token"
}
```

**Supported Events:**
- `installation.completed` - When installation is recorded
- `installation.failed` - When installation fails validation
- `complaint.reported` - When complaint is logged
- `replacement.completed` - When replacement is done
- `meter.status_changed` - When meter status changes

**Webhook Payload Example:**
```json
{
  "event": "installation.completed",
  "timestamp": "2026-05-11T14:30:00Z",
  "data": {
    "id": 1001,
    "meter_number": "METER001",
    "seal": "SEAL123456",
    "region_id": 1
  }
}
```

---

## 🔗 System Integration Scenarios

### Scenario 1: Billing System Integration

**Objective**: Sync MIMS installations with billing/customer database

**Flow:**
```
1. Installation recorded in MIMS
   ↓
2. Webhook triggered: installation.completed
   ↓
3. Billing system receives data via webhook
   ↓
4. Create billing account with meter details
   ↓
5. Return confirmation with billing reference
   ↓
6. MIMS stores billing_reference in installation record
```

**Implementation:**
```php
// app/Events/InstallationCompleted.php
public function broadcastOn()
{
    return new Channel('installations');
}

// In webhook listener on external system
$billReference = $billingSystem->createBillingAccount([
    'meter_number' => $data['meter_number'],
    'customer_name' => $data['fullname'],
    'account_no' => $data['account_no'],
    'installation_date' => $data['installation_date']
]);
```

---

### Scenario 2: GPS/Map System Integration

**Objective**: Display meter installations on interactive map

**Flow:**
```
1. Fetch installations with GPS coordinates
   ↓
2. Send to mapping API (Google Maps, Mapbox, etc.)
   ↓
3. Display clusters by region
   ↓
4. Show installation details on marker click
   ↓
5. Enable filtering by date, status, team
```

**API Usage:**
```bash
# Get all installations with coordinates
GET /api/v1/installations?region_id=1&fields=id,meter_number,x_cordinate,y_cordinate,installation_date,status

# Response contains coordinate data for mapping
```

---

### Scenario 3: Mobile App Integration

**Objective**: Allow field installers to use mobile app with sync

**Flow:**
```
1. Mobile app authenticates with API token
   ↓
2. Download assigned meter list (offline-capable)
   ↓
3. Installer records installation in mobile app
   ↓
4. When online, sync with MIMS via API
   ↓
5. MIMS returns confirmation
   ↓
6. App shows sync status
```

**API Endpoints for Mobile:**
```
GET /api/v1/installations/assigned-meters
POST /api/v1/installations (record new)
GET /api/v1/installations/{id} (get details)
POST /api/v1/installations/{id}/photos (upload photos)
```

---

### Scenario 4: Inventory Management System

**Objective**: Sync MIMS meter inventory with warehouse system

**Flow:**
```
1. MIMS sends meter inventory changes
   ↓
2. External inventory system receives via webhook
   ↓
3. Update warehouse stock records
   ↓
4. Log inventory transactions
   ↓
5. Send alerts for low stock
```

**Webhook Integration:**
```json
{
  "event": "meter.status_changed",
  "data": {
    "meter_number": "METER001",
    "old_status": 1,
    "new_status": 3,
    "status_label": "Installed",
    "timestamp": "2026-05-11T14:30:00Z"
  }
}
```

---

### Scenario 5: CRM/Customer System Integration

**Objective**: Sync customer and installation data with CRM

**Flow:**
```
1. Customer added to CRM
   ↓
2. Trigger MIMS API to create installation schedule
   ↓
3. MIMS adds to schedule list
   ↓
4. Installation completed
   ↓
5. MIMS webhook notifies CRM
   ↓
6. CRM updates customer record with installation status
```

**API Calls:**
```bash
# From CRM to MIMS
POST /api/v1/schedules
{
  "customer_id": "CRM-001",
  "meter_number": "METER001",
  "scheduled_date": "2026-05-15"
}

# From MIMS to CRM via webhook
Event: installation.completed
Data includes: installation_id, meter_number, completion_date
```

---

### Scenario 6: Complaint Management Integration

**Objective**: Route installation complaints to ticketing system

**Flow:**
```
1. Customer reports complaint in MIMS
   ↓
2. Webhook sends complaint to ticketing system
   ↓
3. Ticket created with meter/installation details
   ↓
4. Technician assigned
   ↓
5. Ticket updates sent back to MIMS
   ↓
6. Complaint status updated in MIMS
```

---

### Scenario 7: Analytics/BI System Integration

**Objective**: Feed installation data to BI/analytics platform

**Flow:**
```
1. BI system schedules daily API call
   ↓
2. Fetch new installations: GET /api/v1/installations?date_from=today
   ↓
3. Store in BI database
   ↓
4. Generate dashboards and reports
   ↓
5. Track KPIs: completion rate, regional performance, team metrics
```

---

## 📚 Integration Best Practices

### 1. Authentication & Security
- Use unique API tokens per integration
- Rotate tokens periodically
- Store tokens securely (environment variables)
- Use HTTPS for all requests
- Implement IP whitelisting if possible

### 2. Error Handling
```php
// Retry failed requests with exponential backoff
$retries = 0;
$maxRetries = 3;

while ($retries < $maxRetries) {
    try {
        $response = $client->post('/api/v1/installations', $data);
        break;
    } catch (Exception $e) {
        $retries++;
        sleep(2 ** $retries); // Exponential backoff
    }
}
```

### 3. Data Validation
- Validate all data before sending to MIMS
- Handle validation errors gracefully
- Log validation failures for debugging

### 4. Webhook Verification
```php
// Verify webhook signature on receiving system
$signature = hash_hmac('sha256', $payload, $secret);
if (!hash_equals($signature, $headerSignature)) {
    return 403; // Reject unauthorized webhook
}
```

### 5. Pagination & Limits
- Use pagination for large datasets
- Respect rate limits
- Implement caching where appropriate

### 6. Monitoring & Logging
- Log all API calls for troubleshooting
- Monitor integration health
- Set up alerts for failures

---

### Legacy Dropdown Endpoints

| Endpoint | Data |
|----------|------|
| `/drop-zone-state` | States/zones for region |
| `/drop-zone/{id}` | Zones in state |
| `/drop-feeder-33/{id}` | 33kV feeders |
| `/drop-feeder-11/{id}` | 11kV feeders |
| `/drop-meter-brands` | Meter brands |
| `/drop-meter-types` | Meter types |
| `/drop-supervisors` | Installation supervisors |
| `/drop-installers` | Installation teams |

---

## ⚙ Configuration

### Key Configuration Files

**`.env`** - Environment variables
```env
APP_NAME=MIMS
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=mims
DB_USERNAME=root
DB_PASSWORD=secure_password

CACHE_DRIVER=redis
SESSION_DRIVER=database
QUEUE_CONNECTION=database

MAIL_MAILER=smtp
MAIL_FROM_ADDRESS=no-reply@mims.local
MAIL_FROM_NAME="MIMS Notifications"

SANCTUM_STATEFUL_DOMAINS=localhost,127.0.0.1
SANCTUM_EXPIRATION=10080

FILE_UPLOAD_MAX_SIZE=50M
```

**`config/app.php`** - Application settings
```php
'timezone' => 'Africa/Lagos',
'locale' => 'en',
'fallback_locale' => 'en',
'cipher' => 'AES-256-CBC',
```

**`config/database.php`** - Database configuration
```php
// Connection pooling, query logging, strict mode
'strict' => false,
'engine' => 'InnoDB',
```

**`config/cache.php`** - Caching strategy
```php
// Redis/File cache with TTLs
'default' => env('CACHE_DRIVER', 'file'),
'ttl' => 3600, // 1 hour default
```

**`config/queue.php`** - Background job queue
```php
'default' => env('QUEUE_CONNECTION', 'database'),
'failed' => [
    'driver' => env('QUEUE_FAILED_DRIVER', 'database'),
],
```

**`config/permission.php`** - Role-based access control
```php
// Spatie Laravel Permission configuration
'roles_table' => 'roles',
'permissions_table' => 'permissions',
'model_has_roles' => 'model_has_roles',
```

**`config/logging.php`** - Logging configuration
```php
'channels' => [
    'stack' => ['driver' => 'stack', 'channels' => ['single']],
    'single' => [
        'driver' => 'single',
        'path' => storage_path('logs/laravel.log'),
        'level' => env('LOG_LEVEL', 'debug'),
    ],
],
```

**`tailwind.config.js`** - Tailwind CSS customization
```javascript
module.exports = {
  theme: {
    extend: {
      colors: { /* custom colors */ },
    },
  },
}
```

**`vite.config.js`** - Build configuration
```javascript
export default defineConfig({
  plugins: [
    laravel({
      input: ['resources/css/app.css', 'resources/js/app.js'],
      refresh: true,
    }),
    vue(),
  ],
})
```

### Caching Strategy

**Query Result Caching:**
```php
// Cache meter lists (1 hour)
$meters = Cache::remember('meters_region_'.$regionId, 3600, fn() => 
    MeterList::where('region_id', $regionId)->get()
);

// Cache regions (24 hours)
$regions = Cache::remember('all_regions', 86400, fn() => 
    Region::all()
);

// Cache feeders (24 hours)
$feeders = Cache::remember('feeders_'.$regionId, 86400, fn() => 
    Feeder::where('region_id', $regionId)->get()
);
```

**Cache Invalidation:**
```php
// Clear cache on data changes
Cache::forget('meters_region_'.$installation->region_id);
Cache::tags(['regions'])->flush(); // Tag-based cache clearing
```

### Queue Processing

**Configuration:**
```php
// .env
QUEUE_CONNECTION=database

// Run queue worker
php artisan queue:work

// Run specific queue
php artisan queue:work database --queue=imports,notifications
```

**Queued Jobs:**
- Bulk imports (InstallationImport)
- Email notifications
- Webhook delivery
- Report generation

**Failed Job Handling:**
```bash
# Retry failed jobs
php artisan queue:retry all

# Monitor queue
php artisan queue:monitor
```

### Task Scheduling

**Scheduled Tasks** (in `app/Console/Kernel.php`):
```php
// Daily report generation
$schedule->command('reports:generate')->daily()->at('02:00');

// Weekly data cleanup
$schedule->command('data:cleanup')->weekly()->sundays()->at('03:00');

// Queue monitoring
$schedule->command('queue:monitor')->everyMinute();
```

### Logging & Monitoring

**Log Files:**
```
storage/logs/
├── laravel.log              # Application logs
└── laravel-YYYY-MM-DD.log  # Daily rotation
```

**Log Levels:**
- DEBUG: Development information
- INFO: General information
- WARNING: Warnings
- ERROR: Error conditions
- CRITICAL: Critical issues

**Monitoring Points:**
```php
// Health check endpoint
Route::get('/health', fn() => response()->json(['status' => 'ok']));

// Performance monitoring
Log::info('Installation recorded', [
    'user_id' => auth()->id(),
    'meter' => $meter_number,
    'duration' => microtime(true) - $start
]);
```

### Security Configuration

**Session Security:**
```php
// config/session.php
'secure' => env('SESSION_SECURE_COOKIES', true), // HTTPS only
'http_only' => true, // Not accessible via JavaScript
'same_site' => 'lax', // CSRF protection
```

**CORS Configuration:**
```php
// config/cors.php
'allowed_origins' => ['*'], // Restrict in production
'allowed_methods' => ['*'],
'allowed_headers' => ['*'],
```

**Rate Limiting:**
```php
// 1000 requests per hour per user
RateLimiter::for('api', fn(Request $request) => 
    Limit::perMinute(100)->by($request->user()?->id ?: $request->ip())
);
```

### Helper Functions (in `bootstrap/custom/helper.php`)

```php
getRegionPid()              // Get current user's region PID
getUserPid()                // Get current user's PID
getInstallerSupervisor()    // Lookup supervisor for installer team
public_id()                 // Generate unique public ID
justDate()                  // Get today's date
formatDate()                // Format date for display
pushData()                  // Standard response format
responseMessage()           // JSON response with message
getRegion()                 // Get user's current region
hasPermission()             // Check user permission
getUserTeam()               // Get user's assigned team
```

### Performance Tuning

**Database Optimization:**
```bash
# Create indexes (run in migration)
php artisan db:create-indexes

# Analyze tables
ANALYZE TABLE installations;
ANALYZE TABLE meter_lists;
```

**PHP Optimization:**
```bash
# Cache autoloader
php artisan optimize

# Cache routes
php artisan route:cache

# Cache config
php artisan config:cache

# Cache views
php artisan view:cache
```

**Asset Optimization:**
```bash
# Build for production
npm run build

# This minifies and optimizes assets
```

---

## 🔧 Troubleshooting

### Upload Not Working
1. **Check file format** - Must be `.csv`, `.xlsx`, or `.xls`
2. **Verify headers** - Must match template exactly
3. **Browser cache** - Clear cache and hard refresh (Ctrl+Shift+R)
4. **Server logs** - Check `storage/logs/laravel.log`

### Meter Not Found Error
```
Error: "Meter number not in Meter list"
Solution: Upload meter to system first (Store Manager > Upload Meter List)
```

### Seal Already Used Error
```
Error: "The seal is used for another customer"
Solution: Use unique seal number or check existing installations
```

### No Supervisor Assigned Error
```
Error: "No Supervisor assigned to selected installer team"
Solution: Configure team supervisor in Teams management before upload
```

### Database Connection Error
```bash
# Verify database connection
php artisan migrate:status

# Reset and seed database
php artisan migrate:fresh --seed
```

### Queue Worker Not Processing Jobs
```bash
# Check if queue worker is running
ps aux | grep "queue:work"

# Restart queue worker
php artisan queue:work --stop-when-empty

# Check failed jobs
php artisan queue:failed
```

### Slow Page Load
```bash
# Enable query logging
DB::enableQueryLog();

# Check slow queries
Log::info(DB::getQueryLog());

# Clear caches
php artisan cache:clear
```

---

## 🤝 Contributing

We welcome contributions! Please follow these guidelines:

### Before Making Changes
1. See [MIMS_PROJECT_OVERVIEW.md](MIMS_PROJECT_OVERVIEW.md) for detailed architecture
2. See [SYSTEM_ARCHITECTURE.md](SYSTEM_ARCHITECTURE.md) for technical architecture
3. Follow Laravel conventions and PSR-12 coding standards
4. Ensure permissions/roles are respected in new features

### Development Process
1. Create feature branch: `git checkout -b feature/name`
2. Make your changes
3. Run tests: `php artisan test`
4. Build assets: `npm run build`
5. Submit pull request

### Code Style
- Use consistent indentation (4 spaces)
- Follow Laravel naming conventions
- Add comments for complex logic
- Keep methods focused and DRY

---

## 📞 Support

### Getting Help

- **Documentation**: See `MIMS_PROJECT_OVERVIEW.md` for detailed specs
- **GitHub Issues**: Report bugs and request features
- **Database**: Schema available in `database/mims.sql`
- **Logs**: Check `storage/logs/laravel.log` for errors

### Common Issues

**Problem**: Page shows blank  
**Solution**: Run `npm run build`, refresh browser, check logs

**Problem**: Permission denied errors  
**Solution**: Verify user role, check role assignment in database

**Problem**: File upload size limit  
**Solution**: Increase upload limit in `.env`: `FILE_UPLOAD_MAX_SIZE=50M`

---

## 📄 License

This project is proprietary software. All rights reserved.

---

## 📊 Project Statistics

- **~50,000 lines of code** (including vendors)
- **40+ database tables** with relationships
- **100+ API endpoints**
- **15+ Vue components**
- **8 user roles** with 50+ permissions
- **100% responsive** mobile/tablet/desktop

---

## 🚀 Recent Updates

### Latest Features
- ✨ **Bulk Installation Upload** - Upload multiple installations via CSV/Excel
- 📊 **Advanced Reporting** - Date-range filtering and exports
- 🔍 **Enhanced Search** - Multi-field search across installations
- 📱 **Mobile Responsive** - Full mobile support with Tailwind CSS

---

**Version**: 1.0.0  
**Last Updated**: March 2026  
**Framework**: Laravel 11 | Vue 3 | Tailwind CSS
