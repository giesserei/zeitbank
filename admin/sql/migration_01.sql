--
-- Tabelle mit Angeboten und Suchen von Mitgliedern.
--
CREATE TABLE IF NOT EXISTS `joomghjos_mgh_zb_market_place` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `userid` bigint(20) NOT NULL,
  `erstellt` date NOT NULL COMMENT 'Erstellungsdatum',
  `ablauf` date NOT NULL COMMENT 'Ablaufdatum',
  termin date NULL COMMENT 'Ein Angebot kann einen Termin enthalten',
  `status` SMALLINT NOT NULL DEFAULT 1 COMMENT 'aktiv = 1 oder inaktiv = 0',
  `richtung` SMALLINT NOT NULL DEFAULT 1 COMMENT '1 = Stunden suchen / 2 = Stunden anbieten',
  `art` SMALLINT NOT NULL DEFAULT 1 COMMENT '1 = Arbeitsangebote / 2 = Stundentausch',
  `kategorie_id` bigint(20) NULL COMMENT 'ID der Zeitbank-Kategorie / Privat = NULL',
  `unterkategorie` SMALLINT NOT NULL DEFAULT 0 COMMENT 'nicht definiert = 0',
  `beschreibung` text NOT NULL COMMENT 'freier Text zur Beschreibung',
  `titel` varchar(100) NOT NULL COMMENT 'Bezeichnung',  
  `tags` varchar(1024) NOT NULL COMMENT 'Liste mit beschreibenden Tags',
   update_timestamp DATETIME NOT NULL,
   PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- Testdaten
INSERT INTO joomghjos_mgh_zb_market_place (userid, erstellt, ablauf, termin, status, art, richtung, kategorie_id, beschreibung, titel, tags, update_timestamp) 
    VALUES (134, '2014-02-10', '2014-03-10', null, 1, 1, 0, 3, 'Erstellung und Überarbeitung von Texten im Internet', 'RedakteurIn Webseite', 'Redakteur,Computer,schreiben', now());
INSERT INTO joomghjos_mgh_zb_market_place (userid, erstellt, ablauf, termin, status, art, richtung, kategorie_id, beschreibung, titel, tags, update_timestamp) 
    VALUES (134, '2014-02-09', '2014-03-09', null, 1, 2, 2, null, 'Ich esse gerne Sauerteigbrot aus Roggenmehl', 'Brot backen', 'Brot,backen', now());
INSERT INTO joomghjos_mgh_zb_market_place (userid, erstellt, ablauf, termin, status, art, richtung, kategorie_id, beschreibung, titel, tags, update_timestamp) 
    VALUES (134, '2014-02-08', '2014-03-08', null, 1, 2, 1, null, 'Ich kann leckeren Kuchen backen', 'Kuchen backen', 'Kuchen,backen', now());

