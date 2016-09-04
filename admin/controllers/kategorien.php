<?php

defined('_JEXEC') or die('Restricted access');

class ZeitbankControllerKategorien extends JControllerAdmin
{
    /**
     * Constructor
     *
     * @param   array $config Optional configuration array
     */
    public function __construct($config = array())
    {
        parent::__construct($config);
    }

    /**
     * Proxy for getModel.
     *
     * @param   string $name The model name. Optional.
     * @param   string $prefix The class prefix. Optional.
     * @param   array $config Configuration array for model. Optional.
     *
     * @return  object  The model.
     */
    public function getModel($name = 'Kategorien', $prefix = 'ZeitbankModel', $config = array())
    {
        return parent::getModel($name, $prefix, array('ignore_request' => true));
    }

    /**
     * Save the manual order inputs from the menu items list view
     *
     * @return boolean
     */
    public function saveorder()
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        // Get the arrays from the Request
        $order = $this->input->post->get('order', null, 'array');
        $originalOrder = explode(',', $this->input->getString('original_order_values'));

        // Make sure something has changed
        if (!($order === $originalOrder)) {
            return parent::saveorder();
        } else {
            // Nothing to reorder
            $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false));

            return true;
        }
    }
}
