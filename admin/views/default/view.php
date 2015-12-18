<?php
defined('_JEXEC') or die;

/**
 * Zeitbank Default View
 */
class ZeitbankViewDefault extends JViewLegacy {

  /**
   * Constructor.
   *
   * @param   array  $config  Configuration array
   */
  public function __construct($config = null) {
    parent::__construct($config);
    $this->_addPath('template', $this->_basePath . '/views/default/tmpl');
  }

  /**
   * Display the view.
   *
   * @param   string  $tpl  Template
   *
   * @return  void
   */
  public function display($tpl = null) {
    // Get data from the model.
    $state = $this->get('State');
    $this->state       = &$state;

    $this->addToolbar();
    parent::display($tpl);
  }

  /**
   * Add the page title and toolbar.
   *
   * @return  void
   */
  protected function addToolbar() {
    $canDo = JHelperContent::getActions('com_zeitbank');
    JToolbarHelper::title('Zeitbank-Administration');

    if ($canDo->get('core.admin') || $canDo->get('core.options')) {
      JToolbarHelper::preferences('com_zeitbank');
      JToolbarHelper::divider();
    }

    // Render side bar.
    $this->sidebar = JHtmlSidebar::render();
  }
}
