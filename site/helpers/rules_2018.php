<?php

JLoader::register('Rules', JPATH_COMPONENT . '/helpers/rules.php');

/**
 * Liefert Regeln des Eigenleistungsreglements vom Jahr 2018.
 */
class Rules2018 implements Rules
{

    public function getStundenSollBewohner()
    {
        return 30;
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