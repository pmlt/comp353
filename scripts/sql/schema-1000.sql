SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `Topic`;
CREATE TABLE `Topic` (
  `topic_id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `category` VARCHAR(255) NOT NULL,
  UNIQUE KEY `unique_topic` (`category`,`name`)
) ENGINE=InnoDB DEFAULT CHARACTER SET='utf8'; 

DROP TABLE IF EXISTS `Organization`;
CREATE TABLE `Organization` (
  `organization_id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARACTER SET='utf8';

DROP TABLE IF EXISTS `Country`;
CREATE TABLE `Country` (
  `country_id` CHAR(2) NOT NULL PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARACTER SET='utf8'; 

DROP TABLE IF EXISTS `Conference`;
CREATE TABLE `Conference` (
  `conference_id` INT(11) AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL UNIQUE,
  `description` TEXT NOT NULL,
  `type` ENUM('J','C') NOT NULL,
  `chair_id` int(11) NOT NULL,
  CONSTRAINT `fk_Conference_chair_id` FOREIGN KEY `chair_id` (`chair_id`) REFERENCES `User` (`user_id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARACTER SET='utf8';

DROP TABLE IF EXISTS `ConferenceTopic`;
CREATE TABLE `ConferenceTopic` (
  `conference_id` INT(11) NOT NULL,
  `topic_id` INT(11) NOT NULL,
  PRIMARY KEY (`conference_id`,`topic_id`),
  CONSTRAINT `fk_ConferenceTopic_conference_id` FOREIGN KEY `conference_id` (`conference_id`) REFERENCES `Conference` (`conference_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_ConferenceTopic_topic_id` FOREIGN KEY `topic_id` (`topic_id`) REFERENCES `Topic` (`topic_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARACTER SET='utf8';

DROP TABLE IF EXISTS `Event`;
CREATE TABLE `Event` (
  `event_id` INT(11) AUTO_INCREMENT PRIMARY KEY,
  `conference_id` INT(11) NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT NOT NULL,
  `term_start_date` DATETIME NOT NULL,
  `term_end_date` DATETIME NOT NULL,
  `submit_start_date` DATETIME NOT NULL,
  `submit_end_date` DATETIME NOT NULL,
  `auction_start_date` DATETIME NOT NULL,
  `auction_end_date` DATETIME NOT NULL,
  `review_start_date` DATETIME NOT NULL,
  `review_end_date` DATETIME NOT NULL,
  `decision_date` DATETIME NOT NULL,
  `start_date` DATETIME NOT NULL,
  `end_date` DATETIME NOT NULL,
  `chair_id` INT(11) NOT NULL,
  UNIQUE KEY `unique_title` (`conference_id`,`title`),
  CONSTRAINT `fk_Event_conference_id` FOREIGN KEY `conference_id` (`conference_id`) REFERENCES `Conference` (`conference_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_Event_chair_id` FOREIGN KEY `chair_id` (`chair_id`) REFERENCES `User` (`user_id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARACTER SET='utf8';

DROP TABLE IF EXISTS `EventTopic`;
CREATE TABLE `EventTopic` (
  `event_id` INT(11) NOT NULL,
  `topic_id` INT(11) NOT NULL,
  PRIMARY KEY (`event_id`,`topic_id`),
  CONSTRAINT `fk_EventTopic_event_id` FOREIGN KEY `event_id` (`event_id`) REFERENCES `Event` (`event_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_EventTopic_topic_id` FOREIGN KEY `topic_id` (`topic_id`) REFERENCES `Topic` (`topic_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARACTER SET='utf8';

DROP TABLE IF EXISTS `CommitteeMembership`;
CREATE TABLE `CommitteeMembership` (
  `event_id` INT(11) NOT NULL,
  `user_id` INT(11) NOT NULL,
  PRIMARY KEY (`event_id`,`user_id`),
  CONSTRAINT `fk_CommitteeMembership_event_id` FOREIGN KEY `event_id` (`event_id`) REFERENCES `Event` (`event_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_CommitteeMembership_user_id` FOREIGN KEY `user_id` (`user_id`) REFERENCES `User` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARACTER SET='utf8';

DROP TABLE IF EXISTS `Paper`;
CREATE TABLE `Paper` (
  `paper_id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `title` TEXT NOT NULL,
  `abstract` TEXT NOT NULL,
  `keywords` TEXT NOT NULL,
  `submitter_id` INT(11) NOT NULL,
  `event_id` INT(11) NOT NULL,
  `decision` ENUM('pending','full','short','poster','workshop','position','demo','rejected') NOT NULL,
  `decision_date` DATETIME DEFAULT NULL,
  `publish_date` DATETIME DEFAULT NULL,
  CONSTRAINT `fk_Paper_event_id` FOREIGN KEY `event_id` (`event_id`) REFERENCES `Event` (`event_id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARACTER SET='utf8';

DROP TABLE IF EXISTS `PaperAuthor`;
CREATE TABLE `PaperAuthor` (
  `paper_id` INT(11) NOT NULL,
  `user_id` INT(11) NOT NULL,
  PRIMARY KEY (`paper_id`,`user_id`),
  CONSTRAINT `fk_PaperAuthor_paper_id` FOREIGN KEY `paper_id` (`paper_id`) REFERENCES `Paper` (`paper_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_PaperAuthor_user_id` FOREIGN KEY `user_id` (`user_id`) REFERENCES `User` (`user_id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARACTER SET='utf8';

DROP TABLE IF EXISTS `PaperTopic`;
CREATE TABLE `PaperTopic` (
  `paper_id` INT(11) NOT NULL,
  `topic_id` INT(11) NOT NULL,
  PRIMARY KEY (`paper_id`,`topic_id`),
  CONSTRAINT `fk_PaperTopic_paper_id` FOREIGN KEY `paper_id` (`paper_id`) REFERENCES `Paper` (`paper_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_PaperTopic_topic_id` FOREIGN KEY `topic_id` (`topic_id`) REFERENCES `Topic` (`topic_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARACTER SET='utf8';

DROP TABLE IF EXISTS `PaperVersion`;
CREATE TABLE `PaperVersion` (
  `paper_id` INT(11) NOT NULL,
  `revision_date` DATETIME NOT NULL,
  PRIMARY KEY (`paper_id`,`revision_date`),
  CONSTRAINT `fk_PaperVersion_paper_id` FOREIGN KEY `paper_id` (`paper_id`) REFERENCES `Paper` (`paper_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARACTER SET='utf8';

DROP TABLE IF EXISTS `PaperBid`;
CREATE TABLE `PaperBid` (
  `user_id` INT(11) NOT NULL,
  `paper_id` INT(11) NOT NULL,
  PRIMARY KEY (`user_id`,`paper_id`),
  CONSTRAINT `fk_PaperBid_user_id` FOREIGN KEY `user_id` (`user_id`) REFERENCES `User` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_PaperBid_paper_id` FOREIGN KEY `paper_id` (`paper_id`) REFERENCES `Paper` (`paper_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARACTER SET='utf8';

DROP TABLE IF EXISTS `PaperReview`;
CREATE TABLE `PaperReview` (
  `review_id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `paper_id` INT(11) NOT NULL,
  `reviewer_id` INT(11) NOT NULL,
  `score` INT(2) DEFAULT NULL,
  `confidence` INT(4) DEFAULT NULL,
  `originality` ENUM('good','mediocre','bad') DEFAULT NULL,
  `strong_point` TEXT NOT NULL,
  `review_comments` TEXT NOT NULL,
  `author_comments` TEXT NOT NULL,
  `chair_comments` TEXT NOT NULL,
  `external_reviewer_id` INT(11) DEFAULT NULL,
  UNIQUE KEY `unique_review` (`paper_id`,`reviewer_id`),
  CONSTRAINT `fk_PaperReview_paper_id` FOREIGN KEY `paper_id` (`paper_id`) REFERENCES `Paper` (`paper_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_PaperReview_reviewer_id` FOREIGN KEY `reviewer_id` (`reviewer_id`) REFERENCES `User` (`user_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_PaperReview_external_reviewer_id` FOREIGN KEY `external_reviewer_id` (`external_reviewer_id`) REFERENCES `User` (`user_id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARACTER SET='utf8';

DROP TABLE IF EXISTS `Message`;
CREATE TABLE `Message` (
  `message_id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `event_id` INT(11) NOT NULL,
  `user_id` INT(11) NOT NULL,
  `publish_date` DATETIME NOT NULL,
  `is_public` BIT(1) NOT NULL,
  `title` TEXT NOT NULL,
  `excerpt` TEXT NOT NULL,
  `body` TEXT NOT NULL,
  CONSTRAINT `fk_Message_event_id` FOREIGN KEY `event_id` (`event_id`) REFERENCES `Event` (`event_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_Message_user_id` FOREIGN KEY `user_id` (`user_id`) REFERENCES `User` (`user_id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARACTER SET='utf8';

DROP TABLE IF EXISTS `User`;
CREATE TABLE `User` (
  `user_id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `date_created` DATETIME NOT NULL,
  `title` ENUM('mr','ms','mrs','dr') NOT NULL,
  `first_name` TEXT NOT NULL,
  `middle_name` TEXT DEFAULT NULL,
  `last_name` TEXT NOT NULL,
  `country_id` CHAR(2) NOT NULL,
  `organization_id` INT(11) NOT NULL,
  `department` TEXT NOT NULL,
  `address` TEXT DEFAULT NULL,
  `city` TEXT DEFAULT NULL,
  `province` TEXT DEFAULT NULL,
  `postcode` TEXT DEFAULT NULL,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `password` TEXT NOT NULL,
  `email_sent_flag` BIT(1) NOT NULL DEFAULT b'0',
  `last_event_id` INT(11) DEFAULT NULL,
  CONSTRAINT `fk_User_organization_id` FOREIGN KEY `organization_id` (`organization_id`) REFERENCES `Organization` (`organization_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_User_country_id` FOREIGN KEY `country_id` (`country_id`) REFERENCES `Country` (`country_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_User_last_event_id` FOREIGN KEY `last_event_id` (`last_event_id`) REFERENCES `Event` (`event_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARACTER SET='utf8';

DROP TABLE IF EXISTS `UserTopic`;
CREATE TABLE `UserTopic` (
  `user_id` INT(11) NOT NULL,
  `topic_id` INT(11) NOT NULL,
  PRIMARY KEY (`user_id`,`topic_id`),
  CONSTRAINT `fk_UserTopic_user_id` FOREIGN KEY `user_id` (`user_id`) REFERENCES `User` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_UserTopic_topic_id` FOREIGN KEY `topic_id` (`topic_id`) REFERENCES `Topic` (`topic_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARACTER SET='utf8';

DROP TABLE IF EXISTS `UserRole`;
CREATE TABLE `UserRole` (
  `user_id` INT(11) NOT NULL,
  `role` ENUM('admin','normal') NOT NULL,
  PRIMARY KEY (`user_id`,`role`),
  CONSTRAINT `fk_UserRole_user_id` FOREIGN KEY `user_id` (`user_id`) REFERENCES `User` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARACTER SET='utf8';

SET FOREIGN_KEY_CHECKS=1;

