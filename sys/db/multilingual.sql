#NEWS
ALTER TABLE `acid_news`
ADD `title_fr` VARCHAR( 100 ) NOT NULL AFTER `id_news` ,
ADD `head_fr` TEXT NOT NULL AFTER `title_fr` ,
ADD `content_fr` LONGTEXT NOT NULL AFTER `head_fr` ,
ADD `seo_title_fr` VARCHAR( 100 ) NOT NULL AFTER `content_fr` ,
ADD `seo_desc_fr` VARCHAR( 255 ) NOT NULL AFTER `seo_title_fr` ,
ADD `seo_keys_fr` VARCHAR( 255 ) NOT NULL AFTER `seo_desc_fr` ,
ADD `title_en` VARCHAR( 100 ) NOT NULL AFTER `seo_keys_fr` ,
ADD `head_en` TEXT NOT NULL AFTER `title_en` ,
ADD `content_en` LONGTEXT NOT NULL AFTER `head_en` ,
ADD `seo_title_en` VARCHAR( 100 ) NOT NULL AFTER `content_en` ,
ADD `seo_desc_en` VARCHAR( 255 ) NOT NULL AFTER `seo_title_en` ,
ADD `seo_keys_en` VARCHAR( 255 ) NOT NULL AFTER `seo_desc_en` ,
ADD `title_es` VARCHAR( 100 ) NOT NULL AFTER `seo_keys_en` ,
ADD `head_es` TEXT NOT NULL AFTER `title_es` ,
ADD `content_es` LONGTEXT NOT NULL AFTER `head_es` ,
ADD `seo_title_es` VARCHAR( 100 ) NOT NULL AFTER `content_es` ,
ADD `seo_desc_es` VARCHAR( 255 ) NOT NULL AFTER `seo_title_es` ,
ADD `seo_keys_es` VARCHAR( 255 ) NOT NULL AFTER `seo_desc_es` ,
ADD `title_de` VARCHAR( 100 ) NOT NULL AFTER `seo_keys_es` ,
ADD `head_de` TEXT NOT NULL AFTER `title_de` ,
ADD `content_de` LONGTEXT NOT NULL AFTER `head_de` ,
ADD `seo_title_de` VARCHAR( 100 ) NOT NULL AFTER `content_de` ,
ADD `seo_desc_de` VARCHAR( 255 ) NOT NULL AFTER `seo_title_de` ,
ADD `seo_keys_de` VARCHAR( 255 ) NOT NULL AFTER `seo_desc_de` ,
ADD `title_it` VARCHAR( 100 ) NOT NULL AFTER `seo_keys_de` ,
ADD `head_it` TEXT NOT NULL AFTER `title_it` ,
ADD `content_it` LONGTEXT NOT NULL AFTER `head_it`,
ADD `seo_title_it` VARCHAR( 100 ) NOT NULL AFTER `content_it` ,
ADD `seo_desc_it` VARCHAR( 255 ) NOT NULL AFTER `seo_title_it` ,
ADD `seo_keys_it` VARCHAR( 255 ) NOT NULL AFTER `seo_desc_it`  ;

COMMIT; 

UPDATE `acid_news`  SET
`head_fr`=`head`, 
`head_en`=`head`, 
`head_es`=`head`, 
`head_de`=`head`, 
`head_it`=`head`, 
`title_fr`=`title`, 
`title_en`=`title`, 
`title_es`=`title`, 
`title_de`=`title`, 
`title_it`=`title`, 
`content_fr`=`content`, 
`content_en`=`content`, 
`content_es`=`content`, 
`content_de`=`content`, 
`content_it`=`content`,
`seo_title_fr`=`seo_title`, 
`seo_title_en`=`seo_title`, 
`seo_title_es`=`seo_title`, 
`seo_title_de`=`seo_title`, 
`seo_title_it`=`seo_title`,
`seo_desc_fr`=`seo_desc`, 
`seo_desc_en`=`seo_desc`, 
`seo_desc_es`=`seo_desc`, 
`seo_desc_de`=`seo_desc`, 
`seo_desc_it`=`seo_desc`,
`seo_keys_fr`=`seo_keys`, 
`seo_keys_en`=`seo_keys`, 
`seo_keys_es`=`seo_keys`, 
`seo_keys_de`=`seo_keys`, 
`seo_keys_it`=`seo_keys`;

COMMIT;

ALTER TABLE `acid_news`
  DROP `title`,
  DROP `head`,
  DROP `content`,
  DROP `seo_title`,
  DROP `seo_desc`,
  DROP `seo_keys`;

COMMIT; 


