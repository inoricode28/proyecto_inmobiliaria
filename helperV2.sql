-- MySQL dump 10.13  Distrib 8.0.33, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: helper
-- ------------------------------------------------------
-- Server version	8.0.30

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `activities`
--

DROP TABLE IF EXISTS `activities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `activities` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activities`
--

/*!40000 ALTER TABLE `activities` DISABLE KEYS */;
INSERT INTO `activities` VALUES (1,'Programming','Programming related activities','2025-07-04 10:02:32','2025-07-04 10:02:32',NULL),(2,'Testing','Testing related activities','2025-07-04 10:02:32','2025-07-04 10:02:32',NULL),(3,'Learning','Activities related to learning and training','2025-07-04 10:02:32','2025-07-04 10:02:32',NULL),(4,'Research','Activities related to research','2025-07-04 10:02:32','2025-07-04 10:02:32',NULL),(5,'Other','Other activities','2025-07-04 10:02:32','2025-07-04 10:02:32',NULL);
/*!40000 ALTER TABLE `activities` ENABLE KEYS */;

--
-- Table structure for table `departamentos`
--

-- Elimina la tabla si ya existe
DROP TABLE IF EXISTS `departamentos`;

-- Crear tabla departamentos con relación a tipo_financiamiento
CREATE TABLE `departamentos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `proyecto_id` bigint unsigned DEFAULT NULL,
  `centro_costos` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `edificio_id` bigint unsigned NOT NULL,
  `tipo_inmueble_id` bigint unsigned DEFAULT NULL,
  `tipo_departamento_id` bigint unsigned DEFAULT NULL,
  `estado_departamento_id` bigint unsigned DEFAULT NULL,
  `tipos_financiamiento_id` bigint unsigned DEFAULT NULL, 
  `numero_inicial` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numero_final` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ficha_indep` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `num_departamento` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `num_piso` smallint unsigned DEFAULT NULL,
  `num_dormitorios` tinyint unsigned DEFAULT NULL,
  `num_bano` tinyint unsigned DEFAULT NULL,
  `num_certificado` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bono_techo_propio` tinyint(1) NOT NULL DEFAULT '0',
  `num_bono_tp` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cantidad_uit` decimal(10,2) DEFAULT NULL,
  `codigo_bancario` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `codigo_catastral` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vista_id` bigint unsigned DEFAULT NULL,
  `orden` smallint unsigned DEFAULT NULL,
  `moneda_id` bigint unsigned DEFAULT NULL,
  `precio` decimal(12,2) DEFAULT NULL,
  `Precio_lista` decimal(12,2) DEFAULT NULL,
  `Precio_venta` decimal(12,2) DEFAULT NULL,
  `descuento` decimal(5,2) DEFAULT NULL COMMENT 'Porcentaje de descuento',
  `predio_m2` decimal(10,2) DEFAULT NULL,
  `terreno` decimal(10,2) DEFAULT NULL,
  `techada` decimal(10,2) DEFAULT NULL,
  `construida` decimal(10,2) DEFAULT NULL,
  `terraza` decimal(10,2) DEFAULT NULL,
  `jardin` decimal(10,2) DEFAULT NULL,
  `adicional` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vendible` tinyint(1) NOT NULL DEFAULT '1',
  `frente` decimal(10,2) DEFAULT NULL,
  `derecha` decimal(10,2) DEFAULT NULL,
  `izquierda` decimal(10,2) DEFAULT NULL,
  `fondo` decimal(10,2) DEFAULT NULL,
  `direccion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `observaciones` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `estado_id` bigint unsigned NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `departamentos_proyecto_id_foreign` (`proyecto_id`),
  KEY `departamentos_edificio_id_foreign` (`edificio_id`),
  KEY `departamentos_tipo_inmueble_id_foreign` (`tipo_inmueble_id`),
  KEY `departamentos_tipo_departamento_id_foreign` (`tipo_departamento_id`),
  KEY `departamentos_estado_departamento_id_foreign` (`estado_departamento_id`),
  KEY `departamentos_tipo_financiamiento_id_foreign` (`tipos_financiamiento_id`), 
  KEY `departamentos_moneda_id_foreign` (`moneda_id`),
  KEY `departamentos_estado_id_foreign` (`estado_id`),
  CONSTRAINT `departamentos_edificio_id_foreign` FOREIGN KEY (`edificio_id`) REFERENCES `edificios` (`id`),
  CONSTRAINT `departamentos_estado_departamento_id_foreign` FOREIGN KEY (`estado_departamento_id`) REFERENCES `estados_departamento` (`id`),
  CONSTRAINT `departamentos_estado_id_foreign` FOREIGN KEY (`estado_id`) REFERENCES `estado` (`id`),
  CONSTRAINT `departamentos_moneda_id_foreign` FOREIGN KEY (`moneda_id`) REFERENCES `moneda` (`id`),
  CONSTRAINT `departamentos_proyecto_id_foreign` FOREIGN KEY (`proyecto_id`) REFERENCES `proyectos` (`id`),
  CONSTRAINT `departamentos_tipo_departamento_id_foreign` FOREIGN KEY (`tipo_departamento_id`) REFERENCES `tipos_departamento` (`id`),
  CONSTRAINT `departamentos_tipo_inmueble_id_foreign` FOREIGN KEY (`tipo_inmueble_id`) REFERENCES `tipo_inmueble` (`id`),
  CONSTRAINT `departamentos_vista_id_foreign` FOREIGN KEY (`vista_id`) REFERENCES `vistas` (`id`),
  CONSTRAINT `departamentos_tipo_financiamiento_id_foreign` FOREIGN KEY (`tipos_financiamiento_id`) REFERENCES `tipo_financiamiento` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `departamentos`
--

