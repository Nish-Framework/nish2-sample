CREATE SCHEMA IF NOT EXISTS `nish2_sample` DEFAULT CHARACTER SET utf8mb4;


CREATE TABLE IF NOT EXISTS nish2_sample.translation_keys (
                                                             `id` int(11) NOT NULL AUTO_INCREMENT,
                                                             `namespace` varchar(20) DEFAULT NULL,
                                                             `trans_key` varchar(255) DEFAULT NULL,
                                                             `create_service` varchar(15) null,
                                                             `created_by` int(11) NOT NULL DEFAULT '0',
                                                             `created_at` int(11) NOT NULL DEFAULT '0',
                                                             `update_service` varchar(15)  null,
                                                             `updated_by` int(11) DEFAULT '0',
                                                             `updated_at` int(11) DEFAULT '0',
                                                             `is_deleted` tinyint(4) NOT NULL DEFAULT '0',
                                                             `delete_service` varchar(15)  null,
                                                             `deleted_by` int(11) DEFAULT '0',
                                                             `deleted_at` int(11) DEFAULT '0',
                                                             PRIMARY KEY (`id`),
                                                             KEY `ind_del` (`is_deleted`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS nish2_sample.translations (
                                                         `id` int(11) NOT NULL AUTO_INCREMENT,
                                                         `namespace` varchar(20) DEFAULT NULL,
                                                         `lang` varchar(10) DEFAULT NULL,
                                                         `trans_key` varchar(255) DEFAULT NULL,
                                                         `trans_value` varchar(255) DEFAULT NULL,
                                                         `create_service` varchar(15) null,
                                                         `created_by` int(11) NOT NULL DEFAULT '0',
                                                         `created_at` int(11) NOT NULL DEFAULT '0',
                                                         `update_service` varchar(15)  null,
                                                         `updated_by` int(11) DEFAULT '0',
                                                         `updated_at` int(11) DEFAULT '0',
                                                         `is_deleted` tinyint(4) NOT NULL DEFAULT '0',
                                                         `delete_service` varchar(15)  null,
                                                         `deleted_by` int(11) DEFAULT '0',
                                                         `deleted_at` int(11) DEFAULT '0',
                                                         PRIMARY KEY (`id`),
                                                         KEY `ind_del` (`is_deleted`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS nish2_sample.users (
                                                  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                                                  `first_name` varchar(100) DEFAULT NULL,
                                                  `last_name` varchar(100) DEFAULT NULL,
                                                  `password` varchar(40) DEFAULT NULL,
                                                  `role` varchar(50) DEFAULT NULL,
                                                  `gsm` varchar(50) DEFAULT NULL,
                                                  `email` varchar(100) NOT NULL,
                                                  `create_service` varchar(15) null,
                                                  `created_by` int(11) NOT NULL DEFAULT '0',
                                                  `created_at` int(11) NOT NULL DEFAULT '0',
                                                  `update_service` varchar(15) null,
                                                  `updated_by` int(11) DEFAULT '0',
                                                  `updated_at` int(11) DEFAULT '0',
                                                  `is_deleted` tinyint(4) NOT NULL DEFAULT '0',
                                                  `delete_service` varchar(15) null,
                                                  `deleted_by` int(11) DEFAULT '0',
                                                  `deleted_at` int(11) DEFAULT '0',
                                                  `default_lang` varchar(10) DEFAULT 'tr',
                                                  `flg_tester` tinyint(4) DEFAULT '0',
                                                  PRIMARY KEY (`id`),
                                                  KEY `ind_del` (`is_deleted`),
                                                  KEY `ind_email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

