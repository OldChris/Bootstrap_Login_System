# Bootstrap_Login_System

## What is Bootstrap_Login_System?
A website build using PHP and Bootstrap 5 that allows users to signup and login.  
This project is based on the example provided by CodingNepal  
[CodingNepal on Youtube](https://youtu.be/IpznmyXApms)  
[CodingNepal website with source]( https://www.codingnepalweb.com/2020/09/login-signup-form-with-email-verification-php-mysql-bootstrap.html)  

As you can see I made some corrections and added some extra features.

## How does it work?
It supports (amongst many other features):
- login and signup
- password reset (forgot password)
- password lifetime
- session timeout on inactivity
- a menu system that is file driven per user role

The website is responsive 

An example website can be found at https://pollux.pa7rhm.nl

[Screen shot Login / Signup](docs/loginregister.png)
[Screen shot Signup](docs/signup.png)
[Screen shot Logged in](docs/loggedin.png)

For more information see [User Manual](docs/Bootstrap_Logon_System.pdf)

[Bootstrap CSS and JS files can be downloaded from](https://getbootstrap.com/docs/5.0/getting-started/download/)
# Docker files
Included are Docker files that I used for this project
3 containers were used : 
- apache:php
- mysql
- phpmyadmin

Database passwords for production are in a text file outside the website's documentroot.
Shortcuts for start, stop and status of containers are provided.
Shortscuts for starting the app and phpmyadmin in the browser are also provided.

## Who will use it?
If you want to create a responsive website with logon option this can be a good start point.
Knowledge of PHP, Javascript, Bootstrap and Docker is required

## Goal , next steps
Improve code, enhance functionality

Any suggestions are welcome.