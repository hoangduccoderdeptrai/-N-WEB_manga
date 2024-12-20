CREATE TABLE `user` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(50),
  `birthday` date,
  `email` varchar(150),
  `phoneNumber` varchar(20),
  `address` varchar(200),
  `avartar` varchar(255),
  `role_id` INT,
  `created_at` datetime,
  `updated_at` datetime
);

ALTER TABLE user
MODIFY role_id INT DEFAULT 2;


CREATE TABLE `user_role` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `role_type` varchar(255),
  `created_at` datetime,
  `updated_at` datetime
);

CREATE TABLE `User_credential` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `user_id` INT,
  `email` varchar(50),
  `password` varchar(250),
  `created_at` datetime,
  `updated_at` datetime 
);

CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);

CREATE TABLE password_reset_tokens (
    email VARCHAR(255) PRIMARY KEY,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL
);

CREATE TABLE sessions (
    id VARCHAR(255) PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    payload LONGTEXT NOT NULL,
    last_activity INT NOT NULL
    
);


CREATE TABLE `manga` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `author_id` INT NOT NULL,
    `description` TEXT,
    `release_date` DATE,
    `status` ENUM('Ongoing', 'Completed', 'Hiatus') NOT NULL,
    `thumb` VARCHAR(255),
    `slug` VARCHAR(255),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    
);

CREATE TABLE `chapters`(
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `manga_id` INT NOT NULL,
  `chapter_title` VARCHAR(255),
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP

);

CREATE TABLE `cover_images` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `chapter_id` INT,
    `url` VARCHAR(255)
);

CREATE TABLE `authors` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `bio` TEXT,
  `birth_date` DATE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE `genres` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `genre_name` VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE `manga_genres` (
  `manga_id` INT NOT NULL,
  `genre_id` INT NOT NULL,
  PRIMARY KEY (`manga_id`, `genre_id`)
);

CREATE TABLE `reviews` (
    `review_id` INT AUTO_INCREMENT PRIMARY KEY,
    `manga_id` INT NOT NULL,
    `user_id` INT NOT NULL,
    `rating` INT CHECK (rating BETWEEN 1 AND 10),
    `review_text` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

ALTER TABLE `user` ADD FOREIGN KEY (`role_id`) REFERENCES `user_role`(`id`);
ALTER TABLE `User_credential` ADD FOREIGN KEY (`user_id`) REFERENCES `user`(`id`);
ALTER TABLE `cover_images` ADD FOREIGN KEY (`chapter_id`) REFERENCES `chapters`(`id`);
ALTER TABLE `manga` ADD FOREIGN KEY (`author_id`) REFERENCES `authors`(`id`);
ALTER TABLE `manga_genres` ADD  FOREIGN KEY (`manga_id`) REFERENCES `manga`(`id`);
ALTER TABLE `manga_genres` ADD FOREIGN KEY (`genre_id`) REFERENCES `genres`(`id`);
ALTER TABLE `reviews` ADD FOREIGN KEY (`manga_id`) REFERENCES `manga`(`id`);
ALTER TABLE `reviews` ADD FOREIGN KEY (`user_id`) REFERENCES `user`(`id`);
ALTER TABLE `sessions` ADD FOREIGN KEY (`user_id`) REFERENCES `users`(`id`);
ALTER TABLE `chapters` ADD FOREIGN KEY (`manga_id`) REFERENCES `manga`(`id`);
-- insert role user
INSERT INTO `user_role` (`id`, `role_type`, `created_at`, `updated_at`) VALUES (NULL, 'Admin', NOW(), NOW());
INSERT INTO `user_role` (`id`, `role_type`, `created_at`, `updated_at`) VALUES (NULL, 'user', NOW(), NOW());

-- insert author
INSERT INTO `authors` (`name`, `bio`, `birth_date`, `created_at`, `updated_at`)
VALUES
('Đang cập nhật','updating',NULL,NOW(),NOW()),
('Hirohiko Araki', 'updating', '1960-06-07', NOW(), NOW()),
('Masashi Kishimoto', 'updating', '1974-11-08', NOW(), NOW()),
('Yoshihiro Togashi', 'updating', '1966-04-27', NOW(), NOW()),
('Gosho Aoyama', 'updating', '1963-06-21', NOW(), NOW()),
('Hajime Isayama', 'updating', '1986-08-29', NOW(), NOW());

-- insert gener
INSERT INTO `genres`(`genre_name`)
VALUES
('Action'),
('Adventure'),
('Comedy'),
('Drama'),
('Fantasy'),
('Horror'),
('Mystery'),
('Romance');


