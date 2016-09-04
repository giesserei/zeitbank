<?php
defined('_JEXEC') or die;

/**
 * The Kategorie Controller
 */
class ZeitbankControllerKategorie extends JControllerForm
{
    /**
     * Class constructor.
     *
     * @param   array $config A named array of configuration variables.
     */
    public function __construct($config = array())
    {
        parent::__construct($config);

        // der Redirect nach dem Speichern der Änderungen geht sonst nach "kategories"
        $this->view_list = 'kategorien';
    }

}
