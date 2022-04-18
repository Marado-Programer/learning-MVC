DROP DATABASE IF EXISTS `mvcProject`;
CREATE DATABASE `mvcProject`;
USE `mvcProject`;

CREATE USER IF NOT EXISTS `mvcuser`@`localhost` IDENTIFIED BY "mvc1user!passwd";
GRANT ALL ON `mvcProject`.* TO `mvcuser`@`localhost`;

CREATE TABLE `users`(
	`id` INT(3) UNSIGNED AUTO_INCREMENT,
	`username` VARCHAR(32) NOT NULL,
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
	`nickname` VARCHAR(32) NOT NULL,
	`address` VARCHAR(255) NOT NULL,
	`telephone` VARCHAR(15),
	`taxpayerNumber` INT(9) UNSIGNED NOT NULL,
	`president` INT(3) UNSIGNED NOT NULL,
	UNIQUE(`name`, `nickname`, `taxpayerNumber`),
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

CREATE TABLE `news`(
	`id` INT(3) UNSIGNED AUTO_INCREMENT,
	`association` INT(3) UNSIGNED NOT NULL,
	`author` INT(3) UNSIGNED NOT NULL,
	`title` VARCHAR(80) NOT NULL,
	`image` VARCHAR(255) NOT NULL,
	`article` TEXT NOT NULL,
	`publishTime` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	`lastEditTime` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	PRIMARY KEY (`id`),
	FOREIGN KEY(`association`) REFERENCES `associations`(`id`),
	FOREIGN KEY(`author`) REFERENCES `users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `events`(
	`id` INT(3) UNSIGNED AUTO_INCREMENT,
	`title` VARCHAR(80) NOT NULL,
	`description` VARCHAR(280) NOT NULL,
	`association` INT(3) UNSIGNED NOT NULL,
	`endDate` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	PRIMARY KEY (`id`),
	FOREIGN KEY(`association`) REFERENCES `associations`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `associationsEvents`(
	`eventID` INT(3) UNSIGNED,
	`associationID` INT(3) UNSIGNED,
	`isCreator` BOOL NOT NULL DEFAULT 0,
	PRIMARY KEY(`eventID`, `associationID`),
	FOREIGN KEY(`eventID`) REFERENCES `events`(`id`),
	FOREIGN KEY(`associationID`) REFERENCES `associations`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `registrations`(
	`eventID` INT(3) UNSIGNED,
	`partnerID` INT(3) UNSIGNED,
	PRIMARY KEY(`eventID`, `partnerID`),
	FOREIGN KEY(`eventID`) REFERENCES `events`(`id`),
	FOREIGN KEY(`partnerID`) REFERENCES `users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `projects`(
	`id` INT(11) UNSIGNED AUTO_INCREMENT,
	`description` VARCHAR(150),
	`executionDate` VARCHAR(10),
	`hyperlink` VARCHAR(200) NOT NULL,
	`image` VARCHAR(200),
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

