# MIMS Project Overview

## Purpose
MIMS is a Laravel-based Meter Installation Management System that supports bulk meter inventory management, installation scheduling, recording installations, reporting, and integration with external systems.

## Features
- Meter inventory upload and tracking
- Schedule management and bulk installation uploads
- Installation data capture with GPS coordinates, seals, and customer details
- Role-based access control with Spatie permissions
- Reporting and export capabilities
- API for integration and external system sync

## Architecture
- Laravel backend using Controllers, Models, Middleware, Requests, and Excel import classes
- Vue.js/Inertia frontend with reusable components, pages, and layouts
- MySQL database for persistent storage
- Optional Redis / file caching and Laravel queue processing for async tasks

## Core Modules
- Installations - recording meter installations and upload templates
- Inventory - meter list upload and status tracking
- Schedules - appointment management and plan entries
- Teams - installers, supervisors, and assigned meter teams
- Complaints - complaint tracking and resolution
- Replacements - meter replacement logs

## Sample Flow
1. Store Manager uploads meter inventory
2. Region Admin uploads installation schedules
3. Data Entry records installations one-by-one or via batch upload
4. Completed installations update meter status and trigger reports
5. Integrations such as billing sync or mapping services consume API data
