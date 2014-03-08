INSERT INTO joomghjos_mgh_zb_kategorie (id, bezeichnung, ordering, gesamtbudget, nachtrag, user_id, admin_id, status) 
  VALUES (10, 'Privat / Geschenk', 10, 0, 0, 0, 0, 1);

INSERT INTO joomghjos_mgh_zb_arbeit (id, kurztext, beschreibung, jahressoll, kadenz, pauschale, kategorie_id, aktiviert, ordering, admin_id, kommentar)
  VALUES (3, 'Stunden-Geschenk', 'Eintrag zum Verschenken von Stunden unter Mitgliedern', 0, 0, 0, 10, 1, 0, 62, 'Administration über Superuser');