<?php
defined('_JEXEC') or die('Restricted access');

JLoader::register('BaseFormView', JPATH_COMPONENT . '/views/base_form_view.php');

/**
 * View-Klasse für das Confirm-Formular.
 * 
 * @author Steffen Förster
 */
class ZeitbankFormViewAntragLoeschen extends BaseFormView {

  /**
   * @var JObject
   */
  protected $antrag;

  public function display($tpl = null) {
    $this->initView();
    $this->antrag = $this->getModel()->getAntrag($this->getId());
    
    return parent::display($tpl);
  }
  
}