#PAGES
ALTER TABLE `acid_page` 
ADD `ident_fr` VARCHAR( 255 ) NOT NULL AFTER `id_page` ,
ADD `title_fr` VARCHAR( 100 ) NOT NULL AFTER `ident_fr` ,
ADD `content_fr` TEXT NOT NULL AFTER `title_fr` ,
ADD `seo_title_fr` VARCHAR( 100 ) NOT NULL AFTER `content_fr` ,
ADD `seo_desc_fr` VARCHAR( 255 ) NOT NULL AFTER `seo_title_fr` ,
ADD `seo_keys_fr` VARCHAR( 255 ) NOT NULL AFTER `seo_desc_fr` ,
ADD `ident_en` VARCHAR( 255 ) NOT NULL AFTER `seo_keys_fr` ,
ADD `title_en` VARCHAR( 100 ) NOT NULL AFTER `ident_en` ,
ADD `content_en` TEXT NOT NULL AFTER `title_en` ,
ADD `seo_title_en` VARCHAR( 100 ) NOT NULL AFTER `content_en` ,
ADD `seo_desc_en` VARCHAR( 255 ) NOT NULL AFTER `seo_title_en` ,
ADD `seo_keys_en` VARCHAR( 255 ) NOT NULL AFTER `seo_desc_en` ,
ADD `ident_es` VARCHAR( 255 ) NOT NULL AFTER `seo_keys_en` ,
ADD `title_es` VARCHAR( 100 ) NOT NULL AFTER `ident_es` ,
ADD `content_es` TEXT NOT NULL AFTER `title_es` ,
ADD `seo_title_es` VARCHAR( 100 ) NOT NULL AFTER `content_es` ,
ADD `seo_desc_es` VARCHAR( 255 ) NOT NULL AFTER `seo_title_es` ,
ADD `seo_keys_es` VARCHAR( 255 ) NOT NULL AFTER `seo_desc_es` ,
ADD `ident_de` VARCHAR( 255 ) NOT NULL AFTER `seo_keys_es` ,
ADD `title_de` VARCHAR( 100 ) NOT NULL AFTER `ident_de` ,
ADD `content_de` TEXT NOT NULL AFTER `title_de` ,
ADD `seo_title_de` VARCHAR( 100 ) NOT NULL AFTER `content_de` ,
ADD `seo_desc_de` VARCHAR( 255 ) NOT NULL AFTER `seo_title_de` ,
ADD `seo_keys_de` VARCHAR( 255 ) NOT NULL AFTER `seo_desc_de` ,
ADD `ident_it` VARCHAR( 255 ) NOT NULL AFTER `seo_keys_de` ,
ADD `title_it` VARCHAR( 100 ) NOT NULL AFTER `ident_it` ,
ADD `content_it` TEXT NOT NULL AFTER `title_it` ,
ADD `seo_title_it` VARCHAR( 100 ) NOT NULL AFTER `content_it` ,
ADD `seo_desc_it` VARCHAR( 255 ) NOT NULL AFTER `seo_title_it` ,
ADD `seo_keys_it` VARCHAR( 255 ) NOT NULL AFTER `seo_desc_it`  ;

COMMIT; 

UPDATE `acid_page`  SET 
`ident_fr`=`ident`, 
`ident_en`=`ident`, 
`ident_es`=`ident`, 
`ident_de`=`ident`, 
`ident_it`=`ident`, 
`title_fr`=`title`, 
`title_en`=`title`, 
`title_es`=`title`, 
`title_de`=`title`, 
`title_it`=`title`, 
`content_fr`=`content`, 
`content_en`=`content`, 
`content_es`=`content`, 
`content_de`=`content`, 
`content_it`=`content`,
`seo_title_fr`=`seo_title`, 
`seo_title_en`=`seo_title`, 
`seo_title_es`=`seo_title`, 
`seo_title_de`=`seo_title`, 
`seo_title_it`=`seo_title`,
`seo_desc_fr`=`seo_desc`, 
`seo_desc_en`=`seo_desc`, 
`seo_desc_es`=`seo_desc`, 
`seo_desc_de`=`seo_desc`, 
`seo_desc_it`=`seo_desc`,
`seo_keys_fr`=`seo_keys`, 
`seo_keys_en`=`seo_keys`, 
`seo_keys_es`=`seo_keys`, 
`seo_keys_de`=`seo_keys`, 
`seo_keys_it`=`seo_keys`;

COMMIT;

ALTER TABLE `acid_page`
  DROP `title`,
  DROP `ident`,
  DROP `content`,
  DROP `seo_title`,
  DROP `seo_desc`,
  DROP `seo_keys`;

