<?php

JLoader::register('Rules', JPATH_COMPONENT . '/helpers/rules.php');

/**
 * Liefert Regeln des Eigenleistungsreglements vom Jahr 2016.
 */
class Rules2016 implements Rules
{

    public function getStundenSollBewohner()
    {
        return 33;
    }

    public function getStundenSollGewerbe()
    {
        return 0.2;
    }

    public function getStundenSollMinBewohner()
    {
        return 0;
    }

    public function getErsatzabgabe()
    {
        return 20;
    }

}