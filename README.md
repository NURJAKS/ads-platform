# Ads Platform

A modern, Dockerized classified ads platform featuring a Laravel-based monolithic architecture with Blade templates, enhanced by a Go microservice for high-performance image processing.

## 🚀 Architecture

The project is orchestrated using Docker Compose and consists of the following services:

*   **`app` (Laravel 11)**: The core application handling business logic, serving UI (Blade + TailwindCSS), and managing data.
*   **`go-image` (Go)**: A dedicated microservice for efficient image resizing and processing, communicating with the main app via HTTP.
*   **`pg` (PostgreSQL 15)**: The primary relational database.
*   **`redis` (Redis 6)**: Used for caching and session management.
*   **`minio` (MinIO)**: S3-compatible object storage for handling file uploads locally.

## 🛠️ Technology Stack

*   **Backend**: Laravel 11, PHP 8.2
*   **Frontend**: Blade Templates, TailwindCSS v4, Vite
*   **Microservices**: Go (Golang) for image processing
*   **Database**: PostgreSQL 15
*   **Cache**: Redis
*   **Storage**: MinIO (S3 compatible)
*   **Infrastructure**: Docker, Docker Compose

## 📋 Prerequisites

Ensure you have the following installed on your system:

*   [Docker](https://docs.docker.com/get-docker/)
*   [Docker Compose](https://docs.docker.com/compose/install/)

## ⚡ Installation & Running

1.  **Clone the repository:**
    ```bash
    git clone https://github.com/NURJAKS/ads-platform.git
    cd ads-platform
    ```

2.  **Setup Environment Variables:**
    
    Copy the example `.env` file in the `laravel-app` directory:
    ```bash
    cp laravel-app/.env.example laravel-app/.env
    ```
    *Note: The default `.env.example` comes pre-configured for the Docker environment.*

3.  **Start the Application:**
    
    Run the following command to build and start all containers:
    ```bash
    docker-compose up -d --build
    ```

4.  **Install Dependencies & Setup Database:**
    
    Once the containers are running (check with `docker-compose ps`), install PHP dependencies and run migrations:
    ```bash
    # Install Composer dependencies
    docker-compose exec app composer install

    # Install NPM dependencies and build assets
    docker-compose exec app npm install
    docker-compose exec app npm run build

    # Run Database Migrations
    docker-compose exec app php artisan migrate

    # (Optional) Seed the database
    docker-compose exec app php artisan db:seed
    ```

5.  **Access the Application:**

    *   **Main App**: [http://localhost:8000](http://localhost:8000)
    *   **MinIO Console**: [http://localhost:9001](http://localhost:9001) (User: `minio`, Pass: `minio123`)

## 📂 Project Structure

```
├── laravel-app/       # Main Laravel Application
│   ├── app/           # Controllers, Models, etc.
│   ├── resources/     # Views (Blade) & Assets
│   └── routes/        # Web & API routes
├── go-image-service/  # Go Microservice for Images
├── docker-compose.yml # Docker Orchestration
├── infra/             # Infrastructure configurations
└── README.md          # Project Documentation
```

## 🧪 Running Tests

To run the Laravel test suite:

```bash
docker-compose exec app php artisan test
```
