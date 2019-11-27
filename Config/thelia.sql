
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- order_product_status
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `order_product_status`;

CREATE TABLE `order_product_status`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `status_id` INTEGER,
    `order_product_id` INTEGER,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`),
    INDEX `FI_order_product_status_status_id` (`status_id`),
    INDEX `FI_order_product_status_order_product_id` (`order_product_id`),
    CONSTRAINT `fk_order_product_status_status_id`
        FOREIGN KEY (`status_id`)
        REFERENCES `order_status` (`id`)
        ON UPDATE RESTRICT
        ON DELETE RESTRICT,
    CONSTRAINT `fk_order_product_status_order_product_id`
        FOREIGN KEY (`order_product_id`)
        REFERENCES `order_product` (`id`)
        ON UPDATE RESTRICT
        ON DELETE RESTRICT
) ENGINE=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
