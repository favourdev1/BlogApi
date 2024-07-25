### Blog API Documentation

#### Project Installation 

**Requires PHP 8.1 and above and Laravel 11**

1. **Clone the Repository:**

    ```bash
    git clone https://github.com/your-repository/blog-api.git
    cd blog-api
    ```

2. **Install Dependencies:**

    ```bash
    composer install
    ```

3. **Set Up Environment File:**

    Copy the `.env.example` file to `.env` and configure the environment variables.

    ```
    cp .env.example .env
    ```

4. **Generate Application Key:**

    ```
    php artisan key:generate
    ```


5. **Set Up Database:**

    Configure your database settings in the `.env` file.

6. **Run Migrations:**

    ```bash
    php artisan migrate
    ```

### Features

#### Authentication
- **Register:** `POST /api/register`
- **Login:** `POST /api/login`

#### Blog Management
- **Get All Blogs:** `GET /api/blogs`
- **Create a Blog:** `POST /api/blogs`
- **Get a Single Blog:** `GET /api/blogs/{id}`
- **Update a Blog:** `PUT /api/blogs/{id}`
- **Delete a Blog:** `DELETE /api/blogs/{id}`

#### Post Management
- **Get All Posts in a Blog:** `GET /api/blogs/{blogId}/posts`
- **Create a Post in a Blog:** `POST /api/blogs/{blogId}/posts`
- **Get a Single Post in a Blog:** `GET /api/blogs/{blogId}/posts/{id}`
- **Update a Post in a Blog:** `PUT /api/blogs/{blogId}/posts/{id}`
- **Delete a Post in a Blog:** `DELETE /api/blogs/{blogId}/posts/{id}`
- **Like a Post:** `POST /api/posts/{postId}/like`
- **Comment on a Post:** `POST /api/posts/{postId}/comment`




**Read more on the documentation**  
[Documentation Here](https://documenter.getpostman.com/view/33740282/2sA3kYheXb)
