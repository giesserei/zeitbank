<?php 
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class ZeitbankViewQuittung_Amt extends JView {
  function display($tpl = null) {
    $model =& $this->getModel();

    $quittierungen = $model->getOffeneQuittierungen();

    $this->assignRef('quittierungen',$quittierungen);
    
    parent::display($tpl);
  }
}
?> 
