<?php
defined('_JEXEC') or die('Restricted access');

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');
JLoader::register('ZeitbankControllerUpdZeitbankBase', JPATH_COMPONENT . '/controllers/upd_zeitbank_base.php');

/**
 * Basis-Klasse für die Controller zum Editieren eines Journaleintrags der Zeitbank.
 */
abstract class ZeitbankControllerUpdJournalBase extends ZeitbankControllerUpdZeitbankBase
{

    /**
     * Schneidet den Kommentar auf die zulässige Länge ab.
     *
     * @param $kommentar string Kommentar
     * @return string ggf. gekürzter Kommentar
     */
    protected function cropKommentar($kommentar)
    {
        return ZeitbankFrontendHelper::cropText($kommentar, 1000);
    }

    /**
     * Liefert true, wenn der Benutzer den Eintrag bearbeiten darf. Wenn ID=0, wird immer true geliefert.
     *
     * @inheritdoc
     */
    protected function isEditAllowed($id)
    {
        if ($id == 0) {
            return true;
        }

        $model = $this->getModel();
        if (!$model->isOwner($id)) {
            JFactory::getApplication()->enqueueMessage(
                'Du bist nicht berechtigt, diesen Eintrag zu bearbeiten.', 'warning');
            return false;
        }
        return true;
    }

    /**
     * Im Standardfall keine besondere Prüfung gegenüber der Edit-Prüfung.
     *
     * @inheritdoc
     */
    protected function isSaveAllowed($id, $data)
    {
        return $this->isEditAllowed($id);
    }

    /**
     * Ein Journal-Eintrag kann nur über das Löschenformular gelöscht werden.
     *
     * @inheritdoc
     */
    protected function isDeleteAllowed($id)
    {
        JFactory::getApplication()->enqueueMessage('Das Löschen ist nicht zulässig.', 'warning');
        return false;
    }

}