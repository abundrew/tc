SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

CREATE SCHEMA IF NOT EXISTS `tc` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci ;
USE `tc` ;

-- -----------------------------------------------------
-- Table `tc`.`store`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `tc`.`store` (
  `store_id` INT NOT NULL AUTO_INCREMENT ,
  `code` VARCHAR(20) NOT NULL ,
  `description` VARCHAR(100) NULL ,
  PRIMARY KEY (`store_id`) ,
  UNIQUE INDEX `code_UNIQUE` (`code` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tc`.`period`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `tc`.`period` (
  `period_id` INT NOT NULL AUTO_INCREMENT ,
  `period_number` INT NOT NULL ,
  `started` DATE NOT NULL ,
  `ended` DATE NOT NULL ,
  PRIMARY KEY (`period_id`) ,
  UNIQUE INDEX `code_UNIQUE` (`period_number` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tc`.`job`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `tc`.`job` (
  `job_id` INT NOT NULL AUTO_INCREMENT ,
  `code` CHAR NOT NULL ,
  `description` VARCHAR(45) NOT NULL ,
  `manager` BIT NOT NULL ,
  PRIMARY KEY (`job_id`) ,
  UNIQUE INDEX `code_UNIQUE` (`code` ASC) ,
  UNIQUE INDEX `description_UNIQUE` (`description` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tc`.`employee`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `tc`.`employee` (
  `employee_id` INT NOT NULL AUTO_INCREMENT ,
  `code` VARCHAR(20) NOT NULL ,
  `first_name` VARCHAR(45) NOT NULL ,
  `last_name` VARCHAR(45) NOT NULL ,
  `pwd_md5` VARCHAR(32) NULL ,
  `store_store_id` INT NOT NULL ,
  `job_job_id` INT NOT NULL ,
  PRIMARY KEY (`employee_id`) ,
  UNIQUE INDEX `code_UNIQUE` (`code` ASC) ,
  INDEX `fk_employee_job` (`job_job_id` ASC) ,
  INDEX `fk_employee_store1` (`store_store_id` ASC) ,
  CONSTRAINT `fk_employee_job`
    FOREIGN KEY (`job_job_id` )
    REFERENCES `tc`.`job` (`job_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_employee_store1`
    FOREIGN KEY (`store_store_id` )
    REFERENCES `tc`.`store` (`store_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tc`.`punch`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `tc`.`punch` (
  `punch_id` INT NOT NULL AUTO_INCREMENT ,
  `punch_date` DATE NOT NULL ,
  `punch_time` TIME NOT NULL ,
  `punch_io` CHAR NOT NULL ,
  `employee_employee_id` INT NOT NULL ,
  `store_store_id` INT NOT NULL ,
  `manager_employee_id` INT NULL ,
  PRIMARY KEY (`punch_id`) ,
  UNIQUE INDEX `employee_date_time_UNIQUE` (`employee_employee_id` ASC, `punch_date` ASC, `punch_time` ASC) ,
  INDEX `fk_punch_employee1` (`manager_employee_id` ASC) ,
  INDEX `fk_punch_employee2` (`employee_employee_id` ASC) ,
  INDEX `fk_punch_store1` (`store_store_id` ASC) ,
  CONSTRAINT `fk_punch_employee1`
    FOREIGN KEY (`manager_employee_id` )
    REFERENCES `tc`.`employee` (`employee_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_punch_employee2`
    FOREIGN KEY (`employee_employee_id` )
    REFERENCES `tc`.`employee` (`employee_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_punch_store1`
    FOREIGN KEY (`store_store_id` )
    REFERENCES `tc`.`store` (`store_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tc`.`fraud`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `tc`.`fraud` (
  `fraud_id` INT NOT NULL AUTO_INCREMENT ,
  `fraud_date` DATE NOT NULL ,
  `fraud_time` TIME NOT NULL ,
  `code` VARCHAR(20) NOT NULL ,
  `pwd` VARCHAR(20) NOT NULL ,
  `ip_address` VARCHAR(40) NOT NULL ,
  PRIMARY KEY (`fraud_id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tc`.`store_ip_address`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `tc`.`store_ip_address` (
  `store_ip_address_id` INT NOT NULL AUTO_INCREMENT ,
  `ip_address` VARCHAR(40) NOT NULL ,
  `store_store_id` INT NOT NULL ,
  PRIMARY KEY (`store_ip_address_id`) ,
  INDEX `fk_ip_address_store1` (`store_store_id` ASC) ,
  UNIQUE INDEX `ip_address_UNIQUE` (`ip_address` ASC) ,
  CONSTRAINT `fk_ip_address_store1`
    FOREIGN KEY (`store_store_id` )
    REFERENCES `tc`.`store` (`store_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tc`.`admin`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `tc`.`admin` (
  `admin_id` INT NOT NULL AUTO_INCREMENT ,
  `code` VARCHAR(20) NOT NULL ,
  `name` VARCHAR(45) NOT NULL ,
  `pwd_md5` VARCHAR(32) NOT NULL ,
  PRIMARY KEY (`admin_id`) ,
  UNIQUE INDEX `code_UNIQUE` (`code` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `tc`.`admin_ip_address`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `tc`.`admin_ip_address` (
  `admin_ip_address_id` INT NOT NULL AUTO_INCREMENT ,
  `ip_address` VARCHAR(40) NOT NULL ,
  `admin_admin_id` INT NOT NULL ,
  PRIMARY KEY (`admin_ip_address_id`) ,
  UNIQUE INDEX `ip_address_UNIQUE` (`ip_address` ASC) ,
  INDEX `fk_admin_ip_address_admin1` (`admin_admin_id` ASC) ,
  CONSTRAINT `fk_admin_ip_address_admin1`
    FOREIGN KEY (`admin_admin_id` )
    REFERENCES `tc`.`admin` (`admin_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

USE `tc`;

DELIMITER $$
USE `tc`$$








CREATE TRIGGER t_i_period BEFORE INSERT ON period
    FOR EACH ROW BEGIN
    IF NEW.started > NEW.ended THEN
        SET NEW.started = 1 / 0;
    END IF;
END $$

USE `tc`$$








CREATE TRIGGER t_u_period BEFORE UPDATE ON period
    FOR EACH ROW BEGIN
    IF NEW.started > NEW.ended THEN
        SET NEW.started = 1 / 0;
    END IF;
END $$


DELIMITER ;

DELIMITER $$
USE `tc`$$


















CREATE TRIGGER t_i_punch BEFORE INSERT ON punch
    FOR EACH ROW BEGIN
    IF NEW.punch_time < '00:00:00' THEN
        SET NEW.punch_time = 1 / 0;
    END IF;
    IF NEW.punch_time >= '24:00:00' THEN
        SET NEW.punch_time = 1 / 0;
    END IF;
    IF NEW.punch_io NOT IN ('I', 'O') THEN
        SET NEW.punch_io = 1 / 0;
    END IF;
END $$

USE `tc`$$


















CREATE TRIGGER t_u_punch BEFORE UPDATE ON punch
    FOR EACH ROW BEGIN
    IF NEW.punch_time < '00:00:00' THEN
        SET NEW.punch_time = 1 / 0;
    END IF;
    IF NEW.punch_time >= '24:00:00' THEN
        SET NEW.punch_time = 1 / 0;
    END IF;
    IF NEW.punch_io NOT IN ('I', 'O') THEN
        SET NEW.punch_io = 1 / 0;
    END IF;
END $$


DELIMITER ;

DELIMITER $$
USE `tc`$$










CREATE TRIGGER t_i_fraud BEFORE INSERT ON fraud
    FOR EACH ROW BEGIN
    IF NEW.fraud_time < '00:00:00' THEN
        SET NEW.fraud_time = 1 / 0;
    END IF;
    IF NEW.fraud_time >= '24:00:00' THEN
        SET NEW.fraud_time = 1 / 0;
    END IF;
END $$

USE `tc`$$
















CREATE TRIGGER t_u_fraud BEFORE UPDATE ON fraud
    FOR EACH ROW BEGIN
    IF NEW.fraud_time < '00:00:00' THEN
        SET NEW.fraud_time = 1 / 0;
    END IF;
    IF NEW.fraud_time >= '24:00:00' THEN
        SET NEW.fraud_time = 1 / 0;
    END IF;
END $$


DELIMITER ;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
