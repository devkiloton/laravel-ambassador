## Laravel Ambassador (Monolith)
You can see most of the endpoints and its bodies down below:

[<img src="https://run.pstmn.io/button.svg" alt="Run In Postman" style="width: 128px; height: 32px;">](https://god.gw.postman.com/run-collection/16889380-bf1a7826-d54f-4e33-ae05-6d2385ebd87d?action=collection%2Ffork&source=rip_markdown&collection-url=entityId%3D16889380-bf1a7826-d54f-4e33-ae05-6d2385ebd87d%26entityType%3Dcollection%26workspaceId%3Db10972d8-d348-4dad-a75c-df1938ec825e)

Try to interact locally with the frontend:

[Admin](https://github.com/devkiloton/vue-admin) (don't forget to setup `VUE_APP_API_URL` in the `.env`)

[Checkout](https://github.com/devkiloton/nuxt-checkout) (use a `code` from any link in the db as a route param to make the checkout and set Stripe credentials in the config)

### Running the project
First things first, setup the environment variables, the `.env.example` is equal to the `.env` excepting the Stripe credentials that you must to create and the `MAIL_HOST` variable that you must to define based on you local machine (default is `docker.for.mac.localhost`).

Install Node.js deps:
```
npm i
```
Install PHP deps:
```
composer install
```
Install the mailhog [here](https://github.com/mailhog/MailHog) to run a mail server locally and then start it with:
```
mailhog
```
Create and start the containers:
```
docker-compose up
```
Access the backend terminal:
```
docker-compose exec backend sh
```
Inside the backend terminal, mock the db with the seeds:
```
php artisan db:seed
```
Now, leave the backend terminal and start the application:
```
php artisan serve
```
### Database diagram

```mermaid
erDiagram
    users {
        bigint id PK
        varchar first_name
        varchar last_name
        varchar email
        varchar password
        tinyint is_admin
        timestamp created_at
        timestamp updated_at
    }

    links {
        bigint id PK
        varchar code
        bigint user_id FK
        timestamp created_at
        timestamp updated_at
    }
    
    link_products {
        bigint id PK
        bigint link_id FK
        bigint product_id FK
    }

    products {
        bigint id PK
        varchar title
        varchar description
        varchar image
        decimal price
        timestamp created_at
        timestamp updated_at
    }
    
    orders {
        bigint id PK
        varchar transaction_id
        bigint user_id FK
        varchar code
        varchar customer_email
        varchar first_name
        varchar last_name
        varchar email
        varchar address
        varchar address2
        varchar city
        varchar country
        varchar zip
        tinyint complete
        timestamp created_at
        timestamp updated_at
    }

    order_items {
        bigint id PK
        bigint order_id FK
        varchar product_title
        decimal price
        int quantity
        decimal admin_revenue
        decimal customer_revenue
        timestamp created_at
        timestamp updated_at
    }
    
    personal_access_tokens {
        bigint id PK
        varchar tokenable_type
        bigint tokenable_id
        varchar name
        varchar token
        text abilities
        timestamp last_used_at
        timestamp created_at
        timestamp updated_at
    }

    migrations {
        int id PK
        varchar migration
        int batch
    }

    orders }o--|| order_items : order_id
    link_products }|--o{ products : product_id
    link_products }|--o{ links: link_id
    users }o--|| links : user_id
    users }o--|| orders : user_id
```
