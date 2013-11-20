<?php
/*
 * Created on 27.12.2010
 *
 */
 defined('_JEXEC') or die('Restricted access');

 jimport('joomla.application.component.view');

 class ZeitbankViewStatus extends JView {
    function display($tpl = null) {
        JToolBarHelper::title('Zeitbank: Status','user.png');
        JToolBarHelper::preferences('com_zeitbank');
        JToolBarHelper::save();
        JToolBarHelper::cancel();

        $kategorien =& $this->get('Data');
        $this->assignRef('kategorien',$kategorien);

        parent::display($tpl);
    }
 }
?>
