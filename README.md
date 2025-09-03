# Documentation

This is the documentation of our service. Here you will find information about the available endpoints,
how to use them, and what to expect in the responses.

## Postman endpoints

You wil find the postman endpoints at /postman/[postman_file_name]
On postman app click import and select or drag and drop the previous file.

## Docker configuration

Run on your bash after cloning the repository:

```
    cd [repo_name]
    docker compose up -d --build
    docker compose exec app composer install
    docker compose exec app php bin/console doctrine:migrations:migrate
    docker compose exec app php bin/console doctrine:fixtures:load
    docker compose exec app php ./vendor/bin/phpunit tests (to run all tests)
```

## Available Endpoints

### 1. [endpoint title]

GET /[endpoint]

#### Description

[endpoint description]

#### Query parameters

- None

#### Successful response

```json
{
    "message": "The entered balance 0.10 has been returned successfully.",
    "result": "Coins returned: 0.10"
}

```# mytheresa
