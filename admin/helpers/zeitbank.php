<?php
/**
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Banners component helper.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_banners
 * @since		1.6
 */
class ZeitbankHelper
{
	/**
	 * Configure the Linkbar.
	 *
	 * @param	string	The name of the active view.
	 *
	 * @return	void
	 * @since	1.6
	 */
	public static function addSubmenu($vName)
	{
		JSubMenuHelper::addEntry(
			JText::_('COM_ZEITBANK_STATUS'),
			'index.php?option=com_zeitbank&controller=zeitbank&view=status&task=change_status',
			$vName == 'status'
		);

		JSubMenuHelper::addEntry(
			JText::_('COM_ZEITBANK_KATEGORIEN'),
			'index.php?option=com_zeitbank&controller=kategorien&view=kategorien',
			$vName == 'kategorien'
		);

		JSubMenuHelper::addEntry(
			JText::_('COM_ZEITBANK_ARBEITEN'),
			'index.php?option=com_zeitbank&controller=arbeiten&view=arbeiten',
			$vName == 'arbeiten'
		);

		JSubMenuHelper::addEntry(
			JText::_('COM_ZEITBANK_JOURNAL'),
			'index.php?option=com_zeitbank&controller=zeitbank&view=journal',
			$vName == 'journal'
		);
	}

};