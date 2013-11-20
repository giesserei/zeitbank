<?php 
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class ZeitbankViewZeitbank extends JView {
  function display($tpl = null) {
    $model =& $this->getModel();

    $quittierungen = $model->getOffeneQuittierungen();
    $antraege = $model->getOffeneAntraege();
    $journal = $model->getUserJournal();
    $saldo_vorjahr = $model->getSaldoVorjahr();

    $this->assignRef('quittierungen',$quittierungen);
    $this->assignRef('antraege',$antraege);
    $this->assignRef('journal',$journal);
    $this->assignRef('saldo_vorjahr',$saldo_vorjahr);
    
    parent::display($tpl);
  }
}
?> 
