-- Namen und E-Mail Adresse der Ämtli-Administratoren mit nicht quittierten Buchungen
SELECT name, email, arbeitsgattung, MIN(datum_antrag), count(*) FROM ( 
  SELECT (SELECT u.name FROM joomghjos_users u WHERE u.id = a.admin_id) name, 
         (SELECT u.email FROM joomghjos_users u WHERE u.id = a.admin_id) email, 
         a.kurztext arbeitsgattung,
         j.datum_antrag
  FROM joomghjos_mgh_zb_journal j
    JOIN joomghjos_mgh_zb_arbeit a ON a.id = j.arbeit_id
  WHERE j.arbeit_id NOT IN (1, 3)
    AND j.datum_quittung = '0000-00-00'
    AND j.admin_del = 0
    AND j.datum_antrag BETWEEN CONCAT(YEAR(NOW()) - 1, '-01-01') AND CONCAT(YEAR(NOW()) - 1, '-12-31')
    AND j.abgelehnt = 0
  ) j1
GROUP BY name, email, arbeitsgattung
ORDER BY name, arbeitsgattung;

-- Namen und E-Mail Adresse der Personen, die noch einen privaten Stundentausch quittieren müssen
SELECT name, email, arbeitsgattung, MIN(datum_antrag), count(*) FROM ( 
  SELECT (SELECT u.name FROM joomghjos_users u WHERE u.id = j.belastung_userid) name, 
         (SELECT u.email FROM joomghjos_users u WHERE u.id = j.belastung_userid) email, 
         a.kurztext arbeitsgattung,
         j.datum_antrag
  FROM joomghjos_mgh_zb_journal j
    JOIN joomghjos_mgh_zb_arbeit a ON a.id = j.arbeit_id
  WHERE j.arbeit_id = 1
    AND j.datum_quittung = '0000-00-00'
    AND j.admin_del = 0
    AND j.datum_antrag BETWEEN CONCAT(YEAR(NOW()) - 1, '-01-01') AND CONCAT(YEAR(NOW()) - 1, '-12-31')
    AND j.abgelehnt = 0
  ) j1
GROUP BY name, email, arbeitsgattung
ORDER BY name, arbeitsgattung;

-- Saldo aller Mitglieder
SELECT id AS "User ID", typ AS "Typ", vorname AS "Vorname", nachname AS "Nachname",  
  (gutschrift_eigenleistungen + gutschrift_stundentausch + gutschrift_geschenk - belastung_stundentausch - belastung_geschenk) AS "Saldo", 
  gutschrift_eigenleistungen AS "(+) Eigenleistungen", gutschrift_stundentausch "(+) Stundentausch", belastung_stundentausch AS "(-) Stundentausch", 
  gutschrift_geschenk AS "(+) Stundengeschenk", belastung_geschenk AS "(-) Stundengeschenk",
  email, telefon, handy, adresse, plz, ort, wohnung, einzug, austritt, jahrgang, 
  IF (dispension_grad = 0, '-', dispension_grad) dispension_grad, 
  IF (monate_freistellung = 0, '-', monate_freistellung) monate_freistellung, 
  kommentar
FROM (
  SELECT m.userid AS ID, m.vorname, m.nachname, m.einzug, m.austritt, m.dispension_grad, zb_freistellung monate_freistellung, kommentar,
    u.email, m.telefon, m.handy, m.adresse, m.plz, m.ort, 
    (SELECT GROUP_CONCAT(DISTINCT objektid ORDER BY objektid DESC SEPARATOR ',') FROM joomghjos_mgh_x_mitglied_mietobjekt o WHERE o.userid = m.userid) wohnung,
    m.jahrgang, 
    CASE 
      WHEN (m.typ = 1 AND (m.austritt = '0000-00-00' OR m.austritt > '2014-12-31')) THEN 'Bewohner' 
      WHEN (m.typ = 1 AND m.austritt < '2015-01-01') THEN 'Bewohner ausgezogen' 
      ELSE 'Gewerbe' 
    END AS typ,
       
    ROUND(COALESCE((SELECT SUM(minuten) FROM joomghjos_mgh_zb_journal_quittiert_vorjahr 
                    WHERE gutschrift_userid = m.userid
                      AND arbeit_id NOT IN (SELECT id FROM joomghjos_mgh_zb_arbeit WHERE kategorie_id = -1)
                      AND arbeit_id NOT IN (1, 3)), 0) / 60, 2) gutschrift_eigenleistungen,
    ROUND(COALESCE((SELECT SUM(minuten) FROM joomghjos_mgh_zb_journal_quittiert_vorjahr 
                    WHERE gutschrift_userid = m.userid
                      AND arbeit_id = 1), 0) / 60, 2) gutschrift_stundentausch,
    ROUND(COALESCE((SELECT SUM(minuten) FROM joomghjos_mgh_zb_journal_quittiert_vorjahr 
                    WHERE belastung_userid = m.userid
                      AND arbeit_id = 1), 0) / 60, 2) belastung_stundentausch,
    ROUND(COALESCE((SELECT SUM(minuten) FROM joomghjos_mgh_zb_journal_quittiert_vorjahr 
                    WHERE gutschrift_userid = m.userid
                      AND arbeit_id = 3), 0) / 60, 2) gutschrift_geschenk,
    ROUND(COALESCE((SELECT SUM(minuten) FROM joomghjos_mgh_zb_journal_quittiert_vorjahr 
                    WHERE belastung_userid = m.userid
                      AND arbeit_id = 3), 0) / 60, 2) belastung_geschenk
       
  FROM joomghjos_mgh_mitglied m JOIN joomghjos_users u ON m.userid = u.id
  WHERE m.typ IN (1, 2)
    AND (m.austritt = '0000-00-00' OR m.austritt > '2014-01-01')
) t1
ORDER BY t1.typ, t1.nachname;


