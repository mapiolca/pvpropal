<?php
/* Copyright (C) 2025           Pierre Ardoin                         <developpeur@lesmetiersdubatiment.fr>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */
/*
 * Trigger dedicated to PvPropal module / Déclencheur dédié au module PvPropal
 */

require_once DOL_DOCUMENT_ROOT.'/core/triggers/dolibarrtriggers.class.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/price.lib.php';
// Load proposal class for parent refresh / Charger la classe proposition pour rafraîchir le parent
require_once DOL_DOCUMENT_ROOT.'/comm/propal/class/propal.class.php';

/**
	* Trigger class for PvPropal / Classe de déclencheur pour PvPropal
	*/
class Interface99ModpvpropalPvpropaltrigger extends DolibarrTriggers
{
	/** @var string Family for the trigger / Famille du déclencheur */
	public $family = 'crm';

	/** @var string Description of the trigger / Description du déclencheur */
	public $description = 'Synchronise les indicateurs PV des propositions / Synchronizes PV indicators on proposals';

	/** @var string Picto displayed for the trigger / Picto affiché pour le déclencheur */
	public $picto = 'propal';

	/**
		* Constructor / Constructeur
		*
		* @param DoliDB $db Database handler / Gestionnaire de base de données
		*/
	public function __construct($db)
	{
	$this->db = $db;
	}

	/**
		* Run the trigger / Exécute le déclencheur
		*
		* @param string    $action Action code / Code de l'action
		* @param CommonObject $object Current object / Objet courant
		* @param User      $user   Current user / Utilisateur courant
		* @param Translate $langs  Translations handler / Gestionnaire de traductions
		* @param Conf      $conf   Dolibarr configuration / Configuration Dolibarr
		* @return int                  Status code / Code de statut
		*/
	public function runTrigger($action, $object, $user, $langs, $conf)
	{
	if (empty($conf->pvpropal->enabled)) {
		return 0;
	}

	$handledActions = array(
		'PROPAL_CREATE',
		'PROPAL_MODIFY',
		'PROPAL_VALIDATE',
		'PROPAL_ADD_LINE',
		'PROPAL_UPDATE_LINE',
		'PROPAL_DELETE_LINE',
		'LINEPROPAL_INSERT',
		'LINEPROPAL_UPDATE',
		'LINEPROPAL_DELETE',
		'PROPAL_CLOSE_SIGNED',
		'PROPAL_CLOSE_REFUSED',
		'PROPAL_REOPEN',
		'PROPAL_CLASSIFY_BILLED',
		'PROPAL_CLASSIFY_BILLED_PARTIALLY'
	);

	if (!in_array($action, $handledActions, true)) {
		return 0;
	}

	if (!is_object($object)) {
		return 0;
	}

	$propal = $this->resolvePropalFromObject($object);
	if (!$propal) {
		return 0;
	}

	$result = $this->updatePropalMetrics($propal, $conf);
	if ($result < 0) {
		return -1;
	}

	return 0;
	}

	/**
	 * Resolve the related proposal object / Détermine l'objet proposition associé
	 *
	 * @param CommonObject|CommonObjectLine $object Source trigger object / Objet source du déclencheur
	 * @return Propal|null                          Loaded proposal or null / Proposition chargée ou nulle
	 */
	private function resolvePropalFromObject($object)
	{
		// Handle proposal objects directly / Gérer directement les objets proposition
		if (!empty($object->element) && $object->element === 'propal') {
			return $object;
		}

		// Fallback to proposal line to refresh parent / Repli sur la ligne de proposition pour rafraîchir le parent
		if (!empty($object->element) && $object->element === 'propaldet' && !empty($object->fk_propal)) {
			$propal = new Propal($this->db);
			if ($propal->fetch((int) $object->fk_propal) > 0) {
				return $propal;
			}
		}

		return null;
	}

