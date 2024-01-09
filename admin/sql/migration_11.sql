-- Anpassungen für MySQL 8
-- Datums-Konstante '0000-00-00' in den Views durch 0 ersetzt.
-- Dies als provisorische Lösung, bis wir die entsprechenden Datums-Werte auf NULL umgestellt haben.

-- View, welche alle aktiven Mitglieder (Bewohner + Gewerbe) inkl. Siedlungsassistenz und Jugendmitglieder selektiert
-- Die E-Mail Adresse aus der User-Tabelle ist auch enthalten
CREATE OR REPLACE VIEW joomghjos_mgh_aktiv_mitglied AS
  SELECT m.*, u.email
     FROM joomghjos_mgh_mitglied m
        INNER JOIN joomghjos_users u ON m.userid = u.id
     WHERE m.typ IN (1,2,5,11) AND (m.austritt = 0 OR m.austritt > NOW())

-- Views für Zeitbank ohne Freiwilligenarbeit:
CREATE OR REPLACE VIEW joomghjos_mgh_zb_journal_quittiert_laufend AS
   SELECT *
      FROM joomghjos_mgh_zb_journal
      WHERE datum_quittung <> 0
         AND admin_del = 0
         AND datum_antrag BETWEEN CONCAT(YEAR(NOW()), '-01-01') AND CONCAT(YEAR(NOW()), '-12-31')
         AND arbeit_id NOT IN (SELECT id FROM joomghjos_mgh_zb_arbeit WHERE kategorie_id = -1);

CREATE OR REPLACE VIEW joomghjos_mgh_zb_journal_quittiert_vorjahr AS
   SELECT *
      FROM joomghjos_mgh_zb_journal
      WHERE datum_quittung <> 0
         AND admin_del = 0
         AND datum_antrag BETWEEN CONCAT(YEAR(NOW()) - 1, '-01-01') AND CONCAT(YEAR(NOW()) - 1, '-12-31')
         AND arbeit_id NOT IN (SELECT id FROM joomghjos_mgh_zb_arbeit WHERE kategorie_id = -1);

-- Views für Zeitbank mit Freiwilligenarbeit:
CREATE OR REPLACE VIEW joomghjos_mgh_zb_journal_quittiert_laufend_inkl_freiw AS
   SELECT *
      FROM joomghjos_mgh_zb_journal
      WHERE datum_quittung <> 0
         AND admin_del = 0
         AND datum_antrag BETWEEN CONCAT(YEAR(NOW()), '-01-01') AND CONCAT(YEAR(NOW()), '-12-31');

CREATE OR REPLACE VIEW joomghjos_mgh_zb_journal_quittiert_vorjahr_inkl_freiw AS
   SELECT *
      FROM joomghjos_mgh_zb_journal
      WHERE datum_quittung <> 0
         AND admin_del = 0
         AND datum_antrag BETWEEN CONCAT(YEAR(NOW()) - 1, '-01-01') AND CONCAT(YEAR(NOW()) - 1, '-12-31');
