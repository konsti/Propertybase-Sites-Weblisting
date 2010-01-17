<?php

/* Import the settings*/
require('config/settings.php');

if ($_GET) {
	/* Build query */
	$whereClause = __buildWhereClause($_GET);
	
	$data = array('where'=>$whereClause,
				'ListingFields'=>LISTING_FIELDS,
				'sort'=>LISTING_ORDER_BY,
				'token'=>SF_SECURITY_TOKEN);

	$query = SALESFORCE_URL . LISTING_PAGE . '?' . http_build_query($data);
	
	/* Connecting Salesforce - Load Data */
	$xml = simplexml_load_file($query);
	
	$images = simplexml_load_file(SALESFORCE_URL . IMAGE_PAGE . '?' . http_build_query(array('token'=>SF_SECURITY_TOKEN,'inventoryids'=>__getInventoryIdsAsString($xml))));
	
	/* Matching images with listing */
	$xml = __matchImages($xml,$images);
	
	/* Display the result */
	__displayTable($xml);
	
} else {
	die("No search values...");
}

/**
 * HTML-Output function to display the result
 *
 * @param object $xml XML Object with properties
 *
 */
function __displayTable($xml) {
	include('templates/header.php');
	?>
	<h1>Property Listing</h1>
	<?php if ((int)$xml->SelectSize->Size > (int)$xml->SelectSize->Limit): ?>
		<div style="padding: 10px; width: 702px; height: 48px; background-color: #ffe993;">
			<p style="padding:0;margin:0">Your search has too many properties as a result.<br/>
				Only <b><?php print($xml->SelectSize->Limit); ?></b> are displayed. Please define your search details more precisely.<br/>
				<a href="javascript:history.back()">New search...</a></p>
		</div>
	<?php endif; ?>
	<?php if (count($xml->InventoryList->InventoryItem__c) > 0): ?>
	<table id="pb_listing">
		<?php foreach ($xml->InventoryList->InventoryItem__c as $inventoryItem): ?>
		<tr>
			<td>
				<div class="inventory">
					<div class="image">
						<img src="<?php print($inventoryItem->InventoryImage__c->ThumbnailUrl__c); ?>" alt="<?php print($inventoryItem->InventoryImage__c->ExternalId__c); ?>" />
					</div>
					<div class="text">
						<h2><?php print($inventoryItem->pb__UnitBedrooms__c); ?> bedroom <?php print(strtolower($inventoryItem->pb__UnitType__c)); ?></h2>
						<h3><?php print($inventoryItem->pb__PurchaseListPrice__c); ?> <?php print($inventoryItem->CurrencyIsoCode); ?></h3>
						<p><?php
						$desc_lenght = 255;
						if (strlen($inventoryItem->pb__ItemDescription__c) >= $desc_lenght) {
							print(substr($inventoryItem->pb__ItemDescription__c,0,$desc_lenght) . " (...)");
						} else {
							print($inventoryItem->pb__ItemDescription__c);
						}
						?></p>
						<a href="detail.php?id=<?php print($inventoryItem->Id); ?>" target="_top">Details &gt;&gt;</a>
					</div>
				</div>
			</td>
		</tr>
		<?php endforeach; ?>
	</table>
	<?php else: ?>
	<p>Sorry, your search resulted in no results.</p>
	<?php endif; ?>
	
	<?php
	include('templates/footer.php');
}

/**
 * Method used to match images with propertybase data
 *
 * @param object $xml XMl Object with properties (Propertybase data)
 * @param object $images XML Object with images
 *
 * @return object Propertybase data with images
 */
function __matchImages($xml, $images) {
	/* Loop through all InventoryItems */
	foreach ($xml->InventoryList->InventoryItem__c as $inventory) {
		/* Loop through all Images */
		foreach ($images->InventoryImage__c as $image) {
			if ((string)$image->ImageType__c == 'Property default') {
				if ((string)$inventory->Id == (string)$image->InventoryItemId__c) {
					$inventory->addChild('InventoryImage__c');
					$inventory->InventoryImage__c->addChild('ExternalId__c', $image->ExternalId__c);
					$inventory->InventoryImage__c->addChild('ThumbnailUrl__c', $image->ThumbnailUrl__c);
				}
			}
		}
	}
	return $xml;
}

/**
 * Method returns all Inventory Ids as comma separated string
 * 
 * @param object $xml XML Object with properties (Propertybase data)
 *
 * @return string Comma separated string with all inventory ids
 */
function __getInventoryIdsAsString($xml) {
	$inventoryids = '';
	
	/* Loop through all InventoryItems */
	foreach ($xml->InventoryList->InventoryItem__c as $inventory) {
		$inventoryids .= '\''. (string)$inventory->Id . '\',';
	}
	
	return substr($inventoryids,0,-1);	
}

/**
 * Method used to build query used in where condition according to given search criteria
 *
 * @param array $searchCriteria Search criteria
 *
 * @return string Query used in where condition according to given search criteria
 */
function __buildWhereClause($searchCriteria) {

	// Build query used in where condition according to given search criteria
    $whereQuery = 'pb__PurchaseListPrice__c >= ' . $searchCriteria['price_from'];

    // Consider 'price to' if it's set
    if (isset($searchCriteria['price_to'])) {
        $whereQuery .= ' AND pb__PurchaseListPrice__c <= ' . $searchCriteria['price_to'];
    }

	if ('any' != $searchCriteria['minimum_bedrooms']) {
		$bedroomQuery = ' AND pb__UnitBedrooms__c IN (\'';
		for ($bedroom = (int)$searchCriteria['minimum_bedrooms']; $bedroom <= MAXIMUM_BEDROOMS; $bedroom++) {
			$bedroomQuery.= $bedroom . '\', \'';
		}
		$whereQuery .= substr($bedroomQuery, 0, -4) . '\')';
	}

    // Build query for type
    if ('any' != $searchCriteria['type']) {
        $whereQuery .= ' AND pb__UnitType__c = \'' . $searchCriteria['type'] .'\'';
    }

	// Build query for location
    if ('any' != $searchCriteria['location']) {
        $whereQuery .= ' AND pb__Location__c = \'' . $searchCriteria['location'] .'\'';
    }

	// Initialize conditions
    $whereQuery .= ' AND ';

	// Search for Sale & Lease
    if (isset($searchCriteria['is_for_sale']) && isset($searchCriteria['is_for_lease'])) {
		$whereQuery .= '(pb__IsForSale__c = true OR pb__IsForLease__c = true)';
	} else if (empty($searchCriteria['is_for_sale']) && empty($searchCriteria['is_for_lease'])){
		$whereQuery .= 'pb__IsForSale__c = false AND pb__IsForLease__c = false';
	}

	// Search for Sale only
	if (isset($searchCriteria['is_for_sale']) && empty($searchCriteria['is_for_lease'])) {
		$whereQuery .= 'pb__IsForSale__c = true';
	}

	// Search for Lease only
	if (isset($searchCriteria['is_for_lease']) && empty($searchCriteria['is_for_sale'])) {
		$whereQuery .= 'pb__IsForLease__c = true';
	}

	if (isset($searchCriteria['reference_number']) && $searchCriteria['reference_number'] != '') {
		$whereQuery = 'Name LIKE \'%'. $searchCriteria['reference_number'] .'%\'';
	}
	
	// List only available units
	// DEBUG: Add 'AND pb__IsListed__c = true '
	$whereQuery .= ' AND pb__IsAvailable__c = true';
	
	// Return built query to use in where condition
    return $whereQuery;
}

?>