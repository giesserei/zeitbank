-- alte Submenu-Einträge der Zeitbank löschen
DELETE FROM joomghjos_menu
WHERE id IN (214, 215, 216);

-- Eigenschaften anpassen des Zeitbank-Menüs
UPDATE dbjoomla.joomghjos_menu
SET title = 'COM_ZEITBANK_ADMIN', alias = 'Zeitbank Administration',
    path = 'com-zeitbank-admin', link = 'index.php?option=com_zeitbank',
    img = 'components/com_zeitbank/assets/zeitbank.png'
WHERE id = 201;
