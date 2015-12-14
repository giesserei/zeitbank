<?php
defined('_JEXEC') or die('Restricted access');

JLoader::register('BaseFormView', JPATH_COMPONENT . '/views/base_form_view.php');

/**
 * View-Klasse für das Edit-Formular "Eigenleistungen"
 * 
 * @author Steffen Förster
 */
class ZeitbankViewEigenleistungen extends BaseFormView {

  public function display($tpl = null) {
    $this->initView();
    return parent::display($tpl);
  }
  
  protected function getArbeitsgattungen() {
    $model = $this->getModel();
    return $model->getArbeitsgattungen();
  }

}