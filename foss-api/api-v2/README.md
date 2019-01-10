EgyptFOSS API
====

Free and open source software platform (API)

***************************************************************************
## Installation instructions:
### 1. Make Sure you have PHP 5.5+
To check that run
  > php5 -v

### 2. Install Composer
  > `curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer`

Run 
  > composer install

### 3. Install Required Libraries (Slim & Swagger)

Run 
  > cd path-to-project
  > composer update
This will generate a vendor folder with the libraries specified in composer.json

### 4. Generate Swagger Json File

Run 
  > vendor/zircote/swagger-php/bin/swagger ./file-to-generate-tests-for.php -o ./swagger/output-file.json
