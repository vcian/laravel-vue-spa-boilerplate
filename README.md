## Laravel-Vue-Spa-Boilerplate

## Installation

## Using install shell script

#### For Linux users:

 If you got this error /usr/bin/env: ‘bash\r’: No such file or directory"
 
 Please run `sed $'s/\r$//' ./install.sh > ./install.Unix.sh` and use ./install.Unix.sh to install

 If you got this error "bash: ./install.sh: Permission denied"
 
 Please run `sudo chmod 777 -R install.sh` to make sure this file has permission to execute

#### Install step
 <ul>
 	<li>./install.sh</li>
 </ul>

### Manual installation

* Import sample database from `database/dump/laravel_vuew_spa_boilerplate.sql`
  - Default Admin URL `/login`
  - Default email and password is `admin@admin.com` - `123456`
* Create `.env` file from `.env-example` and update your configuration

### Create Table Migration Pattern

<ul>
	<li>Please read proper document to create table with proper naming convention e.g.<code>php artisan make:migration create_users_table --create=users</code>, try to use plural form.</li>
	<li>Please use proper data type and data length,click here to read document [Here](https://laravel.com/docs/5.5/migrations)</li>
</ul>