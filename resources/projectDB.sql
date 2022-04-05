DROP DATABASE IF EXISTS `mvcProject`;
CREATE DATABASE `mvcProject`;
USE `mvcProject`;

CREATE USER IF NOT EXISTS `mvcuser`@`localhost` IDENTIFIED BY "mvc1user!passwd";
GRANT ALL ON `mvcProject`.* TO `mvcuser`@`localhost`;

CREATE TABLE `users`(
	`id` INT(11) UNSIGNED AUTO_INCREMENT,
	`username` VARCHAR(255) NOT NULL,
	`password` VARCHAR(255) NOT NULL,
	`realName` VARCHAR(255),
	`sessionId` VARCHAR(255),
	`permissions` LONGTEXT,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `projects`(
	`id` INT(11) UNSIGNED AUTO_INCREMENT,
	`description` VARCHAR(150),
	`executionDate` VARCHAR(10),
	`hyperlink` VARCHAR(200) NOT NULL,
	`image` VARCHAR(200),
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `news`(
	`id` INT(11) UNSIGNED AUTO_INCREMENT,
	`publishTime` DATETIME DEFAULT '0000-00-00 00:00:00',
	`author` INT(11) UNSIGNED NOT NULL,
	`title` VARCHAR(255) NOT NULL,
	`text` TEXT NOT NULL,
	`image` VARCHAR(255),
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
