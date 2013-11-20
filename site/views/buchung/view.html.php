<?php 
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class ZeitbankViewBuchung extends JView {
  function display($tpl = null) {
    $model =& $this->getModel();
    $buchung = $model->getBuchung(JRequest::getVar('token'));
    $this->assignRef('Buchung',$buchung);
    parent::display($tpl);
  }
}
?> 
