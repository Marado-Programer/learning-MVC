DROP DATABASE IF EXISTS `mvcProject`;
CREATE DATABASE `mvcProject`;
USE `mvcProject`;

CREATE USER IF NOT EXISTS `mvcuser`@`localhost` IDENTIFIED BY "mvc1user!passwd";
GRANT ALL ON `mvcProject`.* TO `mvcuser`@`localhost`;

CREATE TABLE `users`(
	`id` INT(3) UNSIGNED AUTO_INCREMENT,
	`username` VARCHAR(64) NOT NULL,
	`realName` VARCHAR(80),
	`password` VARCHAR(60) NOT NULL,
	`email` VARCHAR(320) NOT NULL,
	`telephone` VARCHAR(15),
	`sessionId` VARCHAR(128),
	`permissions` INT(11) UNSIGNED NOT NULL DEFAULT 0,
	UNIQUE(`username`, `email`, `telephone`),
	PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `associations`(
	`id` INT(3) UNSIGNED AUTO_INCREMENT,
	`name` VARCHAR(64) NOT NULL,
	`address` VARCHAR(255) NOT NULL,
	`telephone` VARCHAR(15),
	`taxpayerNumber` INT(9) UNSIGNED,
	`president` INT(3) UNSIGNED NOT NULL,
	UNIQUE(`name`),
	PRIMARY KEY(`id`),
	FOREIGN KEY(`president`) REFERENCES `users`(`id`)
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
