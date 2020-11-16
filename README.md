# Camagru


This is a small web application allowing you to make basic photo editing using your webcam and some predefined images.

Have a look at the example [website](https://camagru-hive.herokuapp.com/). (You can sing in with credentials: email `example@gmail.com` password `Example1`)

## Goal

The goal of this project was to have practice with:

 * Create responsive layouts and page design
 * MVC architecture
 * Secure website (no SQL-, HTML injections, plain passwords in the databases)
 * Authorized languages:
    [Server] PHP
    [Client] HTML - CSS - JavaScript (only wiht browser native API)
 * MYSQL with PDO
 * Firefox and Chrome support

## Tech stack

* Frontend:
    * HTML
    * CSS/Bootstrap 4
    * Javascript
    * AJAX
* Backend:
    * PHP
    * MySQL

## ✅ Functionality

<details>
  <summary>User features</summary>
  <br>

  * Register / Login (including activating account and  reseting password through a unique link send by email).
  * User profile page.
  * User data management: modify user data (username, email,  password), delete and create images, set notification  preferences.
  * User changing profile picture.
  * Users can follow each other.
</details>

<details>
  <summary>Gallery features</summary>
  <br>

  * All images are public and likeable and commentable by logged in users.
  * Once image is commented or liked the author is notifiedby email.
  * Images can be sorted by creating date and popularity.
  * Infinite scroll gallery with pagination.
  * You can create images with tags.
</details>

<details>
  <summary>Editing features</summary>
  <br>

  * Create custom images using webcam or images downloaded from computer combined with filters.
</details>

<details>
  <summary>Other features</summary>
  <br>

  * Instant search in the navigation. You can search user by name or tags by #tag.
</details>

## 🚀 Getting started

* You will need to have a local webserver on your machine, for example XAMPP.
* Make sure that you can send mails from terminal. You can configure POSTFIX.
* Clone this repo in a folder with a custom name to your server's virtual host.
* Change config/database.php file with your credentials.
* Start your server and open http://localhost/your_name_of_folder in the Chrome or Firefox.

## 📸 Webapp showcase

 * ### Gallery main view<br>
![Gallery](../images/gallery.png?raw=true)
 * ### Profile view<br>
![Profile](../images/profile.png?raw=true)
 * ### Image view<br>
![Photo](../images/viewImage.png?raw=true)
 * ### Add photo view<br>
![Photomaker](../images/photomaker.png?raw=true)
 * ### Profile settings view<br>
![Profile settings](../images/profile-settings.png?raw=true)

## ✌ Found a bug? Missing a specific feature?

Feel free to file a new issue with a respective title and description on the repository.

## 🙋‍♀️ Authors

[Diana Mukaliyeva](https://github.com/DianaMukaliyeva)

## 📘License

MIT

[![Visits Badge](https://badges.pufler.dev/visits/DianaMukaliyeva/camagru)](https://badges.pufler.dev)
