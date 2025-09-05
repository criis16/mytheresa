# Documentation

This is the documentation of our service. Here you will find information about the available endpoints,
how to use them, and what to expect in the responses.

## Postman endpoints

You wil find the postman endpoints at /postman/Mytheresa.postman_collection.json
On postman app click import and select or drag and drop the previous file.

## Docker configuration

Run on your bash after cloning the repository:

```
    cd mytheresa
    docker compose up -d --build
    docker compose exec app composer install
    docker compose exec app php bin/console doctrine:migrations:migrate
    docker compose exec app php bin/console doctrine:fixtures:load
    docker compose exec app php ./vendor/bin/phpunit tests (to run all tests)
```

## Available Endpoints

### 1. Get products

GET /products

#### Description

This endpoint returns products of the database.
It can be filtered by category, price less than, or both.
It can also be paginated.
In case of an error it will return a 400 status code.

#### Query parameters

- page: the page number to return
- limit: the number of products to return per page
- category: the category of the products to return
- priceLessThan: the price less than of the products to return

#### Successful response

```json
{
    "message": "Products retrieved successfully",
    "result": [
        {
            "sku": "000001",
            "name": "BV Lean leather ankle boots",
            "category": "boots",
            "price": {
                "original": 89000,
                "final": 62300,
                "discount_percentage": "30%",
                "currency": "EUR"
            }
        }
    ],
    "pagination": {
        "currentPage": 1,
        "perPage": 5,
        "totalPages": 1,
        "totalProducts": 5
    }
}

```
