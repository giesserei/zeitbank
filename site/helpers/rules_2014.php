<?php

JLoader::register('Rules', JPATH_COMPONENT . '/helpers/rules.php');

/**
 * Liefert Regeln des Eigenleistungsreglements vom Jahr 2014.
 */
class Rules2014 implements Rules
{

    public function getStundenSollBewohner()
    {
        return 36;
    }

    public function getStundenSollGewerbe()
    {
        return 0.2;
    }

    public function getStundenSollMinBewohner()
    {
        return 0;
    }

    public function getStundenSollAusbildung()
    {
        return 12;
    }

    public function getErsatzabgabe()
    {
        return 20;
    }

}