<?php
/*
 * Created on 30.05.2013
 * Status-Formular
 */
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_BASE.DS.'components'.DS.'com_zeitbank'.DS.'models'.DS.'zentralbank_func.php');

$db = JFactory::getDBO();
$param = JRequest::get( 'get' );

if($param['act'] == 'check'):
?>
<form action="index.php" method="POST" name="adminForm">
<div id="editcell">
	<h2>Status setzen - Zusammenstellung der Buchungen</h2>
	<p>Das Jahresbudget bekommt durch deine Bestätigung Gültigkeit und wird auf alle BewohnerInnen verteilt!<br />
	Bestätige die untenstehenden Vorgänge durch erneutes Klicken auf den Speichernbutton.</p>
<?php 

	// Erwachsener Bewohner zählen mit Berücksichtigung der Bezugstermine: Summe aller "Arbeitstage"
	// Summe aller Kategorien / Summer aller Arbeitstage = Stunden pro Person und Tag (aufrunden!)
	$sBT = summeBewohnerTage()+summeGewerbeTage();
	echo "<p>Total müssen <strong>".summeKategorien()."</strong> Pflichtstunden geleistet werden.</p>";
	echo "<p>Aufsummiert und Bezugsdatumsbereinigt haben wir ".round($sBT/365,2)." Personen (real: ".getAnzahlPersonen()."), welche die Pflichstunden abarbeiten.</p>";
	echo "<p>Das bedeutet, eine Person, die ein ganzes Jahr in Giesserei wohnt, müsste <strong>";
	if ($sBT > 0):
		echo ceil(summeKategorien()*365/$sBT);
	else:
		echo "!-!";
	endif;
	echo " Stunden</strong> pro Jahr arbeiten.</p>";
?>
	
</div>

<input type="hidden" name="ok_status" value="ok" />
<input type="hidden" name="aktueller_status" value="<?php echo $this->kategorien[0]->status; ?>" />
<input type="hidden" name="status" value="<?php echo $param['status']; ?>" />
<input type="hidden" name="option" value="com_zeitbank" />
<input type="hidden" name="view" value="" />
<input type="hidden" name="controller" value="zeitbank" />
<input type="hidden" name="task" value="change_status" />
</form>
<?php 

else:
?>	

<form action="index.php" method="POST" name="adminForm">
<div id="editcell">
	<h2>Status setzen - Reihenfolge chronologisch</h2>
	
	<input type="radio" id="status" name= "status" value="1" <?php 
		if($this->kategorien[0]->status == '1') echo "checked=\"checked\" ";
		if($this->kategorien[0]->status != '1' AND $this->kategorien[0]->status != '5') echo "disabled=\"1\" ";
		?> /> Jahresbudget einreichen <br />
	<input type="radio" id="status" name= "status" value="2" <?php 
		if($this->kategorien[0]->status == '2') echo "checked=\"checked\"";
		if($this->kategorien[0]->status > '2') echo "disabled=\"1\" ";
		?>/> Jahresbudget verteilt mit Buchen in die persönlichen Konti<br />
	<input type="radio" id="status" name= "status" value="3" <?php
		if($this->kategorien[0]->status == '3') echo "checked=\"checked\"";
		if($this->kategorien[0]->status < '2' OR $this->kategorien[0]->status > '3' ) echo "disabled=\"1\" ";
	?> /> Nachtragsphase <br />
	<input type="radio" id="status" name= "status" value="4" <?php
		if($this->kategorien[0]->status == '4') echo "checked=\"checked\"";
		if($this->kategorien[0]->status < '3' OR $this->kategorien[0]->status > '4' ) echo "disabled=\"1\" ";
	?> /> Nachtragsphase abschliessen mit Buchen auf die persönlichen Konti <br />
	<input type="radio" id="status" name= "status" value="5" <?php
		if($this->kategorien[0]->status == '5') echo "checked=\"checked\"";
		if($this->kategorien[0]->status < '4') echo "disabled=\"1\" ";
	?> /> Jahresabschluss mit automatischem Start des neuen Jahresbudgets <br />
</div>
	

<input type="hidden" name="aktueller_status" value="<?php echo $this->kategorien[0]->status; ?>" />
<input type="hidden" name="option" value="com_zeitbank" />
<input type="hidden" name="view" value="" />
<input type="hidden" name="controller" value="zeitbank" />
<input type="hidden" name="task" value="change_status" />

</form>
<?php 
endif;
?>