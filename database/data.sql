-- --------------------------------------------------------
--
-- Table structure for table `user_browser_data`
--
CREATE TABLE `user_browser_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `cookie_consent` tinyint(1) NOT NULL DEFAULT 0,
  `consent_timestamp` datetime DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `device_fingerprint` varchar(255) DEFAULT NULL,
  `screen_resolution` varchar(20) DEFAULT NULL,
  `timezone` varchar(50) DEFAULT NULL,
  `language` varchar(10) DEFAULT NULL,
  `has_payment_methods` tinyint(1) DEFAULT 0 COMMENT 'Detected if browser has payment methods saved',
  `payment_methods_count` smallint DEFAULT 0 COMMENT 'Number of payment methods detected',
  `autofill_data` text DEFAULT NULL COMMENT 'JSON of autofill field names detected',
  `form_preferences` text DEFAULT NULL COMMENT 'JSON of saved form preferences',
  `last_activity` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id_unique` (`user_id`),
  KEY `cookie_consent_index` (`cookie_consent`),
  KEY `has_payment_methods_index` (`has_payment_methods`),
  CONSTRAINT `user_browser_data_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
--
-- Table structure for table `user_consent_logs`
--
CREATE TABLE `user_consent_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `consent_type` enum('cookies','privacy_policy','terms','marketing') NOT NULL,
  `action` enum('granted','revoked') NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id_index` (`user_id`),
  KEY `consent_type_index` (`consent_type`),
  CONSTRAINT `user_consent_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;