	/**
	 * Update proposal extra fields / Met à jour les champs supplémentaires de la proposition
	 *
		* @param Propal $object Proposal object / Objet proposition
		* @param Conf   $conf   Dolibarr configuration / Configuration Dolibarr
		* @return int              Status code / Code de statut
		*/
	private function updatePropalMetrics($object, $conf)
	{
	if (empty($object->lines) && method_exists($object, 'fetch_lines')) {
		$object->fetch_lines();
	}

	if (method_exists($object, 'fetch_optionals') && !isset($object->array_options)) {
		$object->fetch_optionals($object->id);
	}

	$totalPower = 0.0;
	$totalCost = 0.0;

	if (is_array($object->lines)) {
		foreach ($object->lines as $line) {
		$qty = (float) (!empty($line->qty) ? $line->qty : 0);
		$unitCost = $this->resolveUnitCost($line);
		$totalCost += $unitCost * $qty;

		$linePower = $this->resolveLinePower($line);
		$totalPower += $linePower;
		}
	}

	$totalSale = price2num((float) (!empty($object->total_ht) ? $object->total_ht : 0), 'MT');
	$totalPower = price2num($totalPower, 'CU');
	$totalCost = price2num($totalCost, 'MT');
	$margin = price2num($totalSale - $totalCost, 'MT');
	$marginRate = ($totalSale > 0 ? price2num(($margin / $totalSale) * 100, 'MU') : 0.0);
	$salePerWc = ($totalPower > 0 ? price2num($totalSale / $totalPower, 'MU') : 0.0);
	$costPerWc = ($totalPower > 0 ? price2num($totalCost / $totalPower, 'MU') : 0.0);

	list($targetMargin, $targetRate) = $this->computeTargetMargin($totalPower, $totalSale);

	if (!isset($object->array_options) || !is_array($object->array_options)) {
		$object->array_options = array();
	}

	$object->array_options['options_ppvpc'] = $totalPower;
	$object->array_options['options_ppvpvwc'] = $salePerWc;
	$object->array_options['options_ppvpawc'] = $costPerWc;
	$object->array_options['options_ppvtxmarge'] = $marginRate;
	$object->array_options['options_ppvmarge'] = $margin;
	$object->array_options['options_ppvmargecible'] = $targetMargin;
	$object->array_options['options_ppvtxmargecible'] = $targetRate;

	if (method_exists($object, 'insertExtraFields')) {
		$result = $object->insertExtraFields();
		if ($result < 0) {
			$this->error = $object->error;
			return -1;
		}
	}

	return 0;
	}

	/**
		* Resolve unit cost for a line / Détermine le coût unitaire d'une ligne
		*
		* @param CommonObjectLine $line Proposal line / Ligne de proposition
		* @return float                       Unit cost / Coût unitaire
		*/
	private function resolveUnitCost($line)
	{
	$candidates = array('pa_ht', 'buy_price_ht', 'pa_ttc', 'fk_fournprice', 'subprice', 'multicurrency_subprice', 'cost_price');
	foreach ($candidates as $candidate) {
		if (isset($line->$candidate) && $line->$candidate !== '' && $line->$candidate !== null) {
		$value = (float) $line->$candidate;
		if ($value != 0.0) {
			return price2num($value, 'MU');
		}
		}
	}

	if (!empty($line->fk_product)) {
		$product = $this->fetchProductData($line->fk_product);
		if (!empty($product['cost_price'])) {
		return price2num($product['cost_price'], 'MU');
		}
		if (!empty($product['pmp'])) {
		return price2num($product['pmp'], 'MU');
		}
	}

	return 0.0;
	}

	/**
		* Resolve power contributed by a line / Détermine la puissance apportée par une ligne
		*
		* @param CommonObjectLine $line Proposal line / Ligne de proposition
		* @return float                       Total power for the line / Puissance totale de la ligne
		*/
	private function resolveLinePower($line)
	{
	if (empty($line->fk_product)) {
		return 0.0;
	}

	$product = $this->fetchProductData($line->fk_product);
	if (empty($product['is_pv']) || empty($product['power'])) {
		return 0.0;
	}

	$qty = (float) (!empty($line->qty) ? $line->qty : 0);
	return price2num($product['power'] * $qty, 'CU');
	}

	/**
		* Fetch product data with cache / Récupère les données produit avec cache
		*
		* @param int $productId Product identifier / Identifiant produit
		* @return array<string, mixed>          Data array / Tableau de données
		*/
	private function fetchProductData($productId)
	{
	static $cache = array();

	$productId = (int) $productId;
	if ($productId <= 0) {
		return array();
	}

	if (isset($cache[$productId])) {
		return $cache[$productId];
	}

	$sql = "SELECT p.rowid, p.fk_product_nature, p.pmp, p.cost_price, pn.code as naturecode, pn.rowid as naturerowid, ef.modulepvpc as modulepvpc".
		" FROM ".$this->db->prefix()."product as p".
		" LEFT JOIN ".$this->db->prefix()."product_extrafields as ef ON ef.fk_object = p.rowid".
		" LEFT JOIN ".$this->db->prefix()."c_product_nature as pn ON pn.rowid = p.fk_product_nature".
		" WHERE p.rowid = ".$productId;

	$data = array('is_pv' => false, 'power' => 0.0, 'pmp' => 0.0, 'cost_price' => 0.0);
	$resql = $this->db->query($sql);
	if ($resql) {
		$obj = $this->db->fetch_object($resql);
		if ($obj) {
		$power = isset($obj->modulepvpc) ? (float) $obj->modulepvpc : 0.0;
		$natureCode = !empty($obj->naturecode) ? $obj->naturecode : '';
		// Resolve the nature identifier from dictionary data / Résout l'identifiant de nature depuis le dictionnaire
		$natureId = isset($obj->naturerowid) ? (int) $obj->naturerowid : (isset($obj->fk_product_nature) ? (int) $obj->fk_product_nature : 0);

		// Determine PV nature using stored identifiers / Détermine la nature PV via les identifiants stockés
		$expectedId = (int) getDolGlobalInt('PVPROPAL_NATURE_MOD_ID', 0);
		$expectedCode = getDolGlobalString('PVPROPAL_NATURE_MOD_CODE', '');

		$natureMatches = false;
		if ($expectedId > 0 && $natureId > 0 && $natureId === $expectedId) {
			$natureMatches = true;
		} elseif (!empty($expectedCode) && (string) $natureCode === (string) $expectedCode) {
			$natureMatches = true;
		}
		$data['is_pv'] = ($natureMatches && $power > 0);
		$data['power'] = price2num($power, 'CU');
		$data['pmp'] = isset($obj->pmp) ? (float) $obj->pmp : 0.0;
		$data['cost_price'] = isset($obj->cost_price) ? (float) $obj->cost_price : 0.0;
		}
	}

	$cache[$productId] = $data;
	return $data;
	}

