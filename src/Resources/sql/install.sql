DROP TABLE IF EXISTS `bundle_divante_clipboard`;
CREATE TABLE `bundle_divante_clipboard` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `userId` int(11) unsigned NOT NULL,
  `objectId` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;