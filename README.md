# App demo application

## Preparing the environment

### 1. Docker

1. Install [Docker](https://docs.docker.com/engine/installation/)
2. Start all the containers running `docker-compose -p fittracker up -d`
3. Login into the PHP container running `docker exec -it $(docker ps -q --filter=name=fittracker_php_1)  bash`


### 2. PHP

We have to run composer into the container to get all php dependencies:
    
    php composer.phar install

### 3. Creating the database

    php app/console doctrine:schema:update --force
    
### 4. Dump static assets

    php app/console assets:install
    php app/console assetic:dump
    
### 5. Loading sample data
    
    php app/console h4cc_alice_fixtures:load:files src/AppBundle/Resources/fixtures/data.yml
    
Sample data credentials are: 
- **Admin** user: email: `admin@example.com`, password: `password`
- **Standard** user: email: `usrer1@example.com`, password: `password`
    
## Running tests

In order to run tests, the database has to be empty.

To remove the database run from the mysql container, running from the **host** machine the command:

    echo "DELETE FROM user;" | docker exec -i $(docker ps -q --filter=name=fittracker_db) mysql -ufittracker -pfittracker fittracker

### Running PHP tests

    php bin/phpunit

## Demo

Go to:
 - http://localhost:8080/app_dev.php for the development mode
 - http://localhost:8080/ for the production mode
 
## API

Login as ADMIN and go to:

 - http://localhost:8080/app_dev.php/api/doc for the development mode
 - http://localhost:8080/api/doc for the production mode 


        

