
# COSCO

## Requirements

-   `php: ^8.1`
-   `mysql: ^7`

### Installation

#### First Steps

1. install dependency packages
    ```
    composer i
    composer dump-autoload
    ```

2. run below command for upload files
    ```
    php artisan storage:link
    ```

3. run command for migration database and run seeder
    ```
    php artisan migrate:fresh --seed
    ```
4. run below command to install passport for auth
    ```
    php artisan passport:install
    php artisan passport:client --personal
    ```
