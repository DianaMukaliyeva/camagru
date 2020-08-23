# Camagru

Project at [Hive Helsinki](https://www.hive.fi/) coding school.

This is a small web application allowing you to make basic photo editing using your webcam and some predefined images. The goal of this project was to have practice with:

 * Create responsive layouts and page design
 * MVC architecture
 * Secure website (no SQL-, HTML injections, plain passwords in the databases)
 * Authorized languages:
    [Server] PHP
    [Client] HTML - CSS - JavaScript (only wiht browser native API)
 * MYSQL with PDO
 * Firefox and Chrome support

## Getting started

* You will need to have a local webserver on your machine, for example XAMPP.
* Make sure that you can send mails from terminal. You can configure POSTFIX.
* Clone this repo in a folder with a custom name to your server's virtual host.
* Change config/database.php file with your credentials.
* Launch config/setup.php to create database
* Start your server and open http://localhost/your_name_of_folder in the Chrome or Firefox.

## Functionality
* User features:
    * Register / Login (including activating account and reseting password through a unique link send by email)
    * User profile page
    * User data management: modify user data (username, email, password), delete and create images, set notification preferences.
    * User changing profile picture
* Gallery features:
    * All images are public and likeable and commentable by logged in users.
    * Once image is commented or liked the author is notified by email.
    * Infinite scroll gallery with pagination
* Editing features:
    * Create custom images using webcam or images downloaded from computer combined with filters

## Tech stack
* <strong>Frontend: HTML, CSS/Bootstrap 4, Javascript, AJAX</strong>
* <strong>Backend: PHP, MySQL</strong>

## Authors

[Diana Mukaliyeva](https://github.com/DianaMukaliyeva)
