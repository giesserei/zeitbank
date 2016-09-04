<?php

defined('_JEXEC') or die('Restricted access');

class ZeitbankController extends JControllerLegacy
{

    public function execute($task)
    {
        return parent::execute($task);
    }

    function display($cachable = false, $urlparams = Array())
    {
        parent::display();
    }

    function detail()
    {
        global $mainframe;
        JRequest::setVar('view', 'detail');
        parent::display();
        $mainframe->close();
    }

}