COMMIT; 


#PHOTO
ALTER TABLE `acid_photo` 
ADD `name_fr` VARCHAR( 100 ) NOT NULL AFTER `id_photo` ,
ADD `name_en` VARCHAR( 100 ) NOT NULL AFTER `name_fr` ,
ADD `name_es` VARCHAR( 100 ) NOT NULL AFTER `name_en` ,
ADD `name_de` VARCHAR( 100 ) NOT NULL AFTER `name_es` ,
ADD `name_it` VARCHAR( 100 ) NOT NULL AFTER `name_de` ;

COMMIT; 

UPDATE `acid_photo`  SET 
`name_fr`=`name`, 
`name_en`=`name`, 
`name_es`=`name`, 
`name_de`=`name`, 
`name_it`=`name`;

COMMIT; 

ALTER TABLE `acid_photo`
  DROP `name`;

COMMIT; 


#PHOTO HOME
ALTER TABLE `acid_photo_home` 
ADD `name_fr` VARCHAR( 100 ) NOT NULL AFTER `id_photo_home` ,
ADD `name_en` VARCHAR( 100 ) NOT NULL AFTER `name_fr` ,
ADD `name_es` VARCHAR( 100 ) NOT NULL AFTER `name_en` ,
ADD `name_de` VARCHAR( 100 ) NOT NULL AFTER `name_es` ,
ADD `name_it` VARCHAR( 100 ) NOT NULL AFTER `name_de` ;

COMMIT; 

UPDATE `acid_photo_home`  SET 
`name_fr`=`name`, 
`name_en`=`name`, 
`name_es`=`name`, 
`name_de`=`name`, 
`name_it`=`name`;

COMMIT; 

ALTER TABLE `acid_photo_home`
  DROP `name`;

COMMIT; 

#SCRIPT CATEGORY

ALTER TABLE `acid_script_category`
ADD `description_fr` VARCHAR( 100 ) NOT NULL AFTER `id_script_category` ,
ADD `description_en` VARCHAR( 100 ) NOT NULL AFTER `description_fr` ,
ADD `description_es` VARCHAR( 100 ) NOT NULL AFTER `description_en` ,
ADD `description_de` VARCHAR( 100 ) NOT NULL AFTER `description_es` ,
ADD `description_it` VARCHAR( 100 ) NOT NULL AFTER `description_de` ;

ALTER TABLE `acid_script_category`
ADD `name_fr` VARCHAR( 100 ) NOT NULL AFTER `id_script_category` ,
ADD `name_en` VARCHAR( 100 ) NOT NULL AFTER `name_fr` ,
ADD `name_es` VARCHAR( 100 ) NOT NULL AFTER `name_en` ,
ADD `name_de` VARCHAR( 100 ) NOT NULL AFTER `name_es` ,
ADD `name_it` VARCHAR( 100 ) NOT NULL AFTER `name_de` ;

COMMIT;

UPDATE `acid_script_category`  SET
`description_fr`=`description`,
`description_en`=`description`,
`description_es`=`description`,
`description_de`=`description`,
`description_it`=`description`;

UPDATE `acid_script_category`  SET
`name_fr`=`name`,
`name_en`=`name`,
`name_es`=`name`,
`name_de`=`name`,
`name_it`=`name`;

COMMIT;

ALTER TABLE `acid_script_category`
  DROP `description`;

ALTER TABLE `acid_script_category`
  DROP `name`;

COMMIT;

#SCRIPT

ALTER TABLE `acid_script`
ADD `description_fr` VARCHAR( 100 ) NOT NULL AFTER `id_script_category` ,
ADD `description_en` VARCHAR( 100 ) NOT NULL AFTER `description_fr` ,
ADD `description_es` VARCHAR( 100 ) NOT NULL AFTER `description_en` ,
ADD `description_de` VARCHAR( 100 ) NOT NULL AFTER `description_es` ,
ADD `description_it` VARCHAR( 100 ) NOT NULL AFTER `description_de` ;

ALTER TABLE `acid_script`
ADD `name_fr` VARCHAR( 100 ) NOT NULL AFTER `id_script_category` ,
ADD `name_en` VARCHAR( 100 ) NOT NULL AFTER `name_fr` ,
ADD `name_es` VARCHAR( 100 ) NOT NULL AFTER `name_en` ,
ADD `name_de` VARCHAR( 100 ) NOT NULL AFTER `name_es` ,
ADD `name_it` VARCHAR( 100 ) NOT NULL AFTER `name_de` ;

COMMIT;

