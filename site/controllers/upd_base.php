<?php
defined('_JEXEC') or die('Restricted access');

JLoader::register('ZeitbankFrontendHelper', JPATH_COMPONENT . '/helpers/zeitbank_frontend.php');
JLoader::register('ZeitbankConst', JPATH_COMPONENT . '/helpers/zeitbank_const.php');
JLoader::register('ZeitbankAuth', JPATH_COMPONENT . '/helpers/zeitbank_auth.php');

/**
 * Basis-Klasse für die Controller zum Editieren von Datenbank-Objekten.
 */
abstract class ZeitbankControllerUpdBase extends JControllerForm
{

    /**
     * Führt nach ein paar Vorarbeiten einen Redirect auf die View durch, welche das Formular anzeigt.
     * @inheritdoc
     */
    public function edit($key = null, $urlVar = null)
    {
        if (!$this->checkGeneralPermission()) {
            return false;
        }

        $this->saveMenuIdInSession();
        $this->clearSessionData();
        $this->performPreEdit();

        $id = $this->getId();
        if (!$this->isEditAllowed($id)) {
            return false;
        }

        $this->redirectEditView($id);
        return true;
    }

    /**
     * Speichert die Formulardaten in der Datenbank.
     * @inheritdoc
     */
    public function save($key = null, $urlVar = null)
    {
        if (!$this->checkGeneralPermission()) {
            return false;
        }

        // Form-Token prüfen -> Token wird in Template gesetzt
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $formData = $this->getFormData();
        $id = $formData['id'];

        if (!$this->isEditAllowed($id)) {
            return false;
        }

        // Validierung -> Validierungsmeldungen werden direkt ausgegeben
        $validateResult = $this->validateData($formData, $id);
        if ($validateResult === false) {
            return false;
        }

        // Daten Speichern
        if ($this->processSave($validateResult, $id)) {
            $this->clearSessionData();
            $this->redirectSuccessView($id);
            return true;
        }

        return false;
    }

    /**
     * Löscht einen Eintrag.
     */
    public function delete()
    {
        if (!$this->checkGeneralPermission()) {
            return false;
        }

        $this->saveMenuIdInSession();

        $id = $this->getId();
        if (!$this->isEditAllowed($id)) {
            return false;
        }

        $model = $this->getModel();
        if ($model->deleteItem($id)) {
            $this->redirectSuccessView($id);
            return true;
        } else {
            $this->perfomOnDeleteError();
            return false;
        }
    }

    /**
     * Prüft, ob der Benutzer grundsätzlich zum Editieren eines Objektes berechtigt ist. Es wird nicht geprüft, ob der
     * Benutzer ein bestimmtes Objekt bearbeiten darf. Eine Fehlermeldung wird durch die Methode bereitgestellt.
     *
     * @return bool true, wenn die Berechtigung vorhanden ist
     */
    abstract protected function checkGeneralPermission();

    /**
     * Löscht die Session-Daten, welche im Edit-Prozess gespeichert wurden.
     */
    abstract protected function clearSessionData();

    /**
     * Abgeleitete Controller können über diese Methode zusätzliche Verarbeitungslogik vor der Anzeige des Edit-Formulars
     * ausführen.
     */
    protected function performPreEdit()
    {
    }

    /**
     * Abgeleitete Controller können über diese Methode zusätzliche Verarbeitungslogik ausführen, wenn das Löschen eines
     * Objektes fehlgeschlagen ist.
     */
    protected function perfomOnDeleteError()
    {
    }

    /**
     * Abgeleitete Controller können über diese Methode zusätzliche Verarbeitungslogik vor dem Speichern des Objektes
     * ausführen => z.B. Objekt-Daten verändern.
     *
     * @param $data array
     * @return array
     */
    protected function modifyDataBeforeSave($data)
    {
        return $data;
    }

    /**
     * Liefert true, wenn der Benutzer den Eintrag editieren/löschen darf.
     *
     * @param $id int ID des Objektes, welches bearbeitet/gelöscht werden soll
     * @return boolean
     */
    abstract protected function isEditAllowed($id);

    /**
     * Liefert ein Array mit den Formdaten zurück, die gespeichert werden dürfen.
     * -> Verhindert, dass nicht zulässige Tabellen-Felder verändert werden.
     *
     * @param $data array
     * @return array
     */
    abstract protected function filterFormFields($data);

