-- Zeitkonto und Kategorie zum Verbuchen von Freiwilligenarbeit
INSERT INTO joomghjos_users (id,name,username,email,password,usertype,block,sendemail,registerdate,lastvisitdate,activation,params,lastresettime,resetcount)
VALUES (90, 'Zeitkonto Freiwilligenarbeit', 'freiwilligenarbeit', 'kein.email.freiwilligenarbeit@giesserei-gesewo.ch', 
       '2125a3e6b3b14af04857101dd8f9cef6:ZMdAY8AZFOaSHpWtFpCcQm7uPaf3aC1v', 'Registered', 1, 0, NOW(), NOW(), '', '', NOW(), 0);
       
INSERT INTO joomghjos_mgh_zb_kategorie (id, bezeichnung, ordering, gesamtbudget, nachtrag, user_id, admin_id, status) 
  VALUES (-1, 'Freiwilligenarbeit', 11, 0, 0, 90, 134, 1);
  
DROP VIEW joomghjos_mgh_zb_journal_quittiert_laufend;
DROP VIEW joomghjos_mgh_zb_journal_quittiert_vorjahr;
  
-- Views für Zeitbank anpassen, damit die Freiwilligenarbeit nicht enthalten ist
CREATE VIEW joomghjos_mgh_zb_journal_quittiert_laufend AS
SELECT *
FROM joomghjos_mgh_zb_journal
WHERE datum_quittung != '0000-00-00'
  AND admin_del = 0
  AND datum_antrag BETWEEN CONCAT(YEAR(NOW()), '-01-01') AND CONCAT(YEAR(NOW()), '-12-31')
  AND arbeit_id NOT IN (SELECT id FROM joomghjos_mgh_zb_arbeit WHERE kategorie_id = -1);
  
CREATE VIEW joomghjos_mgh_zb_journal_quittiert_vorjahr AS
SELECT *
FROM joomghjos_mgh_zb_journal
WHERE datum_quittung != '0000-00-00'
  AND admin_del = 0
  AND datum_antrag BETWEEN CONCAT(YEAR(NOW()) - 1, '-01-01') AND CONCAT(YEAR(NOW()) - 1, '-12-31')
  AND arbeit_id NOT IN (SELECT id FROM joomghjos_mgh_zb_arbeit WHERE kategorie_id = -1);
  
-- View enthält auch die Freiwilligenarbeit
CREATE VIEW joomghjos_mgh_zb_journal_quittiert_laufend_inkl_freiw AS
SELECT *
FROM joomghjos_mgh_zb_journal
WHERE datum_quittung != '0000-00-00'
  AND admin_del = 0
  AND datum_antrag BETWEEN CONCAT(YEAR(NOW()), '-01-01') AND CONCAT(YEAR(NOW()), '-12-31');

CREATE VIEW joomghjos_mgh_zb_journal_quittiert_vorjahr_inkl_freiw AS
SELECT *
FROM joomghjos_mgh_zb_journal
WHERE datum_quittung != '0000-00-00'
  AND admin_del = 0
  AND datum_antrag BETWEEN CONCAT(YEAR(NOW()) - 1, '-01-01') AND CONCAT(YEAR(NOW()) - 1, '-12-31');