ALTER TABLE joomghjos_mgh_zb_journal ADD COLUMN kommentar_antrag VARCHAR(1000) NULL;
ALTER TABLE joomghjos_mgh_zb_journal ADD COLUMN kommentar_quittung VARCHAR(1000) NULL;

UPDATE joomghjos_mgh_zb_journal j SET kommentar_antrag = (SELECT text FROM joomghjos_mgh_zb_antr_kommentar k WHERE j.id = k.journal_id);
UPDATE joomghjos_mgh_zb_journal j SET kommentar_quittung = (SELECT text FROM joomghjos_mgh_zb_quit_kommentar k WHERE j.id = k.journal_id);