	/**
		* Compute target margin indicators / Calcule les indicateurs de marge cible
		*
		* @param float $totalPower Total PV power / Puissance totale PV
		* @param float $totalSale  Total sale amount / Montant total de vente
		* @return array{0: float, 1: float}      Target margin and rate / Marge cible et taux
		*/
	private function computeTargetMargin($totalPower, $totalSale)
	{
	$entry = $this->getTargetEntry($totalPower);
	$perWc = isset($entry['amount']) ? $entry['amount'] : null;
	$percent = isset($entry['percent']) ? $entry['percent'] : null;

	$targetMargin = 0.0;
	if ($perWc !== null) {
		$targetMargin = price2num($perWc * $totalPower, 'MT');
	} elseif ($percent !== null && $totalSale > 0) {
		$targetMargin = price2num(($percent / 100) * $totalSale, 'MT');
	}

	$targetRate = 0.0;
	if ($percent !== null) {
		$targetRate = price2num($percent, 'MU');
	} elseif ($totalSale > 0 && $targetMargin > 0) {
		$targetRate = price2num(($targetMargin / $totalSale) * 100, 'MU');
	}

	return array($targetMargin, $targetRate);
	}

	/**
		* Get dictionary entry matching the total power / Récupère l'entrée de dictionnaire correspondant à la puissance totale
		*
		* @param float $totalPower Total PV power / Puissance totale PV
		* @return array<string, float|null>          Data extracted from dictionary / Données issues du dictionnaire
		*/
	private function getTargetEntry($totalPower)
	{
	static $entries = null;

	if ($entries === null) {
		$entries = array();
		$sql = "SELECT * FROM ".$this->db->prefix()."c_ppv_marge_cible_wc WHERE active = 1 ORDER BY position ASC, rowid ASC";
		$resql = $this->db->query($sql);
		if ($resql) {
		while ($obj = $this->db->fetch_object($resql)) {
			$entries[] = $obj;
		}
		}
	}

	if (empty($entries)) {
		return array('amount' => null, 'percent' => null);
	}

	$selected = null;
	foreach ($entries as $entry) {
		$min = $this->extractNumeric($entry, array('rang_min', 'range_min', 'power_min', 'puissance_min', 'min_power', 'min'));
		$max = $this->extractNumeric($entry, array('rang_max', 'range_max', 'power_max', 'puissance_max', 'max_power', 'max'));

		if ($min !== null && $totalPower < $min) {
		continue;
		}
		if ($max !== null && $max > 0 && $totalPower > $max) {
		$selected = $entry;
		continue;
		}

		$selected = $entry;
		break;
	}

	if ($selected === null) {
		$selected = end($entries);
		if ($selected === false) {
		return array('amount' => null, 'percent' => null);
		}
	}

	$amount = $this->extractNumeric($selected, array('amount', 'value', 'valeur', 'montant', 'marge', 'marge_wc', 'price', 'price_wc', 'target'));
	$percent = $this->extractNumeric($selected, array('percent', 'tx', 'taux', 'pourcentage', 'ratio', 'rate'));

	return array('amount' => $amount, 'percent' => $percent);
	}

	/**
		* Extract numeric value from object / Extrait une valeur numérique d'un objet
		*
		* @param stdClass $entry      Database row / Ligne de base de données
		* @param array    $candidates  Candidate column names / Noms de colonnes candidats
		* @return float|null                   Numeric value or null / Valeur numérique ou nulle
		*/
	private function extractNumeric($entry, array $candidates)
	{
	foreach ($candidates as $candidate) {
		if (isset($entry->$candidate) && $entry->$candidate !== '') {
		return (float) $entry->$candidate;
		}
	}

	return null;
	}
}
