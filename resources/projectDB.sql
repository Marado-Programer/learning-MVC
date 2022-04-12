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
	`sessionID` VARCHAR(128),
	`permissions` INT(11) UNSIGNED NOT NULL DEFAULT 0,
	UNIQUE(`username`, `email`, `telephone`, `sessionID`),
	PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `associations`(
	`id` INT(3) UNSIGNED AUTO_INCREMENT,
	`name` VARCHAR(64) NOT NULL,
	`address` VARCHAR(255) NOT NULL,
	`telephone` VARCHAR(15),
	`taxpayerNumber` INT(9) UNSIGNED NOT NULL,
	`president` INT(3) UNSIGNED NOT NULL,
	UNIQUE(`name`, `taxpayerNumber`),
	PRIMARY KEY(`id`),
	FOREIGN KEY(`president`) REFERENCES `users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `usersAssociations`(
	`userID` INT(3) UNSIGNED,
	`associationID` INT(3) UNSIGNED,
	`role` INT(6) UNSIGNED NOT NULL DEFAULT 0,
	PRIMARY KEY(`userID`, `associationID`),
	FOREIGN KEY(`userID`) REFERENCES `users`(`id`),
	FOREIGN KEY(`associationID`) REFERENCES `associations`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `associationRoles`(
	`associationID` INT(3) UNSIGNED,
	`name` VARCHAR(64) NOT NULL,
	PRIMARY KEY(`associationID`, `name`),
	FOREIGN KEY(`associationID`) REFERENCES `associations`(`id`)
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
