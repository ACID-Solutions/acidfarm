#ACTU
ALTER TABLE `acid_actu` 
ADD `title_fr` VARCHAR( 100 ) NOT NULL AFTER `id_actu` ,
ADD `head_fr` VARCHAR( 255 ) NOT NULL AFTER `title_fr` ,
ADD `content_fr` LONGTEXT NOT NULL AFTER `head_fr` ,
ADD `title_en` VARCHAR( 100 ) NOT NULL AFTER `content_fr` ,
ADD `head_en` VARCHAR( 255 ) NOT NULL AFTER `title_en` ,
ADD `content_en` LONGTEXT NOT NULL AFTER `head_en` ,
ADD `title_es` VARCHAR( 100 ) NOT NULL AFTER `content_en` ,
ADD `head_es` VARCHAR( 255 ) NOT NULL AFTER `title_es` ,
ADD `content_es` LONGTEXT NOT NULL AFTER `head_es` ,
ADD `title_de` VARCHAR( 100 ) NOT NULL AFTER `content_es` ,
ADD `head_de` VARCHAR( 255 ) NOT NULL AFTER `title_de` ,
ADD `content_de` LONGTEXT NOT NULL AFTER `head_de` ,
ADD `title_it` VARCHAR( 100 ) NOT NULL AFTER `content_de` ,
ADD `head_it` VARCHAR( 255 ) NOT NULL AFTER `title_it` ,
ADD `content_it` LONGTEXT NOT NULL AFTER `head_it` ;

COMMIT; 

UPDATE `acid_actu`  SET 
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
`content_it`=`content`;

COMMIT;

ALTER TABLE `acid_actu`
  DROP `title`,
  DROP `head`,
  DROP `content`;

COMMIT; 


#PAGES
ALTER TABLE `acid_page` 
ADD `ident_fr` VARCHAR( 255 ) NOT NULL AFTER `id_page` ,
ADD `title_fr` VARCHAR( 100 ) NOT NULL AFTER `ident_fr` ,
ADD `content_fr` TEXT NOT NULL AFTER `title_fr` ,
ADD `ident_en` VARCHAR( 255 ) NOT NULL AFTER `content_fr` ,
ADD `title_en` VARCHAR( 100 ) NOT NULL AFTER `ident_en` ,
ADD `content_en` TEXT NOT NULL AFTER `title_en` ,
ADD `ident_es` VARCHAR( 255 ) NOT NULL AFTER `content_en` ,
ADD `title_es` VARCHAR( 100 ) NOT NULL AFTER `ident_es` ,
ADD `content_es` TEXT NOT NULL AFTER `title_es` ,
ADD `ident_de` VARCHAR( 255 ) NOT NULL AFTER `content_es` ,
ADD `title_de` VARCHAR( 100 ) NOT NULL AFTER `ident_de` ,
ADD `content_de` TEXT NOT NULL AFTER `title_de` ,
ADD `ident_it` VARCHAR( 255 ) NOT NULL AFTER `content_de` ,
ADD `title_it` VARCHAR( 100 ) NOT NULL AFTER `ident_it` ,
ADD `content_it` TEXT NOT NULL AFTER `title_it` ;

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
`content_it`=`content`;

COMMIT;

ALTER TABLE `acid_page`
  DROP `title`,
  DROP `ident`,
  DROP `content`;

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

