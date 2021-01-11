

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+01:00";


CREATE TABLE `users` (
 `id` int(11) AUTO_INCREMENT,
 `customerid` int(11) NOT NULL DEFAULT '0',
 `name` varchar(255),
 `email` varchar(255),
 `role` varchar(255),
 `password` varchar(255),
 `code` mediumint(50),
 `code_time` int(11),
 `status` text,
 `dt_created` DATETIME NOT NULL DEFAULT LOCALTIMESTAMP,
 `dt_modified` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, 
 `dt_pwchanged` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, 
 `dt_lastlogin` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, 
 PRIMARY KEY (`id`)
 
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
INSERT INTO `users` (`id`, `customerid`, `name`, `email`, `role`, `password`, `code`, `code_time`, `status`) VALUES
(1, '0', 'Chris The Wizz', 'pa7rhm@gmail.com', 'user', '$2y$10$NYwxrTyUivdoK/i16uojguXSUzH2s/H.jvm4Pyrt4N3REB2fZWf12', '0', '0', 'verified');


CREATE TABLE `users_log` (
  `id` int(6) NOT NULL AUTO_INCREMENT COMMENT 'A unique identifier for each record',
  `logdatetime` datetime NOT NULL,
  `ipaddress` varchar(40) NOT NULL COMMENT ' ip address of station or user logging in to website (for IPV6 length = 8*4 + 7)',
  `hostname` varchar(64) NOT NULL,
  `username` varchar(100) NOT NULL,
  `status` varchar(80) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


INSERT INTO `users_log` (`id`, `logdatetime`, `ipaddress`, `hostname`, `username`, `status`) VALUES
(1, '2017-09-29 19:57:20', '85.148.100.4', 's55946404.adsl.online.nl', 'admin', 'logged out'),
(2, '2017-09-29 19:57:29', '85.148.100.4', 's55946404.adsl.online.nl', 'admin', 'login successfull'),
(3, '2017-09-30 04:08:39', '85.148.100.4', 's55946404.adsl.online.nl', 'admin', 'login successfull'),
(4, '2017-09-30 11:29:55', '85.148.100.4', 's55946404.adsl.online.nl', 'admin', 'login failure'),
(5, '2017-09-30 11:30:08', '85.148.100.4', 's55946404.adsl.online.nl', 'admin', 'login successfull'),
(6, '2017-10-01 04:54:14', '85.148.100.4', 's55946404.adsl.online.nl', 'admin', 'login successfull'),
(7, '2017-10-01 05:17:51', '85.148.100.4', 's55946404.adsl.online.nl', 'admin', 'logged out');

