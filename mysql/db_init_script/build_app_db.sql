
CREATE DATABASE IF NOT EXISTS `app_db`;
USE `app_db`;

CREATE USER 'app_user'@'%' IDENTIFIED BY 'app-password';
GRANT ALL PRIVILEGES ON app_db.* TO 'app_user'@'%' WITH GRANT OPTION;

CREATE TABLE `fruit` (
 `id` int(11) AUTO_INCREMENT,
 `name` varchar(255),
 `price` varchar(255),
 PRIMARY KEY (`id`)
 ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
 
INSERT INTO `fruit` ( `name`, `price`) VALUES
('Red Apple', '0.25 Euro'),
('Big Apple', '0.25 Euro'),
('Green Apple', '0.25 Euro'),
('ApplePie Apple', '0.25 Euro'),
('Orange', '1 Euro'),
('Peach', '1 Euro'),
('Pear', '1 Euro');

