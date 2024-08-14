## Getting Started

1. If not already done, [install Docker Compose](https://docs.docker.com/compose/install/) (v2.10+)
2. Run `docker compose build --no-cache` to build fresh images
3. Run `docker compose up -d --wait` to start the docker
4. Open `https://localhost` in your favorite web browser and [accept the auto-generated TLS certificate](https://stackoverflow.com/a/15076602/1352334)
5. Run `docker compose down --remove-orphans` to stop the Docker containers.

## Prepare the database

1. open bash in docker using `docker compose run php bash`
2. run `php bin/console doctrine:migrations:migrate`

Command above will run all migrations. Database structure was changed during development and all the changes are handled using migrations.

## Install fixtures

1. open bash in docker using `docker compose run php bash`
2. run `php bin/console doctrine:fixtures:load`
3. give it a moment to stop running

About the fixtures: the goal was to have 1mil records in the database. I've decided to go with doctrine/doctrine-fixtures-bundle`
With 2gb memory limit I'm unable to get all the records. Way to go would be to load fixtures in parts,
or simply create a sql dump. I've run out of time while working on this part and decided to ship it as it is right now.

## Exceptions and form handling

As with fixtures: I've allocated 6 hours to the project and I've run out of time to handle exceptions.
Right now the only place with ok form handling is email address in registration form. It can;t be duplicated.
Other fields would be handled in similar fashion (ie. isbn+userId should be a unique key to avoid adding the same book by the same user)

## Book controller

I'm keeping all the logic there as it's just small enough to keep it there. If I were to add anything more I'd move the logic to a service.

## Tests
I'm really light on logic here and I mostly use existing packages. Add time constrains on top of this.
