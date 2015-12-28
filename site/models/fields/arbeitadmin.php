<?php
defined('_JEXEC') or die('Restricted access');

JLoader::register('ZeitbankAuth', JPATH_COMPONENT . '/helpers/zeitbank_auth.php');

JFormHelper::loadFieldClass('list');

class JFormFieldArbeitAdmin extends JFormFieldList
{
    /**
     * Method to get the field options.
     *
     * @return  array  The field option objects.
     */
    protected function getOptions()
    {
        $options = array();

        $admins = $this->getAdmins();
        foreach ($admins as $option)
        {
            $value = (string) $option->id;
            $text = $option->name;

            $tmp = array(
                'value'    => $value,
                'text'     => $text,
                'disable'  => false,
                'class'    => '',
                'selected' => false,
                'checked'  => false
            );

            // Add the option object to the result set.
            $options[] = (object) $tmp;
        }

        reset($options);

        return $options;
    }

    private function getAdmins()
    {
        $db = JFactory::getDBO();
        $query =
            "SELECT u.* FROM #__mgh_zb_x_kat_arbeitadmin a
             LEFT JOIN #__users u ON a.user_id = u.id
             WHERE a.kat_id = " . ZeitbankAuth::getKategorieId() . "
             ORDER BY u.name";
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        return ($rows);
    }

}


