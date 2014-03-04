-- Neue Views für Zeitbank
CREATE VIEW joomghjos_mgh_zb_journal_quittiert_laufend AS
SELECT *
FROM joomghjos_mgh_zb_journal
WHERE datum_quittung != '0000-00-00'
  AND admin_del = 0
  AND datum_antrag BETWEEN CONCAT(YEAR(NOW()), '-01-01') AND CONCAT(YEAR(NOW()), '-12-31');
  
CREATE VIEW joomghjos_mgh_zb_journal_quittiert_vorjahr AS
SELECT *
FROM joomghjos_mgh_zb_journal
WHERE datum_quittung != '0000-00-00'
  AND admin_del = 0
  AND datum_antrag BETWEEN CONCAT(YEAR(NOW()) - 1, '-01-01') AND CONCAT(YEAR(NOW()) - 1, '-12-31');