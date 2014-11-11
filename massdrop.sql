DROP TABLE IF EXISTS `set_items`;
DROP TABLE IF EXISTS `items`;
DROP TABLE IF EXISTS `sets`;
DROP TABLE IF EXISTS `users`;

CREATE TABLE IF NOT EXISTS `users`(
	`user_id` INT NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(255),
	`email` VARCHAR(255) NOT NULL,
	`password` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`user_id`),
	CONSTRAINT `uc_name` UNIQUE (`name`));

# INSERT INTO `users` (`user_id`, `name`, `email`, `password`) VALUES (1, 'Wade', 'a@aa.com', 'a1!');
# INSERT INTO `users` (`user_id`, `name`, `email`, `password`) VALUES (2, 'Ryan', 'b@bb.com', 'b2@');
# INSERT INTO `users` (`user_id`, `name`, `email`, `password`) VALUES (3, 'Mark', 'c@cc.com', 'c3#');

CREATE TABLE IF NOT EXISTS `sets`(
	`set_id` INT NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(255) NOT NULL,
	`creator` INT NOT NULL,
	`description` TEXT NOT NULL,
	`image_url` VARCHAR(255),
	`created_date` DATETIME NOT NULL,
	`last_updated_date` DATETIME NOT NULL,
	PRIMARY KEY(`set_id`),
	FOREIGN KEY(`creator`) REFERENCES `users`(`user_id`) ON DELETE CASCADE ON UPDATE CASCADE);

CREATE TRIGGER `set_created_date` BEFORE INSERT ON `sets` FOR EACH ROW SET NEW.`created_date` = NOW(), NEW.`last_updated_date` = NOW();
CREATE TRIGGER `set_last_updated_date` BEFORE UPDATE ON `sets` FOR EACH ROW SET NEW.`created_date` = OLD.`created_date`, NEW.`last_updated_date` = NOW();

# INSERT INTO `sets` (`set_id`, `name`, `creator`, `description`, `image_url`) VALUES (1, 'My Portable Audio Setup', 1, 'This is how I listen to awesome beats when I\'m out.', 'https://d2qmzng4l690lq.cloudfront.net/resizer/450x450/v/EJ6QKT_20140530_194403_0F8FDF5FA221AAC02E.png');

CREATE TABLE IF NOT EXISTS `items`(
	`item_id` INT NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(255) NOT NULL,
	`creator` INT NOT NULL,
	`image_url` VARCHAR(255),
	`created_date` DATETIME NOT NULL,
	`last_updated_date` DATETIME NOT NULL,
	PRIMARY KEY(`item_id`),
	FOREIGN KEY(`creator`) REFERENCES `users`(`user_id`) ON DELETE CASCADE ON UPDATE CASCADE);

CREATE TRIGGER `item_created_date` BEFORE INSERT ON `items` FOR EACH ROW SET NEW.`created_date` = NOW(), NEW.`last_updated_date` = NOW();
CREATE TRIGGER `item_last_updated_date` BEFORE UPDATE ON `items` FOR EACH ROW SET NEW.`created_date` = OLD.`created_date`, NEW.`last_updated_date` = NOW();

# INSERT INTO `items` (`item_id`, `name`, `creator`, `image_url`) VALUES (1, 'ATH-M50x Professional Monitor Headphones', 1, 'https://d2qmzng4l690lq.cloudfront.net/resizer/450x450/v/VDWZ23_20140202_100823_2E9CC3160EB4DE7586.png');
# INSERT INTO `items` (`item_id`, `name`, `creator`, `image_url`) VALUES (2, 'FiiO E17 USB DAC Headphone Amplifier', 1, 'https://d2qmzng4l690lq.cloudfront.net/resizer/450x450/v/2FNXFT_20130816_132147_OPWOOEWLU125PK8HXM.png');
# INSERT INTO `items` (`item_id`, `name`, `creator`, `image_url`) VALUES (3, 'iBasso DX50 Digital Audio Player', 1, 'https://d2qmzng4l690lq.cloudfront.net/resizer/450x450/v/EJ6QKT_20140530_194403_0F8FDF5FA221AAC02E.png');

CREATE TABLE IF NOT EXISTS `set_items`(
	`set_item_id` INT NOT NULL AUTO_INCREMENT,
	`set_id` INT NOT NULL,
	`item_id` INT NOT NULL,
	`created_date` DATETIME NOT NULL,
	`last_updated_date` DATETIME NOT NULL,
	PRIMARY KEY(`set_item_id`),
	FOREIGN KEY(`set_id`) REFERENCES `sets`(`set_id`) ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY(`item_id`) REFERENCES `items`(`item_id`) ON DELETE CASCADE ON UPDATE CASCADE);

CREATE TRIGGER `set_items_created_date` BEFORE INSERT ON `set_items` FOR EACH ROW SET NEW.`created_date` = NOW(), NEW.`last_updated_date` = NOW();
CREATE TRIGGER `set_items_last_updated_date` BEFORE UPDATE ON `set_items` FOR EACH ROW SET NEW.`created_date` = OLD.`created_date`, NEW.`last_updated_date` = NOW();

# INSERT INTO `set_items` (`set_id`, `item_id`) VALUES (1, 1);
# INSERT INTO `set_items` (`set_id`, `item_id`) VALUES (1, 2);
# INSERT INTO `set_items` (`set_id`, `item_id`) VALUES (1, 3);

GRANT USAGE ON *.* TO 'massdrop'@'localhost';
DROP USER 'massdrop'@'localhost';
CREATE USER 'massdrop'@'localhost' IDENTIFIED BY 'massdrop';
UPDATE mysql.user SET max_questions = 0, max_updates = 0, max_connections = 0 WHERE User = 'massdrop' AND Host = 'localhost';
GRANT CREATE ROUTINE, CREATE VIEW, ALTER, SHOW VIEW, CREATE, ALTER ROUTINE, EVENT, INSERT, SELECT, DELETE, TRIGGER, GRANT OPTION, REFERENCES, UPDATE, DROP, EXECUTE, LOCK TABLES, CREATE TEMPORARY TABLES, INDEX ON `massdrop`.* TO 'massdrop'@'localhost';
