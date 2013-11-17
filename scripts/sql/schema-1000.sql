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
  CONSTRAINT `fk_Conference_chair_id` FOREIGN KEY `chair_id` (`chair_id`) REFERENCES `User` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET='utf8';

DROP TABLE IF EXISTS `ConferenceTopic`;
CREATE TABLE `ConferenceTopic` (
  `conference_id` INT(11) NOT NULL,
  `topic_id` INT(11) NOT NULL,
  PRIMARY KEY (`conference_id`,`topic_id`),
  CONSTRAINT `fk_ConferenceTopic_conference_id` FOREIGN KEY `conference_id` (`conference_id`) REFERENCES `Conference` (`conference_id`),
  CONSTRAINT `fk_ConferenceTopic_topic_id` FOREIGN KEY `topic_id` (`topic_id`) REFERENCES `Topic` (`topic_id`)
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
  CONSTRAINT `fk_Event_conference_id` FOREIGN KEY `conference_id` (`conference_id`) REFERENCES `Conference` (`conference_id`),
  CONSTRAINT `fk_Event_chair_id` FOREIGN KEY `chair_id` (`chair_id`) REFERENCES `User` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET='utf8';

DROP TABLE IF EXISTS `EventTopic`;
CREATE TABLE `EventTopic` (
  `event_id` INT(11) NOT NULL,
  `topic_id` INT(11) NOT NULL,
  PRIMARY KEY (`event_id`,`topic_id`),
  CONSTRAINT `fk_EventTopic_event_id` FOREIGN KEY `event_id` (`event_id`) REFERENCES `Event` (`event_id`),
  CONSTRAINT `fk_EventTopic_topic_id` FOREIGN KEY `topic_id` (`topic_id`) REFERENCES `Topic` (`topic_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET='utf8';

DROP TABLE IF EXISTS `CommitteeMembership`;
CREATE TABLE `CommitteeMembership` (
  `event_id` INT(11) NOT NULL,
  `user_id` INT(11) NOT NULL,
  PRIMARY KEY (`event_id`,`user_id`),
  CONSTRAINT `fk_CommitteeMembership_event_id` FOREIGN KEY `event_id` (`event_id`) REFERENCES `Event` (`event_id`),
  CONSTRAINT `fk_CommitteeMembership_user_id` FOREIGN KEY `user_id` (`user_id`) REFERENCES `User` (`user_id`)
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
  CONSTRAINT `fk_Paper_event_id` FOREIGN KEY `event_id` (`event_id`) REFERENCES `Event` (`event_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET='utf8';

DROP TABLE IF EXISTS `PaperAuthor`;
CREATE TABLE `PaperAuthor` (
  `paper_id` INT(11) NOT NULL,
  `user_id` INT(11) NOT NULL,
  PRIMARY KEY (`paper_id`,`user_id`),
  CONSTRAINT `fk_PaperAuthor_paper_id` FOREIGN KEY `paper_id` (`paper_id`) REFERENCES `Paper` (`paper_id`),
  CONSTRAINT `fk_PaperAuthor_user_id` FOREIGN KEY `user_id` (`user_id`) REFERENCES `User` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET='utf8';

DROP TABLE IF EXISTS `PaperTopic`;
CREATE TABLE `PaperTopic` (
  `paper_id` INT(11) NOT NULL,
  `topic_id` INT(11) NOT NULL,
  PRIMARY KEY (`paper_id`,`topic_id`),
  CONSTRAINT `fk_PaperTopic_paper_id` FOREIGN KEY `paper_id` (`paper_id`) REFERENCES `Paper` (`paper_id`),
  CONSTRAINT `fk_PaperTopic_topic_id` FOREIGN KEY `topic_id` (`topic_id`) REFERENCES `Topic` (`topic_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET='utf8';

DROP TABLE IF EXISTS `PaperVersion`;
CREATE TABLE `PaperVersion` (
  `paper_id` INT(11) NOT NULL,
  `revision_date` DATETIME NOT NULL,
  PRIMARY KEY (`paper_id`,`revision_date`),
  CONSTRAINT `fk_PaperVersion_paper_id` FOREIGN KEY `paper_id` (`paper_id`) REFERENCES `Paper` (`paper_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET='utf8';

DROP TABLE IF EXISTS `PaperReview`;
CREATE TABLE `PaperReview` (
  `paper_id` INT(11) NOT NULL,
  `reviewer_id` INT(11) NOT NULL,
  `score` INT(2) DEFAULT NULL,
  `confidence` INT(4) DEFAULT NULL,
  `originality` ENUM('good','mediocre','bad'),
  `StrongPoint` TEXT NOT NULL,
  `review_comments` TEXT NOT NULL,
  `author_comments` TEXT NOT NULL,
  `chair_comments` TEXT NOT NULL,
  `external_reviewer_id` INT(11) DEFAULT NULL,
  PRIMARY KEY (`paper_id`,`reviewer_id`),
  CONSTRAINT `fk_PaperReview_paper_id` FOREIGN KEY `paper_id` (`paper_id`) REFERENCES `Paper` (`paper_id`),
  CONSTRAINT `fk_PaperReview_reviewer_id` FOREIGN KEY `reviewer_id` (`reviewer_id`) REFERENCES `User` (`user_id`),
  CONSTRAINT `fk_PaperReview_external_reviewer_id` FOREIGN KEY `external_reviewer_id` (`external_reviewer_id`) REFERENCES `User` (`user_id`)
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
  CONSTRAINT `fk_Message_event_id` FOREIGN KEY `event_id` (`event_id`) REFERENCES `Event` (`event_id`),
  CONSTRAINT `fk_Message_user_id` FOREIGN KEY `user_id` (`user_id`) REFERENCES `User` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET='utf8';

DROP TABLE IF EXISTS `User`;
CREATE TABLE `User` (
  `user_id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `date_created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `title` ENUM('mr','ms','mrs','dr') NOT NULL,
  `first_name` TEXT NOT NULL,
  `middle_name` TEXT NOT NULL,
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
  CONSTRAINT `fk_User_organization_id` FOREIGN KEY `organization_id` (`organization_id`) REFERENCES `Organization` (`organization_id`),
  CONSTRAINT `fk_User_country_id` FOREIGN KEY `country_id` (`country_id`) REFERENCES `Country` (`country_id`),
  CONSTRAINT `fk_User_last_event_id` FOREIGN KEY `last_event_id` (`last_event_id`) REFERENCES `Event` (`event_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET='utf8';

DROP TABLE IF EXISTS `UserTopic`;
CREATE TABLE `UserTopic` (
  `user_id` INT(11) NOT NULL,
  `topic_id` INT(11) NOT NULL,
  PRIMARY KEY (`user_id`,`topic_id`),
  CONSTRAINT `fk_UserTopic_user_id` FOREIGN KEY `user_id` (`user_id`) REFERENCES `User` (`user_id`),
  CONSTRAINT `fk_UserTopic_topic_id` FOREIGN KEY `topic_id` (`topic_id`) REFERENCES `Topic` (`topic_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET='utf8';

DROP TABLE IF EXISTS `UserRole`;
CREATE TABLE `UserRole` (
  `user_id` INT(11) NOT NULL,
  `role` ENUM('admin','normal') NOT NULL,
  PRIMARY KEY (`user_id`,`role`),
  CONSTRAINT `fk_UserRole_user_id` FOREIGN KEY `user_id` (`user_id`) REFERENCES `User` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARACTER SET='utf8';

SET FOREIGN_KEY_CHECKS=1;

INSERT INTO `Country` VALUES ('US', 'United States');
INSERT INTO `Country` VALUES ('CA', 'Canada');
INSERT INTO `Country` VALUES ('AF', 'Afghanistan');
INSERT INTO `Country` VALUES ('AL', 'Albania');
INSERT INTO `Country` VALUES ('DZ', 'Algeria');
INSERT INTO `Country` VALUES ('DS', 'American Samoa');
INSERT INTO `Country` VALUES ('AD', 'Andorra');
INSERT INTO `Country` VALUES ('AO', 'Angola');
INSERT INTO `Country` VALUES ('AI', 'Anguilla');
INSERT INTO `Country` VALUES ('AQ', 'Antarctica');
INSERT INTO `Country` VALUES ('AG', 'Antigua and/or Barbuda');
INSERT INTO `Country` VALUES ('AR', 'Argentina');
INSERT INTO `Country` VALUES ('AM', 'Armenia');
INSERT INTO `Country` VALUES ('AW', 'Aruba');
INSERT INTO `Country` VALUES ('AU', 'Australia');
INSERT INTO `Country` VALUES ('AT', 'Austria');
INSERT INTO `Country` VALUES ('AZ', 'Azerbaijan');
INSERT INTO `Country` VALUES ('BS', 'Bahamas');
INSERT INTO `Country` VALUES ('BH', 'Bahrain');
INSERT INTO `Country` VALUES ('BD', 'Bangladesh');
INSERT INTO `Country` VALUES ('BB', 'Barbados');
INSERT INTO `Country` VALUES ('BY', 'Belarus');
INSERT INTO `Country` VALUES ('BE', 'Belgium');
INSERT INTO `Country` VALUES ('BZ', 'Belize');
INSERT INTO `Country` VALUES ('BJ', 'Benin');
INSERT INTO `Country` VALUES ('BM', 'Bermuda');
INSERT INTO `Country` VALUES ('BT', 'Bhutan');
INSERT INTO `Country` VALUES ('BO', 'Bolivia');
INSERT INTO `Country` VALUES ('BA', 'Bosnia and Herzegovina');
INSERT INTO `Country` VALUES ('BW', 'Botswana');
INSERT INTO `Country` VALUES ('BV', 'Bouvet Island');
INSERT INTO `Country` VALUES ('BR', 'Brazil');
INSERT INTO `Country` VALUES ('IO', 'British lndian Ocean Territory');
INSERT INTO `Country` VALUES ('BN', 'Brunei Darussalam');
INSERT INTO `Country` VALUES ('BG', 'Bulgaria');
INSERT INTO `Country` VALUES ('BF', 'Burkina Faso');
INSERT INTO `Country` VALUES ('BI', 'Burundi');
INSERT INTO `Country` VALUES ('KH', 'Cambodia');
INSERT INTO `Country` VALUES ('CM', 'Cameroon');
INSERT INTO `Country` VALUES ('CV', 'Cape Verde');
INSERT INTO `Country` VALUES ('KY', 'Cayman Islands');
INSERT INTO `Country` VALUES ('CF', 'Central African Republic');
INSERT INTO `Country` VALUES ('TD', 'Chad');
INSERT INTO `Country` VALUES ('CL', 'Chile');
INSERT INTO `Country` VALUES ('CN', 'China');
INSERT INTO `Country` VALUES ('CX', 'Christmas Island');
INSERT INTO `Country` VALUES ('CC', 'Cocos (Keeling) Islands');
INSERT INTO `Country` VALUES ('CO', 'Colombia');
INSERT INTO `Country` VALUES ('KM', 'Comoros');
INSERT INTO `Country` VALUES ('CG', 'Congo');
INSERT INTO `Country` VALUES ('CK', 'Cook Islands');
INSERT INTO `Country` VALUES ('CR', 'Costa Rica');
INSERT INTO `Country` VALUES ('HR', 'Croatia (Hrvatska)');
INSERT INTO `Country` VALUES ('CU', 'Cuba');
INSERT INTO `Country` VALUES ('CY', 'Cyprus');
INSERT INTO `Country` VALUES ('CZ', 'Czech Republic');
INSERT INTO `Country` VALUES ('DK', 'Denmark');
INSERT INTO `Country` VALUES ('DJ', 'Djibouti');
INSERT INTO `Country` VALUES ('DM', 'Dominica');
INSERT INTO `Country` VALUES ('DO', 'Dominican Republic');
INSERT INTO `Country` VALUES ('TP', 'East Timor');
INSERT INTO `Country` VALUES ('EC', 'Ecudaor');
INSERT INTO `Country` VALUES ('EG', 'Egypt');
INSERT INTO `Country` VALUES ('SV', 'El Salvador');
INSERT INTO `Country` VALUES ('GQ', 'Equatorial Guinea');
INSERT INTO `Country` VALUES ('ER', 'Eritrea');
INSERT INTO `Country` VALUES ('EE', 'Estonia');
INSERT INTO `Country` VALUES ('ET', 'Ethiopia');
INSERT INTO `Country` VALUES ('FK', 'Falkland Islands (Malvinas)');
INSERT INTO `Country` VALUES ('FO', 'Faroe Islands');
INSERT INTO `Country` VALUES ('FJ', 'Fiji');
INSERT INTO `Country` VALUES ('FI', 'Finland');
INSERT INTO `Country` VALUES ('FR', 'France');
INSERT INTO `Country` VALUES ('FX', 'France, Metropolitan');
INSERT INTO `Country` VALUES ('GF', 'French Guiana');
INSERT INTO `Country` VALUES ('PF', 'French Polynesia');
INSERT INTO `Country` VALUES ('TF', 'French Southern Territories');
INSERT INTO `Country` VALUES ('GA', 'Gabon');
INSERT INTO `Country` VALUES ('GM', 'Gambia');
INSERT INTO `Country` VALUES ('GE', 'Georgia');
INSERT INTO `Country` VALUES ('DE', 'Germany');
INSERT INTO `Country` VALUES ('GH', 'Ghana');
INSERT INTO `Country` VALUES ('GI', 'Gibraltar');
INSERT INTO `Country` VALUES ('GR', 'Greece');
INSERT INTO `Country` VALUES ('GL', 'Greenland');
INSERT INTO `Country` VALUES ('GD', 'Grenada');
INSERT INTO `Country` VALUES ('GP', 'Guadeloupe');
INSERT INTO `Country` VALUES ('GU', 'Guam');
INSERT INTO `Country` VALUES ('GT', 'Guatemala');
INSERT INTO `Country` VALUES ('GN', 'Guinea');
INSERT INTO `Country` VALUES ('GW', 'Guinea-Bissau');
INSERT INTO `Country` VALUES ('GY', 'Guyana');
INSERT INTO `Country` VALUES ('HT', 'Haiti');
INSERT INTO `Country` VALUES ('HM', 'Heard and Mc Donald Islands');
INSERT INTO `Country` VALUES ('HN', 'Honduras');
INSERT INTO `Country` VALUES ('HK', 'Hong Kong');
INSERT INTO `Country` VALUES ('HU', 'Hungary');
INSERT INTO `Country` VALUES ('IS', 'Iceland');
INSERT INTO `Country` VALUES ('IN', 'India');
INSERT INTO `Country` VALUES ('ID', 'Indonesia');
INSERT INTO `Country` VALUES ('IR', 'Iran (Islamic Republic of)');
INSERT INTO `Country` VALUES ('IQ', 'Iraq');
INSERT INTO `Country` VALUES ('IE', 'Ireland');
INSERT INTO `Country` VALUES ('IL', 'Israel');
INSERT INTO `Country` VALUES ('IT', 'Italy');
INSERT INTO `Country` VALUES ('CI', 'Ivory Coast');
INSERT INTO `Country` VALUES ('JM', 'Jamaica');
INSERT INTO `Country` VALUES ('JP', 'Japan');
INSERT INTO `Country` VALUES ('JO', 'Jordan');
INSERT INTO `Country` VALUES ('KZ', 'Kazakhstan');
INSERT INTO `Country` VALUES ('KE', 'Kenya');
INSERT INTO `Country` VALUES ('KI', 'Kiribati');
INSERT INTO `Country` VALUES ('KP', 'Korea, Democratic People''s Republic of');
INSERT INTO `Country` VALUES ('KR', 'Korea, Republic of');
INSERT INTO `Country` VALUES ('KW', 'Kuwait');
INSERT INTO `Country` VALUES ('KG', 'Kyrgyzstan');
INSERT INTO `Country` VALUES ('LA', 'Lao People''s Democratic Republic');
INSERT INTO `Country` VALUES ('LV', 'Latvia');
INSERT INTO `Country` VALUES ('LB', 'Lebanon');
INSERT INTO `Country` VALUES ('LS', 'Lesotho');
INSERT INTO `Country` VALUES ('LR', 'Liberia');
INSERT INTO `Country` VALUES ('LY', 'Libyan Arab Jamahiriya');
INSERT INTO `Country` VALUES ('LI', 'Liechtenstein');
INSERT INTO `Country` VALUES ('LT', 'Lithuania');
INSERT INTO `Country` VALUES ('LU', 'Luxembourg');
INSERT INTO `Country` VALUES ('MO', 'Macau');
INSERT INTO `Country` VALUES ('MK', 'Macedonia');
INSERT INTO `Country` VALUES ('MG', 'Madagascar');
INSERT INTO `Country` VALUES ('MW', 'Malawi');
INSERT INTO `Country` VALUES ('MY', 'Malaysia');
INSERT INTO `Country` VALUES ('MV', 'Maldives');
INSERT INTO `Country` VALUES ('ML', 'Mali');
INSERT INTO `Country` VALUES ('MT', 'Malta');
INSERT INTO `Country` VALUES ('MH', 'Marshall Islands');
INSERT INTO `Country` VALUES ('MQ', 'Martinique');
INSERT INTO `Country` VALUES ('MR', 'Mauritania');
INSERT INTO `Country` VALUES ('MU', 'Mauritius');
INSERT INTO `Country` VALUES ('TY', 'Mayotte');
INSERT INTO `Country` VALUES ('MX', 'Mexico');
INSERT INTO `Country` VALUES ('FM', 'Micronesia, Federated States of');
INSERT INTO `Country` VALUES ('MD', 'Moldova, Republic of');
INSERT INTO `Country` VALUES ('MC', 'Monaco');
INSERT INTO `Country` VALUES ('MN', 'Mongolia');
INSERT INTO `Country` VALUES ('MS', 'Montserrat');
INSERT INTO `Country` VALUES ('MA', 'Morocco');
INSERT INTO `Country` VALUES ('MZ', 'Mozambique');
INSERT INTO `Country` VALUES ('MM', 'Myanmar');
INSERT INTO `Country` VALUES ('NA', 'Namibia');
INSERT INTO `Country` VALUES ('NR', 'Nauru');
INSERT INTO `Country` VALUES ('NP', 'Nepal');
INSERT INTO `Country` VALUES ('NL', 'Netherlands');
INSERT INTO `Country` VALUES ('AN', 'Netherlands Antilles');
INSERT INTO `Country` VALUES ('NC', 'New Caledonia');
INSERT INTO `Country` VALUES ('NZ', 'New Zealand');
INSERT INTO `Country` VALUES ('NI', 'Nicaragua');
INSERT INTO `Country` VALUES ('NE', 'Niger');
INSERT INTO `Country` VALUES ('NG', 'Nigeria');
INSERT INTO `Country` VALUES ('NU', 'Niue');
INSERT INTO `Country` VALUES ('NF', 'Norfork Island');
INSERT INTO `Country` VALUES ('MP', 'Northern Mariana Islands');
INSERT INTO `Country` VALUES ('NO', 'Norway');
INSERT INTO `Country` VALUES ('OM', 'Oman');
INSERT INTO `Country` VALUES ('PK', 'Pakistan');
INSERT INTO `Country` VALUES ('PW', 'Palau');
INSERT INTO `Country` VALUES ('PA', 'Panama');
INSERT INTO `Country` VALUES ('PG', 'Papua New Guinea');
INSERT INTO `Country` VALUES ('PY', 'Paraguay');
INSERT INTO `Country` VALUES ('PE', 'Peru');
INSERT INTO `Country` VALUES ('PH', 'Philippines');
INSERT INTO `Country` VALUES ('PN', 'Pitcairn');
INSERT INTO `Country` VALUES ('PL', 'Poland');
INSERT INTO `Country` VALUES ('PT', 'Portugal');
INSERT INTO `Country` VALUES ('PR', 'Puerto Rico');
INSERT INTO `Country` VALUES ('QA', 'Qatar');
INSERT INTO `Country` VALUES ('RE', 'Reunion');
INSERT INTO `Country` VALUES ('RO', 'Romania');
INSERT INTO `Country` VALUES ('RU', 'Russian Federation');
INSERT INTO `Country` VALUES ('RW', 'Rwanda');
INSERT INTO `Country` VALUES ('KN', 'Saint Kitts and Nevis');
INSERT INTO `Country` VALUES ('LC', 'Saint Lucia');
INSERT INTO `Country` VALUES ('VC', 'Saint Vincent and the Grenadines');
INSERT INTO `Country` VALUES ('WS', 'Samoa');
INSERT INTO `Country` VALUES ('SM', 'San Marino');
INSERT INTO `Country` VALUES ('ST', 'Sao Tome and Principe');
INSERT INTO `Country` VALUES ('SA', 'Saudi Arabia');
INSERT INTO `Country` VALUES ('SN', 'Senegal');
INSERT INTO `Country` VALUES ('SC', 'Seychelles');
INSERT INTO `Country` VALUES ('SL', 'Sierra Leone');
INSERT INTO `Country` VALUES ('SG', 'Singapore');
INSERT INTO `Country` VALUES ('SK', 'Slovakia');
INSERT INTO `Country` VALUES ('SI', 'Slovenia');
INSERT INTO `Country` VALUES ('SB', 'Solomon Islands');
INSERT INTO `Country` VALUES ('SO', 'Somalia');
INSERT INTO `Country` VALUES ('ZA', 'South Africa');
INSERT INTO `Country` VALUES ('GS', 'South Georgia South Sandwich Islands');
INSERT INTO `Country` VALUES ('ES', 'Spain');
INSERT INTO `Country` VALUES ('LK', 'Sri Lanka');
INSERT INTO `Country` VALUES ('SH', 'St. Helena');
INSERT INTO `Country` VALUES ('PM', 'St. Pierre and Miquelon');
INSERT INTO `Country` VALUES ('SD', 'Sudan');
INSERT INTO `Country` VALUES ('SR', 'Suriname');
INSERT INTO `Country` VALUES ('SJ', 'Svalbarn and Jan Mayen Islands');
INSERT INTO `Country` VALUES ('SZ', 'Swaziland');
INSERT INTO `Country` VALUES ('SE', 'Sweden');
INSERT INTO `Country` VALUES ('CH', 'Switzerland');
INSERT INTO `Country` VALUES ('SY', 'Syrian Arab Republic');
INSERT INTO `Country` VALUES ('TW', 'Taiwan');
INSERT INTO `Country` VALUES ('TJ', 'Tajikistan');
INSERT INTO `Country` VALUES ('TZ', 'Tanzania, United Republic of');
INSERT INTO `Country` VALUES ('TH', 'Thailand');
INSERT INTO `Country` VALUES ('TG', 'Togo');
INSERT INTO `Country` VALUES ('TK', 'Tokelau');
INSERT INTO `Country` VALUES ('TO', 'Tonga');
INSERT INTO `Country` VALUES ('TT', 'Trinidad and Tobago');
INSERT INTO `Country` VALUES ('TN', 'Tunisia');
INSERT INTO `Country` VALUES ('TR', 'Turkey');
INSERT INTO `Country` VALUES ('TM', 'Turkmenistan');
INSERT INTO `Country` VALUES ('TC', 'Turks and Caicos Islands');
INSERT INTO `Country` VALUES ('TV', 'Tuvalu');
INSERT INTO `Country` VALUES ('UG', 'Uganda');
INSERT INTO `Country` VALUES ('UA', 'Ukraine');
INSERT INTO `Country` VALUES ('AE', 'United Arab Emirates');
INSERT INTO `Country` VALUES ('GB', 'United Kingdom');
INSERT INTO `Country` VALUES ('UM', 'United States minor outlying islands');
INSERT INTO `Country` VALUES ('UY', 'Uruguay');
INSERT INTO `Country` VALUES ('UZ', 'Uzbekistan');
INSERT INTO `Country` VALUES ('VU', 'Vanuatu');
INSERT INTO `Country` VALUES ('VA', 'Vatican City State');
INSERT INTO `Country` VALUES ('VE', 'Venezuela');
INSERT INTO `Country` VALUES ('VN', 'Vietnam');
INSERT INTO `Country` VALUES ('VG', 'Virigan Islands (British)');
INSERT INTO `Country` VALUES ('VI', 'Virgin Islands (U.S.)');
INSERT INTO `Country` VALUES ('WF', 'Wallis and Futuna Islands');
INSERT INTO `Country` VALUES ('EH', 'Western Sahara');
INSERT INTO `Country` VALUES ('YE', 'Yemen');
INSERT INTO `Country` VALUES ('YU', 'Yugoslavia');
INSERT INTO `Country` VALUES ('ZR', 'Zaire');
INSERT INTO `Country` VALUES ('ZM', 'Zambia');
INSERT INTO `Country` VALUES ('ZW', 'Zimbabwe');

INSERT INTO `Organization` (name) VALUES('Concordia University');
INSERT INTO `Organization` (name) VALUES('Université de Montréal');
INSERT INTO `Organization` (name) VALUES('Université du Québec à Montréal');
INSERT INTO `Organization` (name) VALUES('École de Technologie Supérieure');
INSERT INTO `Organization` (name) VALUES('McGill University');
INSERT INTO `Organization` (name) VALUES('Google');
INSERT INTO `Organization` (name) VALUES('Microsoft');
INSERT INTO `Organization` (name) VALUES('Amazon');
INSERT INTO `Organization` (name) VALUES('Apple');