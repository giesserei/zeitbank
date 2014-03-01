-- 
-- Zusätzliche Felder
-- Statt der Kategorie wird die Arbeitsgattung gespeichert
--
ALTER TABLE joomghjos_mgh_zb_market_place DROP COLUMN kategorie_id;

ALTER TABLE joomghjos_mgh_zb_market_place ADD COLUMN arbeit_id bigint(20) NULL COMMENT 'ID der Arbeitskategorie / Privattausch = NULL';

ALTER TABLE joomghjos_mgh_zb_market_place ADD COLUMN anforderung VARCHAR(255) NULL COMMENT 'Anforderungen für die Arbeit / Privattausch = NULL';

ALTER TABLE joomghjos_mgh_zb_market_place ADD COLUMN zeit VARCHAR(255) NULL COMMENT 'Wann soll die Arbeit ausgeführt werden / Privattausch = NULL';

ALTER TABLE joomghjos_mgh_zb_market_place ADD COLUMN aufwand VARCHAR(255) NULL COMMENT 'Welcher Aufwand darf verbucht werden / Privattausch = NULL';