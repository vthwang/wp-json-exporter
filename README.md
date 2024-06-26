# WordPress JSON Exporter

Export JSON API for Next.js blog, with support for posts and projects.

## Features

- Export posts and projects as custom JSON API.
- Using WordPress as a Headless CMS.
- Redirect WordPress front-end to a desired URL.

## Usage

1. Install and activate the plugin.
2. Go to Sidebar -> `JSON Exporter` and configure the plugin.
    - Is Redirect: Checked if you want to redirect WordPress front-end to a desired URL.
    - Redirect URL: The URL to redirect to.

## Supporting APIs

### Base URL

`[YOUR_WORDPRESS_SITE_URL]/wp-json/wp-json-exporter/v1`

### Endpoints

1. Get all posts
   - Method: GET
   - Description: Retrieves a list of all posts.
   - Sample Request: [Base URL]/posts
   - Response Example

    ```json
    {
        "data": [
            {
                "title": "Hello World",
                "slug": "hello-world",
                "featured_image": "http://localhost:8000/wp-content/uploads/2024/01/hello-world.jpg",
                "category": [
                    "Uncategorized"
                ],
                "date": "2024-01-01"
            }
        ],
        "meta": {
            "current_page": 1,
            "total_pages": 1,
            "total_posts": 1
        }
    }
    ```

2. Get a single post
   - Method: GET
   - Description: Retrieves a single post based on its slug.
   - URL Parameters:
     - slug (string) - The unique identifier for the post.
   - Sample Request: [Base URL]/posts/{slug}
   - Response Example

    ```json
    {
        "data": {
            "title": "Hello World",
            "slug": "hello-world",
            "featured_image": "http://localhost:8000/wp-content/uploads/2024/01/hello-world.jpg",
            "category": [
                "Uncategorized"
            ],
            "date": "2024-01-01",
            "last_modified": "2024-01-01",
            "content": "\n<h1 class=\"wp-block-heading\">Hello World</h1>",
            "tags": [],
            "visits": 1234
        },
        "prev": {
            "title": "Hello World 0",
            "slug": "hello-world-0",
            "featured_image": "http://localhost:8000/wp-content/uploads/2023/12/hello-world-0.jpg",
            "category": [
                "Uncategorized"
            ],
            "date": "2023-12-31"
        },
        "next": {
            "title": "Hello World 1",
            "slug": "hello-world-1",
            "featured_image": "http://localhost:8000/wp-content/uploads/2024/01/hello-world-1.jpg",
            "category": [
                "Uncategorized"
            ],
            "date": "2024-01-02"
        }
    }
    ```

3. Get all projects
   - Method: GET
   - Description: Retrieves a list of all projects.
   - Sample Request: [Base URL]/projects
   - Response Example

    ```json
    {
        "data": [
            {
                "title": "Hello World",
                "slug": "hello-world",
                "featured_image": "http://localhost:8000/wp-content/uploads/2024/01/hello-world.jpg",
                "category": [
                    "Uncategorized"
                ],
                "date": "2024-01-01",
                "meta": {
                    "color": "#ffffff",
                    "product_owner": "[Vincent Wang](https://vthwang.com/)",
                    "website": "https://vthwang.com/",
                    "tech_stack": "- WordPress",
                    "my_role": "- Product Engineer"
                }
            }
        ],
        "meta": {
            "current_page": 1,
            "total_pages": 1,
            "total_posts": 1
        }
    }
    ```

4. Get a single project
   - Method: GET
   - Description: Retrieves a single project based on its slug.
   - URL Parameters:
      - slug (string) - The unique identifier for the project.
   - Sample Request: [Base URL]/projects/{slug}
   - Response Example

    ```json
    {
        "data": {
            "title": "Hello World",
            "slug": "hello-world",
            "featured_image": "http://localhost:8000/wp-content/uploads/2024/01/hello-world.jpg",
            "category": [
                "Uncategorized"
            ],
            "date": "2024-01-01",
            "last_modified": "2024-01-10",
            "content": "\n<h1 class=\"wp-block-heading\">Hello World</h1>",
            "tags": [],
            "meta": {
                "color": "#ffffff",
                "product_owner": "[Vincent Wang](https://vthwang.com/)",
                "website": "https://vthwang.com/",
                "tech_stack": "- WordPress",
                "my_role": "- Product Engineer"
            },
            "visits": 1234
        },
        "next": {
            "title": "Hello World 1",
            "slug": "hello-world-1",
            "featured_image": "http://localhost:8000/wp-content/uploads/2024/01/hello-world-1.jpg",
            "category": [
                "Uncategorized"
            ],
            "date": "2024-01-02"
        }
    }
    ```

5. Get all visits
   - Method: GET
   - Description: Retrieves the total number of visits across all routes.
   - Sample Requests:
     - To retrieve the total visits for all routes: [Base URL]/visits.
     - To retrieve visits for a specific route: [Base URL]/visits?route=/example-route.
   - Response Example

    ```json
    {
        "data": 1234
    }
    ```

6. Update visits
   - Method: POST
   - Description: Increments the visit count for a specified route. If the route does not exist, it creates a new entry for it.
   - **Note**: For posts and projects, the route should be formatted as /posts/[slug] or /projects/[slug] where [slug] is the unique identifier of the post or project. After utilizing this formatted route, you can access the 'visits' field when retrieving a single post or project.
   - Sample Request: [Base URL]/visits
   - Parameters:
     - route (string, required): The route to update the visit count for.
   - Response Example

    ```json
    {
        "route": "/example-route"
    }
    ```

   - Success Response Example

    ```json
    {
        "route": "/example-route",
        "count": 1
    }
    ```

   - Error Response Example

    ```json
    {
        "code": "missing_route",
        "message": "No `route` provided",
        "data": {
            "status": 400
        }
    }
    ```
