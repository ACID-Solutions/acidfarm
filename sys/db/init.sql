-- phpMyAdmin SQL Dump
-- version 3.2.0.1
-- http://www.phpmyadmin.net
--
-- Serveur: localhost
-- Généré le : Mar 01 Mars 2011 à 15:32
-- Version du serveur: 5.1.36
-- Version de PHP: 5.3.0

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `acidfarm`
--

-- --------------------------------------------------------

--
-- Structure de la table `acid_news`
--

CREATE TABLE IF NOT EXISTS `acid_news` (
  `id_news` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `head` text COLLATE utf8_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8_unicode_ci NOT NULL,
  `seo_title` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `seo_desc` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `seo_keys` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `adate` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `active` enum('0','1') COLLATE utf8_unicode_ci NOT NULL,
  `rss` enum('0','1') COLLATE utf8_unicode_ci NOT NULL,
  `src` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `cache_time` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id_news`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;


--
-- Contenu de la table `af_news`
--

INSERT INTO `acid_news` (`id_news`, `title`, `head`, `content`, `seo_title`, `seo_desc`, `seo_keys`, `adate`, `active`, `rss`,`src`,`cache_time`) VALUES
(1, 'AcidFarm is working', 'Here my news head.', '<p>Here my news content.</p>', '','','', NOW(), '1','0','','');

-- --------------------------------------------------------

--
-- Structure de la table `acid_page`
--

CREATE TABLE IF NOT EXISTS `acid_page` (
  `id_page` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_page_category` int(10) unsigned NOT NULL,
  `title` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `ident` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `adate` datetime NULL,
  `content` text COLLATE utf8_unicode_ci NOT NULL,
  `seo_title` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `seo_desc` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `seo_keys` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `active` tinyint(1) unsigned NOT NULL,
  `src` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `cache_time` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id_page`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Contenu de la table `acid_page`
--

INSERT INTO `acid_page` (`id_page`, `id_page_category`, `title`, `ident`, `adate`, `content`, `seo_title`, `seo_desc`, `seo_keys`, `active`,`src`,`cache_time`) VALUES
(1, 0, 'AcidFarm', 'acidfarm', '2011-03-01 11:50:09', '<p>AcidFarm is an open source framework developped by Acid-Solutions SARL.</p>\r\n<p>Primarily developed for web developers who want to build a PHP Website more easily, Acidfarm was made in july 2010.</p>\r\n<p>AcidFarm in few words :</p>\r\n<ul>\r\n<li>A Securized FrameWork</li>\r\n<li>A Lightweight FrameWork</li>\r\n<li>A Flexible Framework</li>\r\n</ul>', '','','', 1, '', '');


-- --------------------------------------------------------

--
-- Structure de la table `acid_seo`
--

CREATE TABLE IF NOT EXISTS `acid_seo` (
  `id_seo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `routename` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `seo_title` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `seo_desc` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `seo_keys` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `seo_img` VARCHAR( 255 ) COLLATE utf8_unicode_ci NOT NULL,
  `strict_mode` enum('0','1') COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id_seo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Contenu de la table `acid_seo`
--



--
-- Structure de la table `acid_session`
--

CREATE TABLE IF NOT EXISTS `acid_session` (
  `id` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `id_user` int(10) unsigned NOT NULL,
  `expire` int(11) unsigned NOT NULL,
  `user_ip` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `user_agent` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `data` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Contenu de la table `acid_session`
--


-- --------------------------------------------------------

--
-- Structure de la table `acid_user`
--

CREATE TABLE IF NOT EXISTS `acid_user` (
  `id_user` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_group` int(11) NOT NULL DEFAULT '0',
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `image_0` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `firstname` VARCHAR( 100 ) COLLATE utf8_unicode_ci NOT NULL,
  `lastname` VARCHAR( 100 ) COLLATE utf8_unicode_ci NOT NULL,
  `address` VARCHAR( 255 ) COLLATE utf8_unicode_ci NOT NULL ,
  `cp` VARCHAR( 15 ) COLLATE utf8_unicode_ci NOT NULL ,
  `city` VARCHAR( 100 ) COLLATE utf8_unicode_ci NOT NULL ,
  `country` VARCHAR( 100 ) COLLATE utf8_unicode_ci NOT NULL ,
  `phone` VARCHAR( 20 ) COLLATE utf8_unicode_ci NOT NULL ,
  `level` tinyint(4) NOT NULL DEFAULT '0',
  `date_creation` datetime NULL DEFAULT NULL,
  `date_activation` datetime NULL DEFAULT NULL,
  `date_deactivation` datetime NULL DEFAULT NULL,
  `date_connexion` datetime NULL DEFAULT NULL,
  `last_lang` varchar(3) COLLATE utf8_unicode_ci NOT NULL,
  `lang` varchar(3) COLLATE utf8_unicode_ci NOT NULL,
  `ip` varchar(19) COLLATE utf8_unicode_ci NOT NULL,
  `user_salt` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `active` ENUM( '0', '1' ) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_user`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;


--
-- Contenu de la table `acid_user`
--

INSERT INTO `acid_user` (`id_user`, `id_group`, `username`, `password`, `email`, `image_0`, `level`, `date_creation`, `date_activation`, `date_deactivation`, `ip`, `user_salt`) VALUES
(1, 0, 'admin', 'f8f90f62c3bb8731ae8dc7f63f31925b', 'admin@domain.tld', '', 10, null,null,null, '', '');


-- --------------------------------------------------------


--
-- Structure de la table `acid_user_group`
--

CREATE TABLE IF NOT EXISTS `acid_user_group` (
  `id_user_group` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id_user_group`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;


--
-- Structure de la table `acid_user_group_assoc`
--

CREATE TABLE IF NOT EXISTS `acid_user_group_assoc` (
  `id_user_group_assoc` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_user` int(10) unsigned NOT NULL,
  `id_user_group` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_user_group_assoc`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;



--
-- Structure de la table `acid_user_permission`
--

CREATE TABLE IF NOT EXISTS `acid_user_permission` (
  `id_user_permission` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `module` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `do` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `id` int(10) unsigned NOT NULL,
  `type` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id_user_permission`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;


--
-- Structure de la table `acid_config`
--

CREATE TABLE IF NOT EXISTS `acid_config` (
  `id_config` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id_config`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------


--
-- Structure de la table `acid_photo_home`
--

CREATE TABLE IF NOT EXISTS `acid_photo_home` (
  `id_photo_home` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `pos` int(11) NOT NULL,
  `src` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `active` ENUM( '0', '1' ) NOT NULL DEFAULT '1',
  `cache_time` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id_photo_home`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;




-- --------------------------------------------------------


--
-- Structure de la table `acid_photo`
--

CREATE TABLE IF NOT EXISTS `acid_photo` (
  `id_photo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `pos` int(11) NOT NULL,
  `src` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `active` ENUM( '0', '1' ) NOT NULL DEFAULT '1',
  `cache_time` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id_photo`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;



-- --------------------------------------------------------


--
-- Structure de la table `acid_menu`
--

CREATE TABLE IF NOT EXISTS `acid_menu` (
  `id_menu` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ident` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `pos` int(11) NOT NULL,
  `active` enum('0','1') COLLATE utf8_unicode_ci NOT NULL,
   PRIMARY KEY (`id_menu`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;



--
-- Structure de la table `acid_script_category`
--

CREATE TABLE IF NOT EXISTS `acid_script_category` (
  `id_script_category` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `key` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `pos` int(11) NOT NULL,
  `show` enum('0','1') COLLATE utf8_unicode_ci NOT NULL,
  `use_cookie` enum('0','1') COLLATE utf8_unicode_ci NOT NULL,
  `default` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `active` enum('0','1') COLLATE utf8_unicode_ci NOT NULL,
   PRIMARY KEY (`id_script_category`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Structure de la table `acid_script`
--

CREATE TABLE IF NOT EXISTS `acid_script` (
  `id_script` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_script_category` int(10) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `key` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `script` text COLLATE utf8_unicode_ci NOT NULL,
  `else_script` text COLLATE utf8_unicode_ci NOT NULL,
  `pos` int(11) NOT NULL,
  `optional` enum('0','1') COLLATE utf8_unicode_ci NOT NULL,
  `default` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `template` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `show` enum('0','1') COLLATE utf8_unicode_ci NOT NULL,
  `active` enum('0','1') COLLATE utf8_unicode_ci NOT NULL,
   PRIMARY KEY (`id_script`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;