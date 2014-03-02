-- View, welche alle aktiven Mitglieder (Bewohner + Gewerbe) selektiert
-- Die E-Mail Adresse aus der User-Tabelle ist auch enthalten
CREATE VIEW joomghjos_mgh_aktiv_mitglied AS
  SELECT m.*, u.email
  FROM joomghjos_mgh_mitglied m
    JOIN joomghjos_users u ON m.userid = u.id
  WHERE m.typ IN (1,2) AND (m.austritt = '0000-00-00' OR m.austritt > NOW())