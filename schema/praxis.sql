-- MySQL dump 10.13  Distrib 5.6.30, for Linux (x86_64)
--
-- Host: 127.0.0.1    Database: praxis
-- ------------------------------------------------------
-- Server version	5.6.30

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `t_autor`
--

DROP TABLE IF EXISTS `t_autor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t_autor` (
  `fn_autor_id` smallint(6) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Id autoincrement',
  `fs_nome` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT 'Nome autor',
  `fs_notacao_autor` varchar(3) NOT NULL COMMENT 'Notação autor',
  `fd_inclusao` datetime NOT NULL COMMENT 'Data de inclusão de registro',
  `fd_alteracao` datetime DEFAULT NULL COMMENT 'Data de alteração de registro',
  PRIMARY KEY (`fn_autor_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='Tabela de autor';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `t_material`
--

DROP TABLE IF EXISTS `t_material`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t_material` (
  `fn_material_id` smallint(6) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Id autoincrement',
  `fs_titulo` varchar(255) NOT NULL COMMENT 'Título do material',
  `fs_subtitulo` varchar(255) DEFAULT NULL COMMENT 'Subtítulo do material',
  `fs_caminho_imagem` varchar(255) DEFAULT NULL COMMENT 'Caminho da imagem do material',
  `fs_tipo_material` varchar(255) NOT NULL,
  PRIMARY KEY (`fn_material_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='Tabela de material';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `t_material_autor`
--

DROP TABLE IF EXISTS `t_material_autor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t_material_autor` (
  `fn_material_autor_id` smallint(6) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Id autoincrement',
  `fn_material_id` smallint(6) unsigned NOT NULL COMMENT 'Id do material',
  `fn_autor_id` smallint(6) unsigned NOT NULL COMMENT 'Id do autor',
  PRIMARY KEY (`fn_material_autor_id`),
  KEY `fk_t_material_has_t_autor_t_autor1_idx` (`fn_autor_id`),
  KEY `fk_t_material_has_t_autor_t_material_idx` (`fn_material_id`),
  CONSTRAINT `fk_t_material_has_t_autor_t_autor1` FOREIGN KEY (`fn_autor_id`) REFERENCES `t_autor` (`fn_autor_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_t_material_has_t_autor_t_material` FOREIGN KEY (`fn_material_id`) REFERENCES `t_material` (`fn_material_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='Tabela de referência entre material e autor';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `t_material_dicionario`
--

DROP TABLE IF EXISTS `t_material_dicionario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t_material_dicionario` (
  `fn_material_id` smallint(6) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Id autoincrement',
  `fs_edicao` varchar(45) NOT NULL,
  `fs_classificacao` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`fn_material_id`),
  CONSTRAINT `fk_t_material_dicionario_t_material1` FOREIGN KEY (`fn_material_id`) REFERENCES `t_material` (`fn_material_id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='Tabela dicionário';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `t_material_livro`
--

DROP TABLE IF EXISTS `t_material_livro`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `t_material_livro` (
  `fn_material_id` smallint(6) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Id autoincrement',
  `fs_isbn` varchar(45) NOT NULL COMMENT 'Código ISBN',
  `fn_numero_pagina` int(11) NOT NULL COMMENT 'Número de páginas do livro',
  `fs_resumo` longtext COMMENT 'Resumo do livro',
  PRIMARY KEY (`fn_material_id`),
  CONSTRAINT `fk_t_material_livro_t_material1` FOREIGN KEY (`fn_material_id`) REFERENCES `t_material` (`fn_material_id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='Tabela livro';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-05-24 14:43:38
