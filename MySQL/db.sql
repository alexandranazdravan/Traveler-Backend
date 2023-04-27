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


ALTER TABLE `users`
    ADD UNIQUE KEY `user_name` (`user_name`);

ALTER TABLE `auth_details`
    ADD UNIQUE KEY `user_name` (`user_name`);

ALTER TABLE users
    ADD COLUMN user_avatar TEXT NOT NULL
        DEFAULT 'https://as2.ftcdn.net/v2/jpg/04/10/43/77/1000_F_410437733_hdq4Q3QOH9uwh0mcqAhRFzOKfrCR24Ta.jpg'
        CHECK(user_avatar REGEXP '^(http|https)://[a-zA-Z0-9]+([\-\.]{1}[a-zA-Z0-9]+)*\.[a-zA-Z]{2,}(:[0-9]{1,5})?(\/.*)?$');

CREATE TABLE `wishlist`
(
    `flight_id`   int(11)      NOT NULL AUTO_INCREMENT,
    `user_id`     int(11)      NOT NULL,
    `destination` varchar(255) NOT NULL,
    `origin`      varchar(255) NOT NULL,
    `depart_day`  DATE         NOT NULL,
    `depart_time` TIME         NOT NULL,
    `return_day`  DATE         NOT NULL,
    `return_time` TIME         NOT NULL,
    `airline`     varchar(255) NOT NULL,
    `price`       varchar(255) NOT NULL,
    `lowcost`     varchar(255) NOT NULL,
    PRIMARY KEY (`flight_id`),
    CONSTRAINT `fk_wishlist_users`
        FOREIGN KEY (`user_id`)
            REFERENCES `users` (`user_id`)
            ON DELETE CASCADE
            ON UPDATE CASCADE
);

ALTER TABLE `wishlist`
    ADD COLUMN `flight_number` int(11)
        AFTER `airline`;

ALTER TABLE `auth_details`
    ADD CONSTRAINT `fk_auth_details_users`
        FOREIGN KEY (`user_name`)
            REFERENCES `users` (`user_name`)
            ON DELETE CASCADE
            ON UPDATE CASCADE;


ALTER TABLE wishlist ADD flag varchar(5) DEFAULT "v1";

ALTER TABLE `wishlist`
    MODIFY COLUMN `depart_time` time DEFAULT NULL,
    MODIFY COLUMN `return_time` time DEFAULT NULL,
    MODIFY COLUMN `airline` varchar(255) DEFAULT NULL,
    MODIFY COLUMN `flight_number` int(11) DEFAULT NULL,
    MODIFY COLUMN `lowcost` varchar(255) DEFAULT NULL;

ALTER TABLE wishlist ADD class varchar(40) DEFAULT NULL;
ALTER TABLE wishlist ADD duration varchar(15) DEFAULT NULL;
ALTER TABLE wishlist ADD distance varchar(15) DEFAULT NULL;
ALTER TABLE wishlist ADD no_of_changes int(2) DEFAULT NULL;


