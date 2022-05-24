DROP DATABASE IF EXISTS `mvcProject`;
CREATE DATABASE `mvcProject`;
USE `mvcProject`;

CREATE USER IF NOT EXISTS `mvcuser`@`localhost` IDENTIFIED BY "mvc1user!passwd";
GRANT SELECT, DELETE, INSERT, UPDATE ON `mvcProject`.* TO `mvcuser`@`localhost`;

CREATE TABLE `users`(
	`id` INT(3) UNSIGNED AUTO_INCREMENT,
	`username` VARCHAR(32) NOT NULL,
	`realName` VARCHAR(80),
	`password` CHAR(60) NOT NULL,
	`email` VARCHAR(320) NOT NULL,
	`telephone` VARCHAR(18),
	`sessionID` CHAR(128),
	`permissions` VARCHAR(6) NOT NULL DEFAULT '0',
	`wallet` DECIMAL(12,3) UNSIGNED NULL,
	UNIQUE(`username`, `email`, `telephone`, `sessionID`),
	PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `associations`(
	`id` INT(3) UNSIGNED AUTO_INCREMENT,
	`name` VARCHAR(64) NOT NULL,
	`nickname` VARCHAR(32) NOT NULL,
	`address` VARCHAR(255) NOT NULL,
	`telephone` VARCHAR(18) NOT NULL,
	`taxpayerNumber` INT(9) UNSIGNED NOT NULL,
	`quotaPrice` DECIMAL(12,3) UNSIGNED NOT NULL DEFAULT 5,
	`timeSpanToPayQuota` TINYTEXT NOT NULL DEFAULT 'P1M',
	`payQuotaAtEntering` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
	UNIQUE(`nickname`, `taxpayerNumber`),
	PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `usersAssociations`(
	`user` INT(3) UNSIGNED,
	`association` INT(3) UNSIGNED,
	`role` VARCHAR(6) NOT NULL DEFAULT '0',
	PRIMARY KEY(`user`, `association`),
	FOREIGN KEY(`user`) REFERENCES `users`(`id`)
		ON DELETE CASCADE
		ON UPDATE CASCADE,
	FOREIGN KEY(`association`) REFERENCES `associations`(`id`)
		ON DELETE CASCADE
		ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `news`(
	`id` INT(3) UNSIGNED AUTO_INCREMENT,
	`association` INT(3) UNSIGNED,
	`author` INT(3) UNSIGNED,
	`title` VARCHAR(80) NOT NULL,
	`requestedTilte` VARCHAR(80) NULL,
	`publishedTitle` VARCHAR(80) NULL,
	`image` VARCHAR(255) NOT NULL,
	`requestedImage` VARCHAR(255) NULL,
	`publishedImage` VARCHAR(255) NULL,
	`article` TEXT NOT NULL,
	`requestedArticle` TEXT NULL,
	`publishedArticle` TEXT NULL,
	`published` TINYINT(1) NOT NULL DEFAULT 0,
	`publishTime` DATETIME NULL,
	`lastEditTime` DATETIME NOT NULL DEFAULT '1001-01-01 00:00:00',
	PRIMARY KEY (`id`),
	FOREIGN KEY(`association`) REFERENCES `associations`(`id`)
		ON DELETE SET NULL
		ON UPDATE CASCADE,
	FOREIGN KEY(`author`) REFERENCES `users`(`id`)
		ON DELETE SET NULL
		ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `events`(
	`id` INT(3) UNSIGNED AUTO_INCREMENT,
	`title` VARCHAR(80) NOT NULL,
	`description` VARCHAR(280) NOT NULL,
	`association` INT(3) UNSIGNED NULL,
	`endDate` DATETIME NOT NULL DEFAULT '1001-01-01 00:00:00',
	PRIMARY KEY (`id`),
	FOREIGN KEY(`association`) REFERENCES `associations`(`id`)
		ON DELETE SET NULL
		ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `associationsEvents`(
	`event` INT(3) UNSIGNED,
	`association` INT(3) UNSIGNED,
	`isCreator` TINYINT(1) NOT NULL DEFAULT 0,
	PRIMARY KEY(`event`, `association`),
	FOREIGN KEY(`event`) REFERENCES `events`(`id`)
		ON DELETE CASCADE
		ON UPDATE CASCADE,
	FOREIGN KEY(`association`) REFERENCES `associations`(`id`)
		ON DELETE CASCADE
		ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `registrations`(
	`event` INT(3) UNSIGNED,
	`partner` INT(3) UNSIGNED,
	PRIMARY KEY(`event`, `partner`),
	FOREIGN KEY(`event`) REFERENCES `events`(`id`)
		ON DELETE CASCADE
		ON UPDATE CASCADE,
	FOREIGN KEY(`partner`) REFERENCES `users`(`id`)
		ON DELETE CASCADE
		ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `quotas`(
	`association` INT(3) UNSIGNED,
	`partner` INT(3) UNSIGNED,
	`price` DECIMAL(12,3) UNSIGNED NOT NULL,
	`payed` DECIMAL(12,3) UNSIGNED NOT NULL DEFAULT 0,
	`startDate` DATETIME NOT NULL DEFAULT '1001-01-01 00:00:00',
	`endDate` DATETIME NOT NULL DEFAULT '1001-01-01 00:00:00',
	PRIMARY KEY(`association`, `partner`),
	FOREIGN KEY(`association`) REFERENCES `associations`(`id`)
		ON DELETE CASCADE
		ON UPDATE CASCADE,
	FOREIGN KEY(`partner`) REFERENCES `users`(`id`)
		ON DELETE CASCADE
		ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `image`(
	`id` INT(3) UNSIGNED AUTO_INCREMENT,
	`association` INT(3) UNSIGNED NOT NULL,
	`title` VARCHAR(80) NOT NULL,
	`path` VARCHAR(255) NOT NULL,
	PRIMARY KEY(`id`),
	FOREIGN KEY(`association`) REFERENCES `associations`(`id`)
		ON DELETE CASCADE
		ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE INDEX `indexAssociationNickname`
ON `associations`(`nickname`);

CREATE VIEW `associationWPresident` AS
SELECT `associations`.*, `usersAssociations`.`user` AS `president`
FROM `associations` INNER JOIN `usersAssociations`
ON `associations`.`id` = `usersAssociations`.`association`
WHERE CAST(CONV(`usersAssociations`.`role`, 16, 10) AS INT) = (
	SELECT MAX(CAST(CONV(`role`, 16, 10) AS INT))
	FROM `usersAssociations`
);

-- SEE CHECK
-- SEE INDEX
-- SEE VIEWS
-- SEE UNION
-- SEE SUB-SELECTS AND EXISTS ALL ANY
-- LIMITS

-- MAX
-- MIN
-- COUNT
-- SUM
-- AVG
-- MAX

-- SQL Keywords

-- WHERE > GROUP BY > HAVING > ORDER BY

-- SELECT DISTINCT

-- AND, OR, NOT. BETWEEN, IN, IS, LIKE
