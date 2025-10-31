CREATE DATABASE  IF NOT EXISTS `fabrimer1` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `fabrimer1`;
-- MySQL dump 10.13  Distrib 8.0.42, for Win64 (x86_64)
--
-- Host: localhost    Database: fabrimer1
-- ------------------------------------------------------
-- Server version	8.0.42

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `clase`
--

DROP TABLE IF EXISTS `clase`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `clase` (
  `NUM_ID_CLASE` int NOT NULL,
  `VCH_NOMBRE` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `VCH_ESTADO` tinyint(1) NOT NULL DEFAULT '1',
  `FEC_FECHA_CREACION` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `FEC_FECHA_MODIFICACION` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `VCH_USER_CREACION` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `VCH_USER_MODIFICACION` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`NUM_ID_CLASE`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `imagenes`
--

DROP TABLE IF EXISTS `imagenes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `imagenes` (
  `NUM_ID_IMAGEN` int NOT NULL,
  `VCH_NOMBRE` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `NUM_ID_PRODUCTO` int NOT NULL,
  `NUM_NRO` int NOT NULL,
  `VCH_RUTA` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `VCH_NOMBRE_ARCHIVO` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `VCH_EXTENSION` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `FEC_FECHA_CREACION` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `FEC_FECHA_MODIFICACION` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `VCH_USER_CREACION` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `VCH_USER_MODIFICACION` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`NUM_ID_IMAGEN`),
  KEY `FK_IMAGEN_PRODUCTO` (`NUM_ID_PRODUCTO`),
  CONSTRAINT `FK_IMAGEN_PRODUCTO` FOREIGN KEY (`NUM_ID_PRODUCTO`) REFERENCES `producto` (`NUM_ID_PRODUCTO`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `listas`
--

DROP TABLE IF EXISTS `listas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `listas` (
  `NUM_ID_LISTA` int NOT NULL,
  `NUM_ID_TIENDA` int NOT NULL,
  `VCH_JUEGO` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `VCH_CODIGO` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `VCH_DESCRIPCION` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `VCH_ESTADO` tinyint(1) NOT NULL DEFAULT '1',
  `FEC_FECHA_CREACION` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `FEC_FECHA_MODIFICACION` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `VCH_USER_CREACION` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `VCH_USER_MODIFICACION` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`NUM_ID_LISTA`),
  KEY `FK_LISTA_TIENDA` (`NUM_ID_TIENDA`),
  CONSTRAINT `FK_LISTA_TIENDA` FOREIGN KEY (`NUM_ID_TIENDA`) REFERENCES `tienda` (`NUM_ID_TIENDA`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `plant_detalle`
--

DROP TABLE IF EXISTS `plant_detalle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `plant_detalle` (
  `NUM_ID_DET_PLANTILLA` int NOT NULL,
  `NUM_ID_PLANTILLA` int NOT NULL,
  `VCH_GRUPO` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `VCH_CAMPO` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `VCH_NOMBRE_PLANTILLA` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `VCH_DESCRIPCION` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `VCH_JUEGO` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `VCH_CODIGO` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `VCH_ESTADO` tinyint(1) NOT NULL DEFAULT '1',
  `VCH_OBLIGATORIO` tinyint(1) NOT NULL DEFAULT '0',
  `NUM_ORDEN` int NOT NULL,
  `FEC_FECHA_CREACION` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `FEC_FECHA_MODIFICACION` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `VCH_USER_CREACION` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `VCH_USER_MODIFICACION` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`NUM_ID_DET_PLANTILLA`),
  KEY `FK_PLANT_DETALLE_PLANTILLA` (`NUM_ID_PLANTILLA`),
  CONSTRAINT `FK_PLANT_DETALLE_PLANTILLA` FOREIGN KEY (`NUM_ID_PLANTILLA`) REFERENCES `plantilla` (`NUM_ID_PLANTILLA`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `plantilla`
--

DROP TABLE IF EXISTS `plantilla`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `plantilla` (
  `NUM_ID_PLANTILLA` int NOT NULL,
  `NUM_ID_TIENDA` int NOT NULL,
  `NUM_ID_CLASE` int NOT NULL,
  `VCH_CATEGORIA_N1` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `VCH_CATEGORIA_N2` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `VCH_CATEGORIA_N3` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `VCH_CATEGORIA_N4` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `VCH_CATEGORIA_N5` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `VCH_ESTADO` tinyint(1) NOT NULL DEFAULT '1',
  `FEC_FECHA_CREACION` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `FEC_FECHA_MODIFICACION` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `VCH_USER_CREACION` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `VCH_USER_MODIFICACION` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`NUM_ID_PLANTILLA`),
  KEY `FK_PLANTILLA_TIENDA` (`NUM_ID_TIENDA`),
  KEY `FK_PLANTILLA_CLASE` (`NUM_ID_CLASE`),
  CONSTRAINT `FK_PLANTILLA_CLASE` FOREIGN KEY (`NUM_ID_CLASE`) REFERENCES `clase` (`NUM_ID_CLASE`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `FK_PLANTILLA_TIENDA` FOREIGN KEY (`NUM_ID_TIENDA`) REFERENCES `tienda` (`NUM_ID_TIENDA`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `producto`
--

DROP TABLE IF EXISTS `producto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `producto` (
  `NUM_ID_PRODUCTO` int NOT NULL,
  `NUM_STOCK` int NOT NULL,
  `NUM_ID_CLASE` int NOT NULL,
  `VCH_NOMBRE` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `VCH_MARCA` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `VCH_MODELO` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `VCH_DESCRIPCION` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `VCH_CATEGORIA_PRIMARIA` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `VCH_PAIS_PRODUCCION` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `VCH_BASIC_COLOR` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `VCH_COLOR` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `VCH_SIZE` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `VCH_SKU_VENDEDOR` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `VCH_CODIGO_BARRAS` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `VCH_SKU_PADRE` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `NUM_QUANTITY_FALABELLA` int NOT NULL,
  `NUM_PRICE_FALABELLA` decimal(18,2) NOT NULL,
  `NUM_SALE_PRICE_FALABELLA` decimal(18,2) NOT NULL,
  `FEC_SALE_START_DATE` datetime NOT NULL,
  `FEC_SALE_END_DATE` datetime NOT NULL,
  `VCH_FIT` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `VCH_COSTUME_GENRE` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `VCH_PANTS_TYPE` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `VCH_COMPOSITION` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `VCH_MATERIAL_VESTUARIO` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `VCH_CONDICION_PRODUCTO` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `VCH_GARANTIA_PRODUCTO` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `VCH_GARANTIA_VENDEDOR` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `VCH_CONTENIDO_PAQUETE` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `NUM_ANCHO_PAQUETE` decimal(18,2) NOT NULL,
  `NUM_LARGO_PAQUETE` decimal(18,2) NOT NULL,
  `NUM_ALTO_PAQUETE` decimal(18,2) NOT NULL,
  `NUM_PESO_PAQUETE` decimal(18,2) NOT NULL,
  `VCH_IMAGEN_PRINCIPAL` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `VCH_IMAGEN2` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `VCH_IMAGEN3` text COLLATE utf8mb4_unicode_ci,
  `VCH_IMAGEN4` text COLLATE utf8mb4_unicode_ci,
  `VCH_IMAGEN5` text COLLATE utf8mb4_unicode_ci,
  `VCH_IMAGEN6` text COLLATE utf8mb4_unicode_ci,
  `VCH_IMAGEN7` text COLLATE utf8mb4_unicode_ci,
  `VCH_IMAGEN8` text COLLATE utf8mb4_unicode_ci,
  `VCH_MONEDA` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `VCH_TIPO_PUBLI` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `VCH_FORM_ENV` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `VCH_COSTO_EN` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `VCH_RETIRO` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `VCH_PESO_PROD` decimal(18,2) NOT NULL,
  `VCH_LONG_PROD` decimal(18,2) NOT NULL,
  `VCH_ANCHO_PROD` decimal(18,2) NOT NULL,
  `VCH_ALTURA_PROD` decimal(18,2) NOT NULL,
  `VCH_TIPO_CUE` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `VCH_TIPO_PUN` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `VCH_TIPO_CIER` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `VCH_TIPO_GARANT` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `VCH_TABLA_TALLA` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `VCH_TAMANIO_PROD` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `FEC_FECHA_CREACION` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `FEC_FECHA_MODIFICACION` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `VCH_USER_CREACION` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `VCH_USER_MODIFICACION` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `VCH_TEMPORADA` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`NUM_ID_PRODUCTO`),
  KEY `FK_PRODUCTO_CLASE` (`NUM_ID_CLASE`),
  CONSTRAINT `FK_PRODUCTO_CLASE` FOREIGN KEY (`NUM_ID_CLASE`) REFERENCES `clase` (`NUM_ID_CLASE`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tienda`
--

DROP TABLE IF EXISTS `tienda`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tienda` (
  `NUM_ID_TIENDA` int NOT NULL,
  `VCH_TIENDA` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `VCH_ESTADO` tinyint(1) NOT NULL DEFAULT '1',
  `FEC_FECHA_CREACION` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `FEC_FECHA_MODIFICACION` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `VCH_USER_CREACION` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `VCH_USER_MODIFICACION` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`NUM_ID_TIENDA`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `usuario`
--

DROP TABLE IF EXISTS `usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuario` (
  `usuario_id` int NOT NULL AUTO_INCREMENT,
  `usuario_nombre` varchar(70) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish2_ci NOT NULL,
  `usuario_apellido` varchar(70) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish2_ci NOT NULL,
  `usuario_email` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish2_ci NOT NULL,
  `usuario_usuario` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish2_ci NOT NULL,
  `usuario_clave` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish2_ci NOT NULL,
  `usuario_foto` varchar(535) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish2_ci NOT NULL,
  `usuario_creado` timestamp NOT NULL,
  `usuario_actualizado` timestamp NOT NULL,
  PRIMARY KEY (`usuario_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping routines for database 'fabrimer1'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-10-30 22:01:39
