# Borrowing Books (API) - Documentation

# Description

This is a simple API for borrowing and returning books. It is built with Laravel.

## Requirements

-   PHP 8.2
-   MySQL 8.0
-   Composer
-   Redis (predis or phpredis)

## Improvements to be made

-   Add authentication (JWT Authentication with custom middleware)
-   Add Authorization (Roles based authorization with JWT Authentication)
-   Add RequestID Middleware with custom middleware
-   Improve Logger for monitoring and debugging (add request id for labeling logs)
-   Add Redis for caching and implement invalidate mechanism

## List Features

-   List all books or categories
-   Add a new book or category
-   Update a book or category
-   Delete a book or category
-   Borrow a book
-   Return a book
-   List all borrowed books

## Documentation

you can import postman collection from `.Borrowing Books.postman_collection.json`

## Setup Instructions

-   Create `.env` file
    for local development, create a `.env` file from the `.env.example` file.

    ```bash
    cp .env.example .env
    ```

-   Install dependencies using Composer

    ```bash
    composer install
    ```

-   Generate an application key

    ```bash
    php artisan key:generate
    ```

-   Run database migrations to create the database schema

    ```bash
    php artisan migrate
    ```

-   Run database seeds to populate the database with initial data. in this app we have seeders for categories, books, and users for testing purpose.

    ```bash
    php artisan db:seed
    ```

-   Run the server to start the application
    ```bash
    php artisan serve
    ```

## Username and Password for testing

Admin (role: admin) :

-   username: admin@test.com
-   password: 12345678

User (role: user) :

-   username: user@test.com
-   password: 12345678
