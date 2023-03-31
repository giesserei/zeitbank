-- Index zum Verschnellern der Ämtli-Verwaltung für die Bereichsverantwortlichen.
-- Die zusätzlichen Felder sind nur im Index, damit die Haupt-Tabelle nicht benötigt wird.
-- Betrifft SQL-Query in site\models\arbeiten.php getArbeiten(), Subqueries mit select sum(minuten).
-- Ist sehr wahrscheinlich auch für weitere ähnliche Queries nützlich.

create index index1 on joomghjos_mgh_zb_journal (arbeit_id, datum_antrag, admin_del, datum_quittung, minuten)
