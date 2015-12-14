<?php
defined('_JEXEC') or die('Restricted access');

JLoader::register('BaseFormView', JPATH_COMPONENT . '/views/base_form_view.php');

/**
 * View-Klasse für das Edit-Formular.
 * 
 * @author Steffen Förster
 */
class ZeitbankFormViewAblehnung extends BaseFormView {

  /**
   * @var stdClass
   */
  protected $antrag;

  public function display($tpl = null) {
    $this->initView();
    $this->antrag = $this->getModel()->getAntrag($this->getId());
    
    return parent::display($tpl);
  }

  protected function isJournalAemtli() {
    return $this->getModel()->isJournalAemtli($this->antrag->id);
  }
  
}