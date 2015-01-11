-- 
-- Abfragen für Kennzahlen des Vorjahres
-- 

-- Liefert die Summe der verbuchten Arbeitstunden (ohne Freiwilligenarbeit) ohne den Stundentausch und die Geschenke.  
SELECT ROUND((sum(j.minuten) / 60), 0) stunden_verbucht
FROM joomghjos_mgh_zb_journal_quittiert_vorjahr j
WHERE arbeit_id NOT IN (1, 3);

-- Liefert die Summe der nicht quittierten Arbeitstunden (ohne Freiwilligenarbeit) ohne den Stundentausch und die Geschenke
SELECT ROUND((sum(j.minuten) / 60), 0) stunden_unquittiert
FROM joomghjos_mgh_zb_journal j
WHERE arbeit_id NOT IN (1, 3)
  AND datum_quittung = '0000-00-00'
  AND admin_del = 0
  AND datum_antrag BETWEEN CONCAT(YEAR(NOW()) - 1, '-01-01') AND CONCAT(YEAR(NOW()) - 1, '-12-31');

-- Liefert die Summen der verbuchten und quittierten Stunden je Arbeitskategorie (ohne Geschenke, Stundentausch und Freiwilligenarbeit)
SELECT ROUND((sum(j.minuten) / 60), 3) saldo, k.id, k.bezeichnung, k.gesamtbudget
FROM joomghjos_mgh_zb_journal_quittiert_vorjahr j 
  JOIN joomghjos_mgh_zb_arbeit a ON a.id = j.arbeit_id
  JOIN joomghjos_mgh_zb_kategorie k ON k.id = a.kategorie_id
WHERE k.id NOT IN (1, 10)
  GROUP BY k.bezeichnung
  ORDER BY k.bezeichnung;

-- Liefert die Summen der verbuchten und quittierten Stunden je Arbeitskategorie Geschenke, Stundentausch und Freiwilligenarbeit
SELECT ROUND((sum(j.minuten) / 60), 0) saldo, k.id, k.bezeichnung, k.gesamtbudget
FROM joomghjos_mgh_zb_journal_quittiert_vorjahr_inkl_freiw j
  JOIN joomghjos_mgh_zb_arbeit a ON a.id = j.arbeit_id
  JOIN joomghjos_mgh_zb_kategorie k ON k.id = a.kategorie_id
WHERE k.id IN (-1, 1, 10)
GROUP BY k.bezeichnung
ORDER BY k.bezeichnung;


-- Liefert die maximale und die durchschnittliche Dauer zwischen einer Buchung und der Quittierung (ohne Freiwilligenarbeit)
SELECT MAX(DATEDIFF(datum_quittung, datum_antrag)) max_dauer, ROUND(AVG(DATEDIFF(datum_quittung, datum_antrag)), 0) avg_dauer
FROM joomghjos_mgh_zb_journal_quittiert_vorjahr j
WHERE arbeit_id NOT IN (1, 3);