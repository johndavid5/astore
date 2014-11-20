drop table user
;

CREATE TABLE `user` (
  `id` bigint(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL DEFAULT '',
  `hash_algorithm` varchar(12) NOT NULL DEFAULT '',
  `password_hash` varchar(512) NOT NULL DEFAULT '',
  `password_salt` varchar(256) NOT NULL DEFAULT '',
  `firstname` varchar(100) NOT NULL DEFAULT '',
  `lastname` varchar(100) NOT NULL DEFAULT '',
  `created` datetime DEFAULT NULL,
  `session_id` varchar(256) NOT NULL DEFAULT '',
  `last_access` datetime DEFAULT NULL,
  `logged_in` tinyint NOT NULL DEFAULT 0,
  `last_login` datetime DEFAULT NULL,
  `last_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'current_timestamp',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_username_uix` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='One record for each person'
;

