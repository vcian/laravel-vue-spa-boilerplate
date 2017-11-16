## Laravel-Vue-Spa-Boilerplate

# Getting started

## Introduction
* This is a Laravel (5.5) API Boilerplate Projcet with JWT Authentication.

## Installation

Please check the official laravel installation guide for server requirements before you start. [Official Documentation](https://laravel.com/docs/5.4/installation#installation)


Clone the repository

    git clone https://github.com/viitoradmin/laravel-vue-spa-boilerplate.git

Switch to the repo folder

    cd laravel-vue-spa-boilerplate

Install all the dependencies using composer

    composer install

Copy the example env file and make the required configuration changes in the .env file

    cp .env.example .env

Generate a new application key

    php artisan key:generate

Generate a new JWT authentication secret key

    php artisan jwt:generate

Run the database migrations (**Set the database connection in .env before migrating**)

    php artisan migrate

Start the local development server

    php artisan serve

You can now access the server at http://localhost:8000

**TL;DR command list**

    git clone https://github.com/viitoradmin/laravel-vue-spa-boilerplate.git
    cd laravel-vue-spa-boilerplate
    composer install
    cp .env.example .env
    php artisan key:generate
    php artisan jwt:generate

**Make sure you set the correct database connection information before running the migrations** [Environment variables](#environment-variables)

    php artisan migrate
    php artisan serve

## Environment variables

- `.env` - Environment variables can be set in this file

***Note*** : You can quickly set the database information and other variables in this file and have the application fully working.

# Authentication

This applications uses JSON Web Token (JWT) to handle authentication. The token is passed with each request using the `Authorization` header with `Token` scheme. The JWT authentication middleware handles the validation and authentication of the token. Please check the following sources to learn more about JWT.

- https://jwt.io/introduction/
- https://self-issued.info/docs/draft-ietf-oauth-json-web-token.html

## Issues

If you come across any issues please report them [here](https://github.com/viitoradmin/laravel-vue-spa-boilerplate/issues).

## Contributing
Feel free to create any pull requests for the project. For propsing any new changes or features you want to add to the project, you can send us an email at vishal@viitorcloud.com or ruchit.patel@viitorcloud.com

## License

[MIT LICENSE](https://github.com/viitoradmin/laravel-vue-spa-boilerplate/blob/master/LICENSE.txt)

