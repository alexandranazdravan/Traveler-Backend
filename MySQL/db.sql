CREATE TABLE `users` (
     `user_id` int(11) NOT NULL AUTO_INCREMENT,
     `user_name` varchar(255) NOT NULL,
     `user_pass` varchar(255) DEFAULT NULL,
     `user_role` varchar(255) NOT NULL DEFAULT 'user',
     `user_fullname` varchar(255) NOT NULL,
     `user_email` varchar(255) NOT NULL,
     PRIMARY KEY (`user_id`)
);


CREATE TABLE `auth_details` (
    `user_name` varchar(255) NOT NULL,
    `cookie` varchar(255) NOT NULL,
    `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
);


ALTER TABLE `users` ADD UNIQUE KEY `user_name` (`user_name`);

ALTER TABLE `auth_details` ADD UNIQUE KEY `user_name` (`user_name`);