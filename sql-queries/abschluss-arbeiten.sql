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
  ) j1
GROUP BY name, email, arbeitsgattung
ORDER BY name, arbeitsgattung;
