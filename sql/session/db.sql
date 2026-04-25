CREATE TABLE `sessions` (
    `id` VARCHAR(255) NOT NULL,
    `data` text,
    `timestamp` INT UNSIGNED NOT NULL,
    PRIMARY KEY (`id`),
    INDEX `timestamp_idx` (`timestamp`)
);
