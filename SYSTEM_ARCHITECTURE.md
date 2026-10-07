# System Architecture

## Overview
MIMS is built with a three-tier architecture:
- Presentation: Vue.js with Inertia.js for a reactive SPA experience
- Application: Laravel backend for routing, validation, business logic, and API handling
- Data: MySQL/MariaDB database storing core entities and relationships

## Backend Architecture
- Controllers manage requests and responses
- Models represent database entities and relationships
- Form Requests handle validation rules
- Imports classes process Excel/CSV files
- Middleware protects routes with auth, CORS, and permissions

## Frontend Architecture
- Pages map to top-level routes
- Components provide reusable UI elements
- Layouts define application shell and navigation
- Vuex stores shared state
- Axios handles API communication

## Data Flow
1. User initiates action in UI
2. Frontend validates input and sends HTTP request
3. Backend validates request and checks permissions
4. Business logic executes and stores data in the database
5. Response returns to frontend
6. UI updates and notifications are shown

## Security
- Session auth for web users
- Sanctum token auth for API
- Role-based permissions with Spatie
- Validation and sanitization on all input

## Scalability
- Use queue workers for import and notification jobs
- Cache frequently used lookup data
- Add database replicas for report-heavy workloads
- Load balance multiple web servers behind a reverse proxy