UPDATE `acid_script`  SET
`description_fr`=`description`,
`description_en`=`description`,
`description_es`=`description`,
`description_de`=`description`,
`description_it`=`description`;

UPDATE `acid_script`  SET
`name_fr`=`name`,
`name_en`=`name`,
`name_es`=`name`,
`name_de`=`name`,
`name_it`=`name`;

COMMIT;

ALTER TABLE `acid_script`
  DROP `description`;

ALTER TABLE `acid_script`
  DROP `name`;

COMMIT;

#SEO
ALTER TABLE `acid_seo` 
ADD `seo_title_fr` VARCHAR( 100 ) NOT NULL AFTER `url` ,
ADD `seo_desc_fr` VARCHAR( 255 ) NOT NULL AFTER `seo_title_fr` ,
ADD `seo_keys_fr` VARCHAR( 255 ) NOT NULL AFTER `seo_desc_fr` ,
ADD `seo_title_en` VARCHAR( 100 ) NOT NULL AFTER `seo_keys_fr` ,
ADD `seo_desc_en` VARCHAR( 255 ) NOT NULL AFTER `seo_title_en` ,
ADD `seo_keys_en` VARCHAR( 255 ) NOT NULL AFTER `seo_desc_en` ,
ADD `seo_title_es` VARCHAR( 100 ) NOT NULL AFTER `seo_keys_en` ,
ADD `seo_desc_es` VARCHAR( 255 ) NOT NULL AFTER `seo_title_es` ,
ADD `seo_keys_es` VARCHAR( 255 ) NOT NULL AFTER `seo_desc_es` ,
ADD `seo_title_de` VARCHAR( 100 ) NOT NULL AFTER `seo_keys_es` ,
ADD `seo_desc_de` VARCHAR( 255 ) NOT NULL AFTER `seo_title_de` ,
ADD `seo_keys_de` VARCHAR( 255 ) NOT NULL AFTER `seo_desc_de` ,
ADD `seo_title_it` VARCHAR( 100 ) NOT NULL AFTER `seo_keys_de` ,
ADD `seo_desc_it` VARCHAR( 255 ) NOT NULL AFTER `seo_title_it` ,
ADD `seo_keys_it` VARCHAR( 255 ) NOT NULL AFTER `seo_desc_it`  ;

COMMIT; 

UPDATE `acid_seo`  SET 
`seo_title_fr`=`seo_title`, 
`seo_title_en`=`seo_title`, 
`seo_title_es`=`seo_title`, 
`seo_title_de`=`seo_title`, 
`seo_title_it`=`seo_title`,
`seo_desc_fr`=`seo_desc`, 
`seo_desc_en`=`seo_desc`, 
`seo_desc_es`=`seo_desc`, 
`seo_desc_de`=`seo_desc`, 
`seo_desc_it`=`seo_desc`,
`seo_keys_fr`=`seo_keys`, 
`seo_keys_en`=`seo_keys`, 
`seo_keys_es`=`seo_keys`, 
`seo_keys_de`=`seo_keys`, 
`seo_keys_it`=`seo_keys`;

COMMIT;

ALTER TABLE `acid_seo`
  DROP `seo_title`,
  DROP `seo_desc`,
  DROP `seo_keys`;

COMMIT;

#MENU
ALTER TABLE `acid_menu`
ADD `name_fr` VARCHAR( 100 ) NOT NULL AFTER `id_menu` ,
ADD `name_en` VARCHAR( 100 ) NOT NULL AFTER `name_fr` ,
ADD `name_es` VARCHAR( 100 ) NOT NULL AFTER `name_en` ,
ADD `name_de` VARCHAR( 100 ) NOT NULL AFTER `name_es` ,
ADD `name_it` VARCHAR( 100 ) NOT NULL AFTER `name_de` ;

COMMIT;

UPDATE `acid_menu`  SET
`name_fr`=`name`,
`name_en`=`name`,
`name_es`=`name`,
`name_de`=`name`,
`name_it`=`name`;

COMMIT;

ALTER TABLE `acid_menu`
  DROP `name`;

COMMIT;

ALTER TABLE `acid_menu`
ADD `url_fr` VARCHAR( 100 ) NOT NULL AFTER `id_menu` ,
ADD `url_en` VARCHAR( 100 ) NOT NULL AFTER `url_fr` ,
ADD `url_es` VARCHAR( 100 ) NOT NULL AFTER `url_en` ,
ADD `url_de` VARCHAR( 100 ) NOT NULL AFTER `url_es` ,
ADD `url_it` VARCHAR( 100 ) NOT NULL AFTER `url_de` ;

ALTER TABLE `acid_menu`
  DROP `url`;

COMMIT;