/*!40000 ALTER TABLE `departamentos` DISABLE KEYS */;
INSERT INTO `departamentos` (
    `id`, `proyecto_id`, `centro_costos`, `edificio_id`, `tipo_inmueble_id`, `tipo_departamento_id`,
    `estado_departamento_id`, `tipos_financiamiento_id`, `numero_inicial`, `numero_final`, `ficha_indep`,
    `num_departamento`, `num_piso`, `num_dormitorios`, `num_bano`, `num_certificado`, `bono_techo_propio`,
    `num_bono_tp`, `cantidad_uit`, `codigo_bancario`, `codigo_catastral`, `vista_id`, `orden`, `moneda_id`,
    `precio`, `Precio_lista`, `Precio_venta`, `descuento`, `predio_m2`, `terreno`, `techada`, `construida`,
    `terraza`, `jardin`, `adicional`, `vendible`, `frente`, `derecha`, `izquierda`, `fondo`,
    `direccion`, `observaciones`, `estado_id`, `created_at`, `updated_at`, `deleted_at`
) VALUES
(1,1,NULL,1,1,1,2,1,'101','150','1011','101',1,100,10,'999',1,'2112',NULL,'01','01',1,22,1,12323.00,2323.00,32323.00,32.00,23.00,323.00,32.00,232.00,22.00,22.00,NULL,1,22.00,22.00,22.00,22.00,'calle sullana 1987 urb chacra rios norte cercado de lima lima peru','223',1,'2025-07-04 10:05:40','2025-07-04 10:05:40',NULL),
(2,1,NULL,1,1,1,4,2,'101','150','1011','201',2,3,2,'999',0,NULL,NULL,'01','01',1,222,1,3422.00,222.00,222.00,222.00,222.00,222.00,222.00,222.00,222.00,222.00,NULL,1,222.00,222.00,222.00,222.00,'calle sullana 1987 urb chacra rios norte cercado de lima lima peru','222',1,'2025-07-04 10:06:45','2025-07-04 11:10:52',NULL),
(3,1,NULL,1,1,1,2,3,'101','150','1011','102',1,33,33,'33',0,NULL,NULL,'33','33',1,33,1,33333.00,33.00,33.00,33.00,33.00,33.00,33.00,33.00,33.00,33.00,NULL,1,33.00,33.00,33.00,33.00,'333','333',1,'2025-07-04 10:07:45','2025-07-04 10:07:45',NULL),
(4,1,NULL,1,1,1,2,4,'001','345','Independiente','103',1,5,3,'455454',1,'432432',NULL,'450565','458151',1,4522,1,350000.00,300000.00,350000.00,10.00,75.00,75.00,75.00,75.00,75.00,75.00,NULL,1,75.00,75.00,75.00,75.00,'jr.las coraleinas 458','thhtr',1,'2025-07-04 11:40:20','2025-07-04 11:40:20',NULL),
(5,1,NULL,1,1,2,9,5,'001','345','Independiente','202',2,5,3,'455454',1,'213544',NULL,'24255','34465',1,25453,1,350000.00,200000.00,350000.00,10.00,75.00,75.00,75.00,75.00,75.00,75.00,NULL,1,75.00,75.00,75.00,75.00,'jr.las coraleinas 458','trgtf',1,'2025-07-04 11:48:41','2025-07-04 11:48:41',NULL),
(6,1,NULL,1,1,3,4,6,'001','345','1011','301',3,5,3,'455454',1,'6576',NULL,'43543','654365',1,75,1,350000.00,300000.00,350000.00,10.00,75.00,75.00,75.00,75.00,75.00,75.00,NULL,1,75.00,75.00,75.00,75.00,'jr.las coraleinas 458','yth',1,'2025-07-04 11:51:32','2025-07-04 11:51:32',NULL),
(7,1,NULL,1,1,2,2,7,'001','200','Independiente','401',4,5,3,'455454',1,'567678',NULL,'4565465','46575',1,8655,1,300000.00,350000.00,350000.00,10.00,75.00,75.00,75.00,75.00,75.00,75.00,NULL,1,75.00,75.00,75.00,57.00,'jr.las coraleinas 458','kjhgfjhg',1,'2025-07-04 11:53:55','2025-07-04 11:53:55',NULL),
(8,1,NULL,1,1,3,2,8,'001','345','Independiente','501',5,5,3,'455454',1,NULL,NULL,'54353','5436',2,54,1,350000.00,300000.00,350000.00,7.00,75.00,75.00,75.00,75.00,75.00,75.00,NULL,1,75.00,75.00,75.00,75.00,'jr.las coraleinas 458','jhkjh',1,'2025-07-04 12:01:10','2025-07-04 12:01:10',NULL),
(9,1,NULL,1,1,1,9,9,'001','345','Independiente','601',6,5,3,'455454',1,'432432',NULL,'4654','657654',2,45,1,350000.00,300000.00,350000.00,10.00,75.00,75.00,75.00,75.00,75.00,75.00,NULL,1,75.00,75.00,75.00,75.00,'jr.las coraleinas 458','hgjhg',1,'2025-07-04 12:31:40','2025-07-04 12:31:40',NULL),
(10,1,NULL,1,1,1,3,10,'001','345','Independiente','701',7,5,3,'455454',1,'465765',NULL,'354654','34564',1,78,1,350000.00,300000.00,350000.00,10.00,75.00,75.00,75.00,75.00,75.00,75.00,NULL,1,75.00,75.00,75.00,75.00,'jr.las coraleinas 458','ghfhgfhg',1,'2025-07-04 12:36:28','2025-07-04 12:36:28',NULL);
/*!40000 ALTER TABLE `departamentos` ENABLE KEYS */;

--
-- Table structure for table `edificios`
--

DROP TABLE IF EXISTS `edificios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `edificios` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `proyecto_id` bigint unsigned NOT NULL,
  `nombre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cantidad_pisos` int DEFAULT NULL,
  `cantidad_departamentos` int DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_entrega` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `edificios_proyecto_id_foreign` (`proyecto_id`),
  CONSTRAINT `edificios_proyecto_id_foreign` FOREIGN KEY (`proyecto_id`) REFERENCES `proyectos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `edificios`
--

/*!40000 ALTER TABLE `edificios` DISABLE KEYS */;
INSERT INTO `edificios` VALUES (1,1,'EDIFICIO 1','dfdfd',10,100,'2025-07-04','2025-07-31','2025-07-04 10:04:34','2025-07-04 10:04:34');
/*!40000 ALTER TABLE `edificios` ENABLE KEYS */;

--
-- Table structure for table `empresas`
--

DROP TABLE IF EXISTS `empresas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `empresas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ruc` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `direccion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `telefono` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `representante_legal` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `empresas_ruc_unique` (`ruc`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empresas`
--

/*!40000 ALTER TABLE `empresas` DISABLE KEYS */;
INSERT INTO `empresas` VALUES (1,'serven vicava sac','20609018997','calle sullana 1987 urb chacra rios norte cercado de lima lima peru','951768369','serven_vicava@hotmail.com','Luis','2025-07-04 10:03:43','2025-07-04 11:32:03');
/*!40000 ALTER TABLE `empresas` ENABLE KEYS */;

--
-- Table structure for table `epics`
--

DROP TABLE IF EXISTS `epics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `epics` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `project_id` bigint unsigned NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `starts_at` date NOT NULL,
  `ends_at` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `parent_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `epics_project_id_foreign` (`project_id`),
  KEY `epics_parent_id_foreign` (`parent_id`),
  CONSTRAINT `epics_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `epics` (`id`),
  CONSTRAINT `epics_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `epics`
--

/*!40000 ALTER TABLE `epics` DISABLE KEYS */;
/*!40000 ALTER TABLE `epics` ENABLE KEYS */;

--
-- Table structure for table `estado`
--

DROP TABLE IF EXISTS `estado`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `estado` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `estado`
--

/*!40000 ALTER TABLE `estado` DISABLE KEYS */;
INSERT INTO `estado` VALUES (1,1),(2,0);
/*!40000 ALTER TABLE `estado` ENABLE KEYS */;

--
-- Table structure for table `estados_departamento`
--

DROP TABLE IF EXISTS `estados_departamento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `estados_departamento` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `color` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#6b7280',
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `estados_departamento_nombre_unique` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `estados_departamento`
--

/*!40000 ALTER TABLE `estados_departamento` DISABLE KEYS */;
INSERT INTO `estados_departamento` VALUES (1,'Bloqueado','Departamento bloqueado temporalmente','#ef4444',0,NULL,NULL),(2,'Disponible','Departamento disponible para venta','#10b981',1,NULL,NULL),(3,'Separacion Temporal','Separado temporalmente por cliente','#f59e0b',0,NULL,NULL),(4,'Separacion','Separado definitivamente por cliente','#f97316',0,NULL,NULL),(5,'Pagado sin minuta','Pagado sin minuta firmada','#8b5cf6',0,NULL,NULL),(6,'Minuta','Minuta firmada','#6366f1',0,NULL,NULL),(7,'Cancelado','Venta cancelada','#64748b',0,NULL,NULL),(8,'Listo Entrega','Listo para entrega al cliente','#06b6d4',0,NULL,NULL),(9,'Entregado','Entregado al cliente','#14b8a6',0,NULL,NULL);
/*!40000 ALTER TABLE `estados_departamento` ENABLE KEYS */;

--
-- Table structure for table `estados_proyecto`
--

DROP TABLE IF EXISTS `estados_proyecto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `estados_proyecto` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `estados_proyecto_nombre_unique` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `estados_proyecto`
--

/*!40000 ALTER TABLE `estados_proyecto` DISABLE KEYS */;
INSERT INTO `estados_proyecto` VALUES (1,'Planificado','El proyecto está en la fase de planificación, aún no se ha iniciado la construcción.'),(2,'Construcción','El proyecto está en proceso de construcción.'),(3,'Terminado','El proyecto ha sido completado y está finalizado.');
/*!40000 ALTER TABLE `estados_proyecto` ENABLE KEYS */;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;

--
-- Table structure for table `foto_departamentos`
--

DROP TABLE IF EXISTS `foto_departamentos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `foto_departamentos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `proyecto_id` bigint unsigned NOT NULL,
  `edificio_id` bigint unsigned NOT NULL,
  `departamento_id` bigint unsigned NOT NULL,
  `imagen` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `foto_departamentos_proyecto_id_foreign` (`proyecto_id`),
  KEY `foto_departamentos_edificio_id_foreign` (`edificio_id`),
  KEY `foto_departamentos_departamento_id_foreign` (`departamento_id`),
  CONSTRAINT `foto_departamentos_departamento_id_foreign` FOREIGN KEY (`departamento_id`) REFERENCES `departamentos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `foto_departamentos_edificio_id_foreign` FOREIGN KEY (`edificio_id`) REFERENCES `edificios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `foto_departamentos_proyecto_id_foreign` FOREIGN KEY (`proyecto_id`) REFERENCES `proyectos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `foto_departamentos`
--

/*!40000 ALTER TABLE `foto_departamentos` DISABLE KEYS */;
INSERT INTO `foto_departamentos` VALUES (1,1,1,1,'departamentos/194838.jpg','2025-07-04 10:08:29','2025-07-04 10:58:23'),(2,1,1,2,'departamentos/194838.jpg','2025-07-04 10:12:48','2025-07-04 10:57:59'),(3,1,1,3,'departamentos/194838.jpg','2025-07-04 11:04:51','2025-07-04 11:04:51'),(4,1,1,4,'departamentos/194844.jpg','2025-07-04 11:41:11','2025-07-04 12:14:39'),(5,1,1,3,'departamentos/194847.jpg','2025-07-04 12:19:09','2025-07-04 12:19:09'),(6,1,1,5,'departamentos/194847.jpg','2025-07-04 12:20:13','2025-07-04 12:20:13'),(7,1,1,6,'departamentos/194842.jpg','2025-07-04 12:21:25','2025-07-04 12:21:25'),(8,1,1,8,'departamentos/194852.jpg','2025-07-04 12:23:12','2025-07-04 12:23:12'),(9,1,1,7,'departamentos/194855.jpg','2025-07-04 12:24:06','2025-07-04 12:24:06'),(10,1,1,9,'departamentos/194853.jpg','2025-07-04 12:49:29','2025-07-04 12:49:29'),(11,1,1,10,'departamentos/194850.jpg','2025-07-04 12:50:00','2025-07-04 12:50:00'),(12,1,1,10,'departamentos/194857.jpg','2025-07-04 12:51:42','2025-07-04 12:51:42'),(13,1,1,11,'departamentos/194863.jpg','2025-07-04 12:52:31','2025-07-04 12:52:31'),(14,1,1,12,'departamentos/194861.jpg','2025-07-04 12:53:29','2025-07-04 12:53:29');
/*!40000 ALTER TABLE `foto_departamentos` ENABLE KEYS */;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;

--
-- Table structure for table `media`
--

DROP TABLE IF EXISTS `media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `media` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `collection_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `mime_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `disk` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `conversions_disk` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `size` bigint unsigned NOT NULL,
  `manipulations` json NOT NULL,
  `custom_properties` json NOT NULL,
  `generated_conversions` json NOT NULL,
  `responsive_images` json NOT NULL,
  `order_column` int unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `media_uuid_unique` (`uuid`),
  KEY `media_model_type_model_id_index` (`model_type`,`model_id`),
  KEY `media_order_column_index` (`order_column`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `media`
--

/*!40000 ALTER TABLE `media` DISABLE KEYS */;
/*!40000 ALTER TABLE `media` ENABLE KEYS */;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=74 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2014_10_12_000000_create_users_table',1),(2,'2014_10_12_100000_create_password_resets_table',1),(3,'2019_08_19_000000_create_failed_jobs_table',1),(4,'2019_12_14_000001_create_personal_access_tokens_table',1),(5,'2022_11_02_111430_add_two_factor_columns_to_table',1),(6,'2022_11_02_113007_create_permission_tables',1),(7,'2022_11_02_124027_create_project_statuses_table',1),(8,'2022_11_02_124028_create_projects_table',1),(9,'2022_11_02_131753_create_project_users_table',1),(10,'2022_11_02_134510_create_media_table',1),(11,'2022_11_02_152359_create_project_favorites_table',1),(12,'2022_11_02_193241_create_ticket_statuses_table',1),(13,'2022_11_02_193242_create_tickets_table',1),(14,'2022_11_06_155109_add_tickets_prefix_to_projects',1),(15,'2022_11_06_163226_add_code_to_tickets',1),(16,'2022_11_06_164004_create_ticket_types_table',1),(17,'2022_11_06_165400_add_type_to_ticket',1),(18,'2022_11_06_173220_add_order_to_tickets',1),(19,'2022_11_06_184448_add_order_to_ticket_statuses',1),(20,'2022_11_06_193051_create_ticket_activities_table',1),(21,'2022_11_06_194000_create_ticket_priorities_table',1),(22,'2022_11_06_194728_add_priority_to_tickets',1),(23,'2022_11_06_203702_add_status_type_to_project',1),(24,'2022_11_06_204227_add_project_to_ticket_statuses',1),(25,'2022_11_07_064347_create_ticket_comments_table',1),(26,'2022_11_08_084509_create_ticket_subscribers_table',1),(27,'2022_11_08_144611_create_notifications_table',1),(28,'2022_11_08_150309_create_jobs_table',1),(29,'2022_11_08_163244_create_ticket_relations_table',1),(30,'2022_11_08_172846_create_settings_table',1),(31,'2022_11_08_173004_general_settings',1),(32,'2022_11_08_173852_create_general_settings',1),(33,'2022_11_09_085506_create_socialite_users_table',1),(34,'2022_11_09_085638_make_user_password_nullable',1),(35,'2022_11_09_110740_remove_unique_from_users',1),(36,'2022_11_09_110955_add_soft_deletes_to_users',1),(37,'2022_11_09_173852_add_social_login_to_general_settings',1),(38,'2022_11_10_193214_create_ticket_hours_table',1),(39,'2022_11_10_200608_add_estimation_to_tickets',1),(40,'2022_11_12_134201_add_creation_token_to_users',1),(41,'2022_11_12_142644_create_pending_user_emails_table',1),(42,'2022_11_12_173852_add_default_role_to_general_settings',1),(43,'2022_11_12_173852_add_login_form_oidc_enabled_flags_to_general_settings',1),(44,'2022_11_12_173852_add_site_language_to_general_settings',1),(45,'2022_12_15_100852_create_epics_table',1),(46,'2022_12_15_101035_add_epic_to_ticket',1),(47,'2022_12_16_133836_add_parent_to_epics',1),(48,'2022_12_27_082239_add_comment_to_ticket_hours',1),(49,'2023_01_05_182946_add_attachments_to_tickets',1),(50,'2023_01_09_113159_create_activities_table',1),(51,'2023_01_09_113847_add_activity_to_ticket_hours_table',1),(52,'2023_01_12_203211_remove_unique_constraint_from_users',1),(53,'2023_01_12_204221_drop_attachments',1),(54,'2023_01_15_201358_add_type_to_projects',1),(55,'2023_01_15_202225_create_sprints_table',1),(56,'2023_01_15_204606_add_sprint_to_tickets',1),(57,'2023_01_15_214849_add_epic_to_sprints',1),(58,'2023_01_16_085329_add_started_ended_at_to_sprints',1),(59,'2023_01_24_084637_update_users_for_oidc',1),(60,'2023_04_10_123922_add_unique_ticket_prefix_to_projects_table',1),(61,'2025_06_15_233914_create_tipo_inmueble_table',1),(62,'2025_06_15_234227_create_vistas_table',1),(63,'2025_06_15_234233_create_moneda_table',1),(64,'2025_06_15_234238_create_estado_table',1),(65,'2025_06_17_173712_create_estados_proyecto_table',1),(66,'2025_06_17_174336_create_empresas_table',1),(67,'2025_06_17_174503_create_proyectos_table',1),(68,'2025_06_17_174715_create_edificios_table',1),(69,'2025_06_17_174836_create_tipos_departamento_table',1),(70,'2025_06_17_174925_create_estados_departamento_table',1),(71,'2025_06_17_175030_create_departamentos_table',1),(72,'2025_06_19_222520_create_foto_departamentos_table',1),(73,'2025_07_04_024703_create_tipos_financiamiento_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;

--
-- Table structure for table `model_has_permissions`
--

DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_permissions`
--

/*!40000 ALTER TABLE `model_has_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `model_has_permissions` ENABLE KEYS */;

--
-- Table structure for table `model_has_roles`
--

DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_roles` (
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_roles`
--

/*!40000 ALTER TABLE `model_has_roles` DISABLE KEYS */;
INSERT INTO `model_has_roles` VALUES (1,'App\\Models\\User',1);
/*!40000 ALTER TABLE `model_has_roles` ENABLE KEYS */;

--
-- Table structure for table `moneda`
--

DROP TABLE IF EXISTS `moneda`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `moneda` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `moneda`
--

/*!40000 ALTER TABLE `moneda` DISABLE KEYS */;
INSERT INTO `moneda` VALUES (1,'Soles'),(2,'Dólares');
/*!40000 ALTER TABLE `moneda` ENABLE KEYS */;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_id` bigint unsigned NOT NULL,
  `data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_resets` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_resets`
--

/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;

--
-- Table structure for table `pending_user_emails`
--

DROP TABLE IF EXISTS `pending_user_emails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pending_user_emails` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pending_user_emails_user_type_user_id_index` (`user_type`,`user_id`),
  KEY `pending_user_emails_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pending_user_emails`
--

/*!40000 ALTER TABLE `pending_user_emails` DISABLE KEYS */;
/*!40000 ALTER TABLE `pending_user_emails` ENABLE KEYS */;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,'List permissions','web','2025-07-04 10:02:32','2025-07-04 10:02:32'),(2,'View permission','web','2025-07-04 10:02:32','2025-07-04 10:02:32'),(3,'Create permission','web','2025-07-04 10:02:32','2025-07-04 10:02:32'),(4,'Update permission','web','2025-07-04 10:02:32','2025-07-04 10:02:32'),(5,'Delete permission','web','2025-07-04 10:02:32','2025-07-04 10:02:32'),(6,'List projects','web','2025-07-04 10:02:32','2025-07-04 10:02:32'),(7,'View project','web','2025-07-04 10:02:32','2025-07-04 10:02:32'),(8,'Create project','web','2025-07-04 10:02:32','2025-07-04 10:02:32'),(9,'Update project','web','2025-07-04 10:02:32','2025-07-04 10:02:32'),(10,'Delete project','web','2025-07-04 10:02:32','2025-07-04 10:02:32'),(11,'List project statuses','web','2025-07-04 10:02:32','2025-07-04 10:02:32'),(12,'View project status','web','2025-07-04 10:02:32','2025-07-04 10:02:32'),(13,'Create project status','web','2025-07-04 10:02:32','2025-07-04 10:02:32'),(14,'Update project status','web','2025-07-04 10:02:32','2025-07-04 10:02:32'),(15,'Delete project status','web','2025-07-04 10:02:32','2025-07-04 10:02:32'),(16,'List roles','web','2025-07-04 10:02:32','2025-07-04 10:02:32'),(17,'View role','web','2025-07-04 10:02:32','2025-07-04 10:02:32'),(18,'Create role','web','2025-07-04 10:02:32','2025-07-04 10:02:32'),(19,'Update role','web','2025-07-04 10:02:32','2025-07-04 10:02:32'),(20,'Delete role','web','2025-07-04 10:02:32','2025-07-04 10:02:32'),(21,'List tickets','web','2025-07-04 10:02:32','2025-07-04 10:02:32'),(22,'View ticket','web','2025-07-04 10:02:32','2025-07-04 10:02:32'),(23,'Create ticket','web','2025-07-04 10:02:32','2025-07-04 10:02:32'),(24,'Update ticket','web','2025-07-04 10:02:32','2025-07-04 10:02:32'),(25,'Delete ticket','web','2025-07-04 10:02:32','2025-07-04 10:02:32'),(26,'List ticket priorities','web','2025-07-04 10:02:32','2025-07-04 10:02:32'),(27,'View ticket priority','web','2025-07-04 10:02:32','2025-07-04 10:02:32'),(28,'Create ticket priority','web','2025-07-04 10:02:32','2025-07-04 10:02:32'),(29,'Update ticket priority','web','2025-07-04 10:02:32','2025-07-04 10:02:32'),(30,'Delete ticket priority','web','2025-07-04 10:02:32','2025-07-04 10:02:32'),(31,'List ticket statuses','web','2025-07-04 10:02:32','2025-07-04 10:02:32'),(32,'View ticket status','web','2025-07-04 10:02:32','2025-07-04 10:02:32'),(33,'Create ticket status','web','2025-07-04 10:02:32','2025-07-04 10:02:32'),(34,'Update ticket status','web','2025-07-04 10:02:32','2025-07-04 10:02:32'),(35,'Delete ticket status','web','2025-07-04 10:02:32','2025-07-04 10:02:32'),(36,'List ticket types','web','2025-07-04 10:02:32','2025-07-04 10:02:32'),(37,'View ticket type','web','2025-07-04 10:02:32','2025-07-04 10:02:32'),(38,'Create ticket type','web','2025-07-04 10:02:32','2025-07-04 10:02:32'),(39,'Update ticket type','web','2025-07-04 10:02:32','2025-07-04 10:02:32'),(40,'Delete ticket type','web','2025-07-04 10:02:32','2025-07-04 10:02:32'),(41,'List users','web','2025-07-04 10:02:32','2025-07-04 10:02:32'),(42,'View user','web','2025-07-04 10:02:32','2025-07-04 10:02:32'),(43,'Create user','web','2025-07-04 10:02:32','2025-07-04 10:02:32'),(44,'Update user','web','2025-07-04 10:02:32','2025-07-04 10:02:32'),(45,'Delete user','web','2025-07-04 10:02:32','2025-07-04 10:02:32'),(46,'List activities','web','2025-07-04 10:02:32','2025-07-04 10:02:32'),(47,'View activity','web','2025-07-04 10:02:32','2025-07-04 10:02:32'),(48,'Create activity','web','2025-07-04 10:02:32','2025-07-04 10:02:32'),(49,'Update activity','web','2025-07-04 10:02:32','2025-07-04 10:02:32'),(50,'Delete activity','web','2025-07-04 10:02:32','2025-07-04 10:02:32'),(51,'List sprints','web','2025-07-04 10:02:32','2025-07-04 10:02:32'),(52,'View sprint','web','2025-07-04 10:02:32','2025-07-04 10:02:32'),(53,'Create sprint','web','2025-07-04 10:02:32','2025-07-04 10:02:32'),(54,'Update sprint','web','2025-07-04 10:02:32','2025-07-04 10:02:32'),(55,'Delete sprint','web','2025-07-04 10:02:32','2025-07-04 10:02:32'),(56,'Manage general settings','web','2025-07-04 10:02:32','2025-07-04 10:02:32'),(57,'Import from Jira','web','2025-07-04 10:02:32','2025-07-04 10:02:32'),(58,'List timesheet data','web','2025-07-04 10:02:32','2025-07-04 10:02:32'),(59,'View timesheet dashboard','web','2025-07-04 10:02:32','2025-07-04 10:02:32');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;

--
-- Table structure for table `project_favorites`
--

DROP TABLE IF EXISTS `project_favorites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `project_favorites` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `project_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `project_favorites_user_id_foreign` (`user_id`),
  KEY `project_favorites_project_id_foreign` (`project_id`),
  CONSTRAINT `project_favorites_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`),
  CONSTRAINT `project_favorites_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `project_favorites`
--

/*!40000 ALTER TABLE `project_favorites` DISABLE KEYS */;
/*!40000 ALTER TABLE `project_favorites` ENABLE KEYS */;

--
-- Table structure for table `project_statuses`
--

DROP TABLE IF EXISTS `project_statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `project_statuses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `color` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#cecece',
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `project_statuses`
--

/*!40000 ALTER TABLE `project_statuses` DISABLE KEYS */;
/*!40000 ALTER TABLE `project_statuses` ENABLE KEYS */;

--
-- Table structure for table `project_users`
--

DROP TABLE IF EXISTS `project_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `project_users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `project_id` bigint unsigned NOT NULL,
  `role` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `project_users_user_id_foreign` (`user_id`),
  KEY `project_users_project_id_foreign` (`project_id`),
  CONSTRAINT `project_users_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`),
  CONSTRAINT `project_users_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `project_users`
--

/*!40000 ALTER TABLE `project_users` DISABLE KEYS */;
/*!40000 ALTER TABLE `project_users` ENABLE KEYS */;

--
-- Table structure for table `projects`
--

DROP TABLE IF EXISTS `projects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `projects` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `owner_id` bigint unsigned NOT NULL,
  `status_id` bigint unsigned NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `ticket_prefix` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'default',
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'kanban',
  PRIMARY KEY (`id`),
  UNIQUE KEY `projects_ticket_prefix_unique` (`ticket_prefix`),
  KEY `projects_owner_id_foreign` (`owner_id`),
  KEY `projects_status_id_foreign` (`status_id`),
  CONSTRAINT `projects_owner_id_foreign` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`),
  CONSTRAINT `projects_status_id_foreign` FOREIGN KEY (`status_id`) REFERENCES `project_statuses` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `projects`
--

/*!40000 ALTER TABLE `projects` DISABLE KEYS */;
/*!40000 ALTER TABLE `projects` ENABLE KEYS */;

--
-- Table structure for table `proyectos`
--

DROP TABLE IF EXISTS `proyectos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `proyectos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `ubicacion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_entrega` date DEFAULT NULL,
  `estado_proyecto_id` bigint unsigned NOT NULL,
  `empresa_constructora_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `proyectos_estado_proyecto_id_foreign` (`estado_proyecto_id`),
  KEY `proyectos_empresa_constructora_id_foreign` (`empresa_constructora_id`),
  CONSTRAINT `proyectos_empresa_constructora_id_foreign` FOREIGN KEY (`empresa_constructora_id`) REFERENCES `empresas` (`id`),
  CONSTRAINT `proyectos_estado_proyecto_id_foreign` FOREIGN KEY (`estado_proyecto_id`) REFERENCES `estados_proyecto` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `proyectos`
--

/*!40000 ALTER TABLE `proyectos` DISABLE KEYS */;
INSERT INTO `proyectos` VALUES (1,'Proyecto Talara','TALARA','AV. SAN MIGUEL 133','2025-07-04','2026-07-07',3,1,'2025-07-04 10:04:10','2025-07-04 11:32:41');
/*!40000 ALTER TABLE `proyectos` ENABLE KEYS */;

--
-- Table structure for table `role_has_permissions`
--

DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_has_permissions`
--

/*!40000 ALTER TABLE `role_has_permissions` DISABLE KEYS */;
INSERT INTO `role_has_permissions` VALUES (1,1),(2,1),(3,1),(4,1),(5,1),(6,1),(7,1),(8,1),(9,1),(10,1),(11,1),(12,1),(13,1),(14,1),(15,1),(16,1),(17,1),(18,1),(19,1),(20,1),(21,1),(22,1),(23,1),(24,1),(25,1),(26,1),(27,1),(28,1),(29,1),(30,1),(31,1),(32,1),(33,1),(34,1),(35,1),(36,1),(37,1),(38,1),(39,1),(40,1),(41,1),(42,1),(43,1),(44,1),(45,1),(46,1),(47,1),(48,1),(49,1),(50,1),(51,1),(52,1),(53,1),(54,1),(55,1),(56,1),(57,1),(58,1),(59,1);
/*!40000 ALTER TABLE `role_has_permissions` ENABLE KEYS */;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Default role','web','2025-07-04 10:02:32','2025-07-04 10:02:32');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `group` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `locked` tinyint(1) NOT NULL DEFAULT '0',
  `payload` json NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `settings_group_index` (`group`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES (1,'general','site_name',0,'\"Helper\"','2025-07-04 10:02:30','2025-07-04 10:02:32'),(2,'general','site_logo',0,'null','2025-07-04 10:02:30','2025-07-04 10:02:32'),(3,'general','enable_registration',0,'true','2025-07-04 10:02:30','2025-07-04 10:02:32'),(4,'general','enable_social_login',0,'\"1\"','2025-07-04 10:02:30','2025-07-04 10:02:32'),(5,'general','default_role',0,'\"1\"','2025-07-04 10:02:30','2025-07-04 10:02:32'),(6,'general','enable_login_form',0,'\"1\"','2025-07-04 10:02:30','2025-07-04 10:02:32'),(7,'general','enable_oidc_login',0,'\"1\"','2025-07-04 10:02:30','2025-07-04 10:02:32'),(8,'general','site_language',0,'\"en\"','2025-07-04 10:02:30','2025-07-04 10:02:32');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;

--
-- Table structure for table `socialite_users`
--

DROP TABLE IF EXISTS `socialite_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `socialite_users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `provider` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `provider_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `socialite_users_provider_provider_id_unique` (`provider`,`provider_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `socialite_users`
--

/*!40000 ALTER TABLE `socialite_users` DISABLE KEYS */;
/*!40000 ALTER TABLE `socialite_users` ENABLE KEYS */;

--
-- Table structure for table `sprints`
--

DROP TABLE IF EXISTS `sprints`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sprints` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `starts_at` date NOT NULL,
  `ends_at` date NOT NULL,
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `project_id` bigint unsigned NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `epic_id` bigint unsigned DEFAULT NULL,
  `started_at` datetime DEFAULT NULL,
  `ended_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sprints_project_id_foreign` (`project_id`),
  KEY `sprints_epic_id_foreign` (`epic_id`),
  CONSTRAINT `sprints_epic_id_foreign` FOREIGN KEY (`epic_id`) REFERENCES `epics` (`id`),
  CONSTRAINT `sprints_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sprints`
--

/*!40000 ALTER TABLE `sprints` DISABLE KEYS */;
/*!40000 ALTER TABLE `sprints` ENABLE KEYS */;

--
-- Table structure for table `ticket_activities`
--

DROP TABLE IF EXISTS `ticket_activities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ticket_activities` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ticket_id` bigint unsigned NOT NULL,
  `old_status_id` bigint unsigned NOT NULL,
  `new_status_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ticket_activities_ticket_id_foreign` (`ticket_id`),
  KEY `ticket_activities_old_status_id_foreign` (`old_status_id`),
  KEY `ticket_activities_new_status_id_foreign` (`new_status_id`),
  KEY `ticket_activities_user_id_foreign` (`user_id`),
  CONSTRAINT `ticket_activities_new_status_id_foreign` FOREIGN KEY (`new_status_id`) REFERENCES `ticket_statuses` (`id`),
  CONSTRAINT `ticket_activities_old_status_id_foreign` FOREIGN KEY (`old_status_id`) REFERENCES `ticket_statuses` (`id`),
  CONSTRAINT `ticket_activities_ticket_id_foreign` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`),
  CONSTRAINT `ticket_activities_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ticket_activities`
--

/*!40000 ALTER TABLE `ticket_activities` DISABLE KEYS */;
/*!40000 ALTER TABLE `ticket_activities` ENABLE KEYS */;

--
-- Table structure for table `ticket_comments`
--

DROP TABLE IF EXISTS `ticket_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ticket_comments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ticket_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ticket_comments_ticket_id_foreign` (`ticket_id`),
  KEY `ticket_comments_user_id_foreign` (`user_id`),
  CONSTRAINT `ticket_comments_ticket_id_foreign` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`),
  CONSTRAINT `ticket_comments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ticket_comments`
--

/*!40000 ALTER TABLE `ticket_comments` DISABLE KEYS */;
/*!40000 ALTER TABLE `ticket_comments` ENABLE KEYS */;

--
-- Table structure for table `ticket_hours`
--

DROP TABLE IF EXISTS `ticket_hours`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ticket_hours` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ticket_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `value` double(8,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `comment` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `activity_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ticket_hours_ticket_id_foreign` (`ticket_id`),
  KEY `ticket_hours_user_id_foreign` (`user_id`),
  KEY `ticket_hours_activity_id_foreign` (`activity_id`),
  CONSTRAINT `ticket_hours_activity_id_foreign` FOREIGN KEY (`activity_id`) REFERENCES `activities` (`id`),
  CONSTRAINT `ticket_hours_ticket_id_foreign` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`),
  CONSTRAINT `ticket_hours_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ticket_hours`
--

/*!40000 ALTER TABLE `ticket_hours` DISABLE KEYS */;
/*!40000 ALTER TABLE `ticket_hours` ENABLE KEYS */;

--
-- Table structure for table `ticket_priorities`
--

DROP TABLE IF EXISTS `ticket_priorities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ticket_priorities` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `color` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#cecece',
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ticket_priorities`
--

/*!40000 ALTER TABLE `ticket_priorities` DISABLE KEYS */;
INSERT INTO `ticket_priorities` VALUES (1,'Low','#008000',0,NULL,'2025-07-04 10:02:32','2025-07-04 10:02:32'),(2,'Normal','#CECECE',1,NULL,'2025-07-04 10:02:32','2025-07-04 10:02:32'),(3,'High','#ff0000',0,NULL,'2025-07-04 10:02:32','2025-07-04 10:02:32');
/*!40000 ALTER TABLE `ticket_priorities` ENABLE KEYS */;

--
-- Table structure for table `ticket_relations`
--

DROP TABLE IF EXISTS `ticket_relations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ticket_relations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ticket_id` bigint unsigned NOT NULL,
  `relation_id` bigint unsigned NOT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ticket_relations_ticket_id_foreign` (`ticket_id`),
  KEY `ticket_relations_relation_id_foreign` (`relation_id`),
  CONSTRAINT `ticket_relations_relation_id_foreign` FOREIGN KEY (`relation_id`) REFERENCES `tickets` (`id`),
  CONSTRAINT `ticket_relations_ticket_id_foreign` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ticket_relations`
--

/*!40000 ALTER TABLE `ticket_relations` DISABLE KEYS */;
/*!40000 ALTER TABLE `ticket_relations` ENABLE KEYS */;

--
-- Table structure for table `ticket_statuses`
--

DROP TABLE IF EXISTS `ticket_statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ticket_statuses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `color` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#cecece',
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `order` int NOT NULL DEFAULT '1',
  `project_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ticket_statuses_project_id_foreign` (`project_id`),
  CONSTRAINT `ticket_statuses_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ticket_statuses`
--

/*!40000 ALTER TABLE `ticket_statuses` DISABLE KEYS */;
INSERT INTO `ticket_statuses` VALUES (1,'Todo','#cecece',1,NULL,'2025-07-04 10:02:32','2025-07-04 10:02:32',1,NULL),(2,'In progress','#ff7f00',0,NULL,'2025-07-04 10:02:32','2025-07-04 10:02:32',2,NULL),(3,'Done','#008000',0,NULL,'2025-07-04 10:02:32','2025-07-04 10:02:32',3,NULL),(4,'Archived','#ff0000',0,NULL,'2025-07-04 10:02:32','2025-07-04 10:02:32',4,NULL);
/*!40000 ALTER TABLE `ticket_statuses` ENABLE KEYS */;

--
-- Table structure for table `ticket_subscribers`
--

DROP TABLE IF EXISTS `ticket_subscribers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ticket_subscribers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `ticket_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ticket_subscribers_user_id_foreign` (`user_id`),
  KEY `ticket_subscribers_ticket_id_foreign` (`ticket_id`),
  CONSTRAINT `ticket_subscribers_ticket_id_foreign` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`),
  CONSTRAINT `ticket_subscribers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ticket_subscribers`
--

/*!40000 ALTER TABLE `ticket_subscribers` DISABLE KEYS */;
/*!40000 ALTER TABLE `ticket_subscribers` ENABLE KEYS */;

--
-- Table structure for table `ticket_types`
--

DROP TABLE IF EXISTS `ticket_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ticket_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `color` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#cecece',
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ticket_types`
--

/*!40000 ALTER TABLE `ticket_types` DISABLE KEYS */;
INSERT INTO `ticket_types` VALUES (1,'Task','heroicon-o-check-circle','#00FFFF',1,NULL,'2025-07-04 10:02:32','2025-07-04 10:02:32'),(2,'Evolution','heroicon-o-clipboard-list','#008000',0,NULL,'2025-07-04 10:02:32','2025-07-04 10:02:32'),(3,'Bug','heroicon-o-x','#ff0000',0,NULL,'2025-07-04 10:02:32','2025-07-04 10:02:32');
/*!40000 ALTER TABLE `ticket_types` ENABLE KEYS */;

--
-- Table structure for table `tickets`
--

DROP TABLE IF EXISTS `tickets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tickets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner_id` bigint unsigned NOT NULL,
  `responsible_id` bigint unsigned DEFAULT NULL,
  `status_id` bigint unsigned NOT NULL,
  `project_id` bigint unsigned NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type_id` bigint unsigned NOT NULL,
  `order` int NOT NULL DEFAULT '0',
  `priority_id` bigint unsigned NOT NULL,
  `estimation` double(8,2) DEFAULT NULL,
  `epic_id` bigint unsigned DEFAULT NULL,
  `sprint_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tickets_owner_id_foreign` (`owner_id`),
  KEY `tickets_responsible_id_foreign` (`responsible_id`),
  KEY `tickets_status_id_foreign` (`status_id`),
  KEY `tickets_project_id_foreign` (`project_id`),
  KEY `tickets_type_id_foreign` (`type_id`),
  KEY `tickets_priority_id_foreign` (`priority_id`),
  KEY `tickets_epic_id_foreign` (`epic_id`),
  KEY `tickets_sprint_id_foreign` (`sprint_id`),
  CONSTRAINT `tickets_epic_id_foreign` FOREIGN KEY (`epic_id`) REFERENCES `epics` (`id`),
  CONSTRAINT `tickets_owner_id_foreign` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`),
  CONSTRAINT `tickets_priority_id_foreign` FOREIGN KEY (`priority_id`) REFERENCES `ticket_priorities` (`id`),
  CONSTRAINT `tickets_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`),
  CONSTRAINT `tickets_responsible_id_foreign` FOREIGN KEY (`responsible_id`) REFERENCES `users` (`id`),
  CONSTRAINT `tickets_sprint_id_foreign` FOREIGN KEY (`sprint_id`) REFERENCES `sprints` (`id`),
  CONSTRAINT `tickets_status_id_foreign` FOREIGN KEY (`status_id`) REFERENCES `ticket_statuses` (`id`),
  CONSTRAINT `tickets_type_id_foreign` FOREIGN KEY (`type_id`) REFERENCES `ticket_types` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tickets`
--

/*!40000 ALTER TABLE `tickets` DISABLE KEYS */;
/*!40000 ALTER TABLE `tickets` ENABLE KEYS */;

--
-- Table structure for table `tipo_inmueble`
--

DROP TABLE IF EXISTS `tipo_inmueble`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tipo_inmueble` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipo_inmueble`
--

/*!40000 ALTER TABLE `tipo_inmueble` DISABLE KEYS */;
INSERT INTO `tipo_inmueble` VALUES (1,'Departamento'),(2,'Estacionamiento'),(3,'Estacionamiento de bicicletas');
/*!40000 ALTER TABLE `tipo_inmueble` ENABLE KEYS */;

--
-- Table structure for table `tipos_departamento`
--

DROP TABLE IF EXISTS `tipos_departamento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tipos_departamento` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tipos_departamento_nombre_unique` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipos_departamento`
--

/*!40000 ALTER TABLE `tipos_departamento` DISABLE KEYS */;
INSERT INTO `tipos_departamento` VALUES (1,'FLAT','FLAT'),(2,'DUPLEX','DUPLEX'),(3,'TRIPLEX','TRIPLEX');
/*!40000 ALTER TABLE `tipos_departamento` ENABLE KEYS */;

--
-- Table structure for table `tipos_financiamiento`
--

DROP TABLE IF EXISTS `tipos_financiamiento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tipos_financiamiento` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `color` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#6b7280',
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tipos_financiamiento_nombre_unique` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipos_financiamiento`
--

/*!40000 ALTER TABLE `tipos_financiamiento` DISABLE KEYS */;
INSERT INTO `tipos_financiamiento` VALUES (1,'Crédito','Crédito para proyectos diversos','#4CAF50',0,NULL,NULL),(2,'Hipotecario','Crédito hipotecario para la compra de viviendas','#2196F3',0,NULL,NULL),(3,'Crédito Directo','Préstamos personales sin intermediarios','#FF9800',0,NULL,NULL),(4,'Contado','Pago de forma inmediata sin financiamiento','#F44336',0,NULL,NULL),(5,'Leasing','Arrendamiento de bienes con opción de compra','#9C27B0',0,NULL,NULL),(6,'Fovimar','Fondo de vivienda militar','#3F51B5',0,NULL,NULL),(7,'Fovipol','Fondo de vivienda para policías','#8BC34A',0,NULL,NULL),(8,'Permuta','Intercambio de propiedad entre dos partes','#FFEB3B',0,NULL,NULL),(9,'Ahorro','Ahorro personal para adquirir propiedades','#009688',0,NULL,NULL),(10,'Fovimfap','Fondo de vivienda para trabajadores del sector público','#607D8B',0,NULL,NULL);
/*!40000 ALTER TABLE `tipos_financiamiento` ENABLE KEYS */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `two_factor_secret` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `two_factor_recovery_codes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `two_factor_confirmed_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `creation_token` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'db',
  `oidc_username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `oidc_sub` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'John DOE','john.doe@helper.app','2025-07-04 10:02:32','$2y$10$.qrMusoWi6OgfF6MSLIMRejzQnlq4q6G7cj94r4fyKwMeazMsq1ru',NULL,NULL,NULL,NULL,'2025-07-04 10:02:32','2025-07-04 10:02:32',NULL,NULL,'db',NULL,NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;

--
-- Table structure for table `vistas`
--

DROP TABLE IF EXISTS `vistas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vistas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vistas`
--

/*!40000 ALTER TABLE `vistas` DISABLE KEYS */;
INSERT INTO `vistas` VALUES (1,'Interno'),(2,'Externo');
/*!40000 ALTER TABLE `vistas` ENABLE KEYS */;

--
-- Dumping routines for database 'helper'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-07-04  2:59:47
