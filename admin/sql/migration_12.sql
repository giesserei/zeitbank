-- Umstellung der Datums-Feld-Werte von 0000-00-00 auf NULL.

-- Tabelle mgh_zb_journal
set sql_mode = '';
alter table joomghjos_mgh_zb_journal
   modify column datum_quittung date;
set sql_mode = ''; update joomghjos_mgh_zb_journal set datum_quittung = null where datum_quittung = '0000-00-00'; -- 83

-- Views anpassen

-- View, welche alle aktiven Mitglieder (Bewohner + Gewerbe) inkl. Siedlungsassistenz und Jugendmitglieder selektiert
-- Die E-Mail Adresse aus der User-Tabelle ist auch enthalten
CREATE OR REPLACE VIEW joomghjos_mgh_aktiv_mitglied AS
  SELECT m.*, u.email
     FROM joomghjos_mgh_mitglied m
        INNER JOIN joomghjos_users u ON m.userid = u.id
     WHERE m.typ IN (1,2,5,11) AND (m.austritt is null OR m.austritt > NOW())

-- Views für Zeitbank ohne Freiwilligenarbeit:
CREATE OR REPLACE VIEW joomghjos_mgh_zb_journal_quittiert_laufend AS
   SELECT *
      FROM joomghjos_mgh_zb_journal
      WHERE datum_quittung is not null
         AND admin_del = 0
         AND datum_antrag BETWEEN CONCAT(YEAR(NOW()), '-01-01') AND CONCAT(YEAR(NOW()), '-12-31')
         AND arbeit_id NOT IN (SELECT id FROM joomghjos_mgh_zb_arbeit WHERE kategorie_id = -1);

CREATE OR REPLACE VIEW joomghjos_mgh_zb_journal_quittiert_vorjahr AS
   SELECT *
      FROM joomghjos_mgh_zb_journal
      WHERE datum_quittung is not null
         AND admin_del = 0
         AND datum_antrag BETWEEN CONCAT(YEAR(NOW()) - 1, '-01-01') AND CONCAT(YEAR(NOW()) - 1, '-12-31')
         AND arbeit_id NOT IN (SELECT id FROM joomghjos_mgh_zb_arbeit WHERE kategorie_id = -1);

-- Views für Zeitbank mit Freiwilligenarbeit:
CREATE OR REPLACE VIEW joomghjos_mgh_zb_journal_quittiert_laufend_inkl_freiw AS
   SELECT *
      FROM joomghjos_mgh_zb_journal
      WHERE datum_quittung is not null
         AND admin_del = 0
         AND datum_antrag BETWEEN CONCAT(YEAR(NOW()), '-01-01') AND CONCAT(YEAR(NOW()), '-12-31');

CREATE OR REPLACE VIEW joomghjos_mgh_zb_journal_quittiert_vorjahr_inkl_freiw AS
   SELECT *
      FROM joomghjos_mgh_zb_journal
      WHERE datum_quittung is not null
         AND admin_del = 0
         AND datum_antrag BETWEEN CONCAT(YEAR(NOW()) - 1, '-01-01') AND CONCAT(YEAR(NOW()) - 1, '-12-31');
