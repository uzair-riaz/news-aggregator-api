About
-----
A news aggregator service that pulls articles from various sources and provides endpoints for a frontend application to consume.

Prerequisites
-----

Have Docker installed and running on your machine.

See the [Docker website](http://www.docker.io/gettingstarted/#h_installation) for installation instructions.

Build
-----

Steps to build the project with Docker:

1. Clone this repo

        git clone https://github.com/uzair-riaz/news-aggregator-api.git

2. Build and run the project (This will take a few minutes.)

        docker compose up -d

3. Once everything has started up, you should be able to access the api via [http://localhost:8000/](http://localhost:8000/) on your host machine.

        http://localhost:8000/api

4. Access OpenAPI docs by navigating to Swagger UI

        http://localhost:8000/api/documentation

    These docs are also hosted in Swagger Hub which can be accessed here: [OpenAPI Docs](https://app.swaggerhub.com/apis/UZAIRRFAROOQUI/news-aggregator_api/1.0.0)


5. Run test suite using container shell

        docker exec -it news_aggregator_api sh
        php artisan test
 

Technical Details
-----
This project uses docker to spin up three service containers:

- `mysql`: MySQL server that acts as the local database for the project. Project is DB agnostic, however.
- `api`: Laravel application container that handles authentication tokens and exposes other APIs to the frontend module.
- `scheduler`: Same as `api` but this one focuses solely on running the scheduled tasks. It's entrypoint is set in a way that it executes the artisan command `schedule:run` every 60 seconds. `aggregate:news` is the artisan command that is currently scheduled to run every thirty minutes.
