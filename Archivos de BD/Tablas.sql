-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema general
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema general
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `general` DEFAULT CHARACTER SET utf8 ;
USE `general` ;

-- -----------------------------------------------------
-- Table `general`.`Usuario`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `general`.`Usuario` (
  `idUsuario` INT NOT NULL,
  `username` VARCHAR(50) NOT NULL,
  `password` VARCHAR(100) NOT NULL,
  `tipo` INT NOT NULL COMMENT '1: admin\n2: transportes\n3: planificacion',
  PRIMARY KEY (`idUsuario`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `general`.`Bodega`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `general`.`Bodega` (
  `idBodega` INT NOT NULL,
  `nombre` VARCHAR(45) NOT NULL,
  `direccion` VARCHAR(200) NOT NULL,
  `longitud` DOUBLE NOT NULL,
  `latitud` DOUBLE NOT NULL,
  PRIMARY KEY (`idBodega`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `general`.`Producto`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `general`.`Producto` (
  `idProducto` INT NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(200) NOT NULL,
  `precioCompra` INT NULL,
  `precioVenta` INT NOT NULL,
  `impuesto` INT NOT NULL COMMENT 'En porcentaje.\n13 para 13%\n0 para nada de impuesto',
  `pesoPorCaja` INT NULL COMMENT 'En kg',
  PRIMARY KEY (`idProducto`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `general`.`Inventario`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `general`.`Inventario` (
  `Bodega_idBodega` INT NOT NULL,
  `Producto_idProducto` INT NOT NULL,
  `cantidad` INT NOT NULL,
  PRIMARY KEY (`Producto_idProducto`, `Bodega_idBodega`),
  CONSTRAINT `fk_Bodega_has_Producto_Bodega`
    FOREIGN KEY (`Bodega_idBodega`)
    REFERENCES `general`.`Bodega` (`idBodega`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Bodega_has_Producto_Producto1`
    FOREIGN KEY (`Producto_idProducto`)
    REFERENCES `general`.`Producto` (`idProducto`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_Bodega_has_Producto_Producto1_idx` ON `general`.`Inventario` (`Producto_idProducto` ASC);

CREATE INDEX `fk_Bodega_has_Producto_Bodega_idx` ON `general`.`Inventario` (`Bodega_idBodega` ASC);


-- -----------------------------------------------------
-- Table `general`.`Marca`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `general`.`Marca` (
  `idMarca` INT NOT NULL,
  `nombre` VARCHAR(100) NOT NULL,
  `Producto_idProducto` INT NOT NULL,
  PRIMARY KEY (`idMarca`),
  CONSTRAINT `fk_Marca_Producto1`
    FOREIGN KEY (`Producto_idProducto`)
    REFERENCES `general`.`Producto` (`idProducto`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_Marca_Producto1_idx` ON `general`.`Marca` (`Producto_idProducto` ASC);


-- -----------------------------------------------------
-- Table `general`.`Categoria`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `general`.`Categoria` (
  `idCategoria` INT NOT NULL,
  `nombre` VARCHAR(100) NULL,
  `Producto_idProducto` INT NOT NULL,
  PRIMARY KEY (`idCategoria`),
  CONSTRAINT `fk_Categoria_Producto1`
    FOREIGN KEY (`Producto_idProducto`)
    REFERENCES `general`.`Producto` (`idProducto`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_Categoria_Producto1_idx` ON `general`.`Categoria` (`Producto_idProducto` ASC);


-- -----------------------------------------------------
-- Table `general`.`Empleado`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `general`.`Empleado` (
  `cedula` INT NOT NULL,
  `nombre` VARCHAR(100) NOT NULL,
  `apellidos` VARCHAR(200) NOT NULL,
  `idTipoEmpleado` VARCHAR(3) NOT NULL,
  `Usuario_idUsuario` INT NOT NULL,
  PRIMARY KEY (`Usuario_idUsuario`),
  CONSTRAINT `fk_Empleado_Usuario1`
    FOREIGN KEY (`Usuario_idUsuario`)
    REFERENCES `general`.`Usuario` (`idUsuario`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `general`.`EmailsXEmpleado`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `general`.`EmailsXEmpleado` (
  `email` VARCHAR(45) NOT NULL,
  `Empleado_Usuario_idUsuario` INT NOT NULL,
  PRIMARY KEY (`email`),
  CONSTRAINT `fk_EmailsXEmpleado_Empleado1`
    FOREIGN KEY (`Empleado_Usuario_idUsuario`)
    REFERENCES `general`.`Empleado` (`Usuario_idUsuario`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE UNIQUE INDEX `email_UNIQUE` ON `general`.`EmailsXEmpleado` (`email` ASC);


-- -----------------------------------------------------
-- Table `general`.`TelefonosXEmpleado`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `general`.`TelefonosXEmpleado` (
  `telefono` INT NOT NULL,
  `Empleado_Usuario_idUsuario` INT NOT NULL,
  PRIMARY KEY (`telefono`),
  CONSTRAINT `fk_TelefonosXEmpleado_Empleado1`
    FOREIGN KEY (`Empleado_Usuario_idUsuario`)
    REFERENCES `general`.`Empleado` (`Usuario_idUsuario`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE UNIQUE INDEX `telefono_UNIQUE` ON `general`.`TelefonosXEmpleado` (`telefono` ASC);


-- -----------------------------------------------------
-- Table `general`.`Camion`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `general`.`Camion` (
  `idCamion` INT NOT NULL COMMENT 'placa',
  `anho` INT NOT NULL,
  `RTV` TINYINT(1) NULL,
  `marca` VARCHAR(45) NULL,
  `modelo` VARCHAR(45) NULL,
  `combustible` VARCHAR(45) NULL,
  `fueraDeServicio` TINYINT(1) NULL,
  PRIMARY KEY (`idCamion`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `general`.`Provincia`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `general`.`Provincia` (
  `idProvincia` INT NOT NULL,
  `nombre` VARCHAR(45) NULL,
  PRIMARY KEY (`idProvincia`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `general`.`Canton`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `general`.`Canton` (
  `idCanton` INT NOT NULL,
  `nombre` VARCHAR(45) NULL,
  `Provincia_idProvincia` INT NOT NULL,
  PRIMARY KEY (`idCanton`),
  CONSTRAINT `fk_Canton_Provincia1`
    FOREIGN KEY (`Provincia_idProvincia`)
    REFERENCES `general`.`Provincia` (`idProvincia`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_Canton_Provincia1_idx` ON `general`.`Canton` (`Provincia_idProvincia` ASC);


-- -----------------------------------------------------
-- Table `general`.`Ruta`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `general`.`Ruta` (
  `idRuta` INT NOT NULL,
  `Canton_idCanton1` INT NOT NULL,
  `Camion_idCamion` INT NOT NULL,
  `Bodega_idBodega` INT NOT NULL,
  `lunes` TINYINT(1) NULL,
  `martes` TINYINT(1) NULL,
  `miercoles` TINYINT(1) NULL,
  `jueves` TINYINT(1) NULL,
  `viernes` TINYINT(1) NULL,
  `sabado` TINYINT(1) NULL,
  `Empleado_Usuario_idUsuario` INT NOT NULL,
  PRIMARY KEY (`idRuta`),
  CONSTRAINT `fk_Ruta_Canton1`
    FOREIGN KEY (`Canton_idCanton1`)
    REFERENCES `general`.`Canton` (`idCanton`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Ruta_Camion1`
    FOREIGN KEY (`Camion_idCamion`)
    REFERENCES `general`.`Camion` (`idCamion`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Ruta_Bodega1`
    FOREIGN KEY (`Bodega_idBodega`)
    REFERENCES `general`.`Bodega` (`idBodega`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Ruta_Empleado1`
    FOREIGN KEY (`Empleado_Usuario_idUsuario`)
    REFERENCES `general`.`Empleado` (`Usuario_idUsuario`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_Ruta_Canton1_idx` ON `general`.`Ruta` (`Canton_idCanton1` ASC);

CREATE INDEX `fk_Ruta_Camion1_idx` ON `general`.`Ruta` (`Camion_idCamion` ASC);

CREATE INDEX `fk_Ruta_Bodega1_idx` ON `general`.`Ruta` (`Bodega_idBodega` ASC);

CREATE INDEX `fk_Ruta_Empleado1_idx` ON `general`.`Ruta` (`Empleado_Usuario_idUsuario` ASC);


-- -----------------------------------------------------
-- Table `general`.`Cliente`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `general`.`Cliente` (
  `idCliente` INT NOT NULL,
  `nombreEncargado` VARCHAR(200) NOT NULL,
  `cedula` INT NOT NULL,
  `nombreLocal` VARCHAR(200) NOT NULL,
  `correo` VARCHAR(100) NOT NULL,
  `latitud` DOUBLE NOT NULL,
  `longitud` DOUBLE NOT NULL,
  `direccion` VARCHAR(200) NOT NULL,
  `horaApertura` INT NOT NULL,
  `minutoApertura` INT NOT NULL,
  `horaCierre` INT NOT NULL,
  `minutoCierre` INT NOT NULL,
  `tiempoDescarga` INT NULL COMMENT 'En minutos.',
  PRIMARY KEY (`idCliente`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `general`.`ClienteXRuta`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `general`.`ClienteXRuta` (
  `Ruta_idRuta` INT NOT NULL,
  `Cliente_idCliente` INT NOT NULL,
  PRIMARY KEY (`Ruta_idRuta`, `Cliente_idCliente`),
  CONSTRAINT `fk_Ruta_has_Cliente_Ruta1`
    FOREIGN KEY (`Ruta_idRuta`)
    REFERENCES `general`.`Ruta` (`idRuta`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Ruta_has_Cliente_Cliente1`
    FOREIGN KEY (`Cliente_idCliente`)
    REFERENCES `general`.`Cliente` (`idCliente`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_Ruta_has_Cliente_Cliente1_idx` ON `general`.`ClienteXRuta` (`Cliente_idCliente` ASC);

CREATE INDEX `fk_Ruta_has_Cliente_Ruta1_idx` ON `general`.`ClienteXRuta` (`Ruta_idRuta` ASC);


-- -----------------------------------------------------
-- Table `general`.`TelefonosXCliente`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `general`.`TelefonosXCliente` (
  `Cliente_idCliente` INT NOT NULL,
  `telefono` INT NOT NULL,
  CONSTRAINT `fk_TelefonosXCliente_Cliente1`
    FOREIGN KEY (`Cliente_idCliente`)
    REFERENCES `general`.`Cliente` (`idCliente`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_TelefonosXCliente_Cliente1_idx` ON `general`.`TelefonosXCliente` (`Cliente_idCliente` ASC);


-- -----------------------------------------------------
-- Table `general`.`Pedido`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `general`.`Pedido` (
  `idPedido` INT NOT NULL AUTO_INCREMENT,
  `Cliente_idCliente` INT NOT NULL,
  `entregado` TINYINT(1) NULL,
  PRIMARY KEY (`idPedido`),
  CONSTRAINT `fk_Pedido_Cliente1`
    FOREIGN KEY (`Cliente_idCliente`)
    REFERENCES `general`.`Cliente` (`idCliente`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_Pedido_Cliente1_idx` ON `general`.`Pedido` (`Cliente_idCliente` ASC);


-- -----------------------------------------------------
-- Table `general`.`ArticulosXPedido`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `general`.`ArticulosXPedido` (
  `Inventario_Bodega_idBodega` INT NOT NULL,
  `Inventario_Producto_idProducto` INT NOT NULL,
  `Pedido_idPedido` INT NOT NULL,
  `cantidad` INT NOT NULL,
  PRIMARY KEY (`Inventario_Producto_idProducto`, `Pedido_idPedido`),
  CONSTRAINT `fk_Inventario_has_Pedido_Inventario1`
    FOREIGN KEY (`Inventario_Bodega_idBodega` , `Inventario_Producto_idProducto`)
    REFERENCES `general`.`Inventario` (`Bodega_idBodega` , `Producto_idProducto`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Inventario_has_Pedido_Pedido1`
    FOREIGN KEY (`Pedido_idPedido`)
    REFERENCES `general`.`Pedido` (`idPedido`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

CREATE INDEX `fk_Inventario_has_Pedido_Pedido1_idx` ON `general`.`ArticulosXPedido` (`Pedido_idPedido` ASC);

CREATE INDEX `fk_Inventario_has_Pedido_Inventario1_idx` ON `general`.`ArticulosXPedido` (`Inventario_Bodega_idBodega` ASC, `Inventario_Producto_idProducto` ASC);


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
