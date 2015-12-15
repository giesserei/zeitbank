<?php
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class ZeitbankController extends JControllerLegacy {

	/**
	 * Wenn keine View gewählt wurde, wird die View "welcome" gezeigt.
	 *
	 * @inheritdoc
	 */
	public function display($cachable = false, $urlparams = false) {
		$input = JFactory::getApplication()->input;

		// alle Variablen mit Vorgabewerten initialisieren
		$view   = $input->get('view', "empty");
		$task   = $input->get('task', 'default');

		if ($view === 'empty') {
		  $this->setRedirect(JRoute::_('index.php?option=com_zeitbank&view=welcome', false));
		  return false;
		}
		
		// Zugriffe prüfen
		if (! $this->isAuthorised($view, $task)) {
		  return false;
		}
		
		// Submenü erstellen
		$this->addSidebar($view);

		return parent::display($cachable, $urlparams);
	}
	
	// -------------------------------------------------------------------------
	// private section
	// -------------------------------------------------------------------------
	
	private function isAuthorised($view, $task) {
	  $user = JFactory::getUser();
	  $assetname = 'com_zeitbank';
	  
	  if ($view === 'journal') {
	    return $user->authorise('view.journal', $assetname);
	  }
	  if ($this->startsWith($task, 'journal')) {
	    return $user->authorise('edit.journal', $assetname);
	  }
		if ($view === 'arbeiten') {
			return $user->authorise('view.arbeit', $assetname);
		}
		if ($this->startsWith($task, 'arbeit')) {
			return $user->authorise('edit.arbeit', $assetname);
		}
		if ($view === 'kategorien') {
			return $user->authorise('view.kategorie', $assetname);
		}
		if ($this->startsWith($task, 'kategorie')) {
			return $user->authorise('edit.kategorie', $assetname);
		}
	  
	  return true;
	}
	
	/**
	 * Sidebar unter Berücksichtigung der Berechtigungen aufbauen.
	 *
	 * @param string $view
	 */
	private function addSidebar($view) {
	  $user = JFactory::getUser();
	  $assetname = 'com_zeitbank';

		JSubMenuHelper::addEntry(
      'Start',
      'index.php?option=com_zeitbank&view=welcome', $view == 'welcome'
    );
	  
	  if ($user->authorise('view.kategorie', $assetname)) {
			JSubMenuHelper::addEntry(
	      'Kategorien',
	      'index.php?option=com_zeitbank&view=kategorien', $view == 'kategorien'
	    );
	  }

		if ($user->authorise('view.arbeit', $assetname)) {
			JSubMenuHelper::addEntry(
					'Arbeiten',
					'index.php?option=com_zeitbank&controller=arbeiten&view=arbeiten', $view == 'arbeiten'
			);
		}

		if ($user->authorise('view.journal', $assetname)) {
			JSubMenuHelper::addEntry(
					'Journal',
					'index.php?option=com_zeitbank&view=journal', $view == 'journal'
			);
		}
	}

	private function startsWith($haystack, $needle) {
		$length = strlen($needle);
		return (substr($haystack, 0, $length) === $needle);
	}
}