    /**
     * Liefert true, wenn bei einer fehlgeschlagenen Validierung oder Speicherung die Date für eine Anzeige in der
     * Session gespeichert werden sollen.
     */
    abstract protected function isSaveDataInSession();

    /**
     * Speichert die übergebenen Daten in der Session.
     *
     * @param $data mixed Zu speichernde Daten
     */
    abstract protected function saveDataInSession($data);

    /**
     * Liefert den Namen der View.
     */
    abstract protected function getViewName();

    /**
     * Führt einen Redirect auf die Seite durch, die nach dem Speichern angezeigt werden soll.
     * In abgeleiteten Controllern, kann die anzuzeigende Seite abhängig vom bearbeiteten Objekt sein.
     * Daher kann die ID des Eintrags übergeben werden.
     *
     * @param $id int
     */
    protected function redirectSuccessView($id)
    {
        $app = JFactory::getApplication();
        $menuId = $app->getUserState(ZeitbankConst::SESSION_KEY_ZEITBANK_MENU_ID);
        $this->setRedirect(
            JRoute::_('index.php?option=com_zeitbank&view=zeitbank&Itemid=' . $menuId, false)
        );
    }

    /**
     * Auf die Edit-View weiterleiten.
     *
     * @param $id int ID der Buchung
     */
    protected function redirectEditView($id)
    {
        $app = JFactory::getApplication();
        $menuId = $app->getUserState(ZeitbankConst::SESSION_KEY_ZEITBANK_MENU_ID);
        $this->setRedirect(
            JRoute::_('index.php?option=com_zeitbank&view=' . $this->getViewName() . '&layout=edit&id=' . $id . '&Itemid=' . $menuId, false)
        );
    }

    /**
     * Speichert die Daten. Tritt ein Fehler auf, werden die Eingaben in der Session gespeichert damit diese erneut
     * angezeigt werden können.
     *
     * Fehlermeldungen werden direkt angezeigt.
     *
     * @param $data array
     * @param $id ID des Objektes
     *
     * @return boolean True, wenn das Speichern erfolgreich war
     */
    protected function processSave($data, $id)
    {
        $app = JFactory::getApplication();

        $model = $this->getModel();
        $data = $this->modifyDataBeforeSave($data);

        // Fehlermeldung dem Benutzer anzeigen
        if (!$model->saveItem($data, $id)) {
            $errors = $model->getErrors();
            foreach ($errors as $error) {
                $app->enqueueMessage($error, 'warning');
            }

            if ($this->isSaveDataInSession()) {
                $this->saveDataInSession($data);
            }

            $this->redirectEditView($id);
            return false;
        }

        return true;
    }

    /**
     * Liefert des Objektes, welches bearbeitet/gelöscht werden soll.
     */
    protected function getId()
    {
        $app = JFactory::getApplication();
        $input = $app->input;
        return $input->get("id", 0, "INT");
    }

    // -------------------------------------------------------------------------
    // private section
    // -------------------------------------------------------------------------

    /**
     * Speichert die ID des Zeitbank-Menüs in der User-Session.
     */
    private function saveMenuIdInSession()
    {
        $app = JFactory::getApplication();
        $jinput = $app->input;
        $menuId = $jinput->get("Itemid", "0", "INT");
        $app->setUserState(ZeitbankConst::SESSION_KEY_ZEITBANK_MENU_ID, $menuId);
    }

    /**
     * Holt die Formulardaten aus dem JInput.
     */
    private function getFormData()
    {
        $app = JFactory::getApplication();
        $input = $app->input;
        $model = $this->getModel();
        $form = $model->getForm(array(), false);
        $data = $input->get($form->getFormControl(), '', 'array');

        return $this->filterFormFields($data);
    }

    /**
     * Prüft, ob die Eingaben korrekt sind. Sind die Eingaben nicht korrekt, werden die
     * Eingaben in der Session gespeichert, damit diese erneut angezeigt werden können.
     *
     * Validierungsmeldungen werden direkt ausgegeben.
     *
     * @return mixed  Array mit gefilterten Daten, wenn alle Daten korrekt sind; sonst false
     */
    private function validateData($data, $id)
    {
        $model = $this->getModel();
        $form = $model->getForm($data, false);

        $validateResult = $model->validate($form, $data);

        // Nur die ersten drei Fehler dem Benutzer anzeigen
        if ($validateResult === false) {
            if ($this->isSaveDataInSession()) {
                $this->saveDataInSession($data);
            }

            $this->redirectEditView($id);

            return false;
        }
        return $validateResult;
    }

}