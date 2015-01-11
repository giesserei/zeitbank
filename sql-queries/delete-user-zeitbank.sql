-- Wird ein User aus Joomla entfernt, muss die User-ID durch den anonymen User 722 in der Zeitbank ersetzt werden


update `joomghjos_mgh_zb_journal` set gutschrift_userid = 722 where gutschrift_userid = 569;
update `joomghjos_mgh_zb_journal` set belastung_userid = 722 where belastung_userid = 569;