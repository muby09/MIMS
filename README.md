# Meter Information Management System (MIMS)

MIMS manages Meter Installation, Installers and Inventory.
It automates installation processes starting from schedule, to requesting for meter by team lead to capturing which meter is install for a particular customer and when. The application features a clean, modern user interface built with Vue and Blade components.

## Features

-   **Staff Authentication:** Secure registration and login for Users(i.e, Regional Admin, Installer, Data Entry staff, Supervisor and Inventory staff).
-   **Inventory Tracking: 88Maintain a detailed record of the inventory, including meters and accessories, with real-time updates on stock levels and installations.
-   **Installation Management:** Organize and track daily installation schedules, including recording and reporting installations by meter type, region, and meter phase.
-   **Data Validation and Authentication:** Ensure the accuracy and authenticity of data input, facilitating streamlined reporting and compliance with regulations.
•	**Search and Filtering:** Implement easy-to-use search and filtering functionalities to quickly access specific data based on various parameters.
•	**Report Generation:** Provide graphical reports and charts on installation, inventory, and maintenance data.
•	**Maintenance Tracking:** Keep a comprehensive record of meter maintenance and issue resolution, including the management of customer complaints.
•	**API Integration:** Allow integration with external systems for data exchange and extended functionalities.
•	**NERC Template Export:** Ensure exported data complies with the NERC-approved template format for regulatory reporting.


## Tech Stack

-   **Backend:** PHP 8.3+, Laravel 11
-   **Frontend:** Vue, Blade, Vite
-   **Database:** SQLite (default), MySQL, PostgreSQL
-   **Testing:** Pest

## Getting Started

Follow these instructions to get a copy of the project up and running on your local machine for development and testing purposes.

### Prerequisites

-   PHP >= 8.3
-   Composer
-   Node.js & npm
-   A database server (SQLite is the default)

### Installation

1.  **Clone the repository:**
    ```sh
    git clone https://github.com/muby09/mims.git
    cd pixel-jobs
    ```

2.  **Install PHP dependencies:**
    ```sh
    composer install
    ```

3.  **Install JavaScript dependencies:**
    ```sh
    npm install
    ```

4.  **Set up your environment file:**
    Copy the example environment file and generate an application key.
    ```sh
    cp .env.example .env
    php artisan key:generate
    ```

5.  **Configure the database:**
    By default, the application is configured to use SQLite. Simply create the database file:
    ```sh
    touch database/database.sqlite
    ```
    If you prefer to use another database like MySQL, update the `DB_*` variables in your `.env` file accordingly.

6.  **Run database migrations and seeding:**
    This command will create the necessary tables and populate the database with sample jobs and tags.
    ```sh
    php artisan migrate --seed
    ```

7.  **Build frontend assets:**
    ```sh
    npm run build
    ```

### Running the Application

To serve the application locally, you can use the Artisan `serve` command:

```sh
php artisan serve
```

For a more comprehensive development experience that includes the server, queue listener, logs, and Vite asset bundling, use the custom `dev` script:

```sh
composer run dev
```

The application will be available at `http://127.0.0.1:8000`.

## Testing

The application uses Pest for testing. To run the test suite, use the following command:

```sh
composer test
```
This will execute all the feature and unit tests located in the `tests/` directory.
