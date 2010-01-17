<?php

/* Import the settings*/
require('config/settings.php');

if ($_GET) {
	/* Build query */
	#$whereClause = __buildWhereClause($_GET);
	
	$data = array('ListingFields'=>LISTING_FIELDS,
				'sort'=>LISTING_ORDER_BY,
				'token'=>SF_SECURITY_TOKEN,
				'maximum_bedrooms'=>MAXIMUM_BEDROOMS);
	
	$data += $_GET;

	$query = SALESFORCE_URL . LISTING_PAGE . '?' . http_build_query($data);
	
	/* Connecting Salesforce - Load Data */
	$xml = simplexml_load_file($query);
	
	$images = simplexml_load_file(SALESFORCE_URL . IMAGE_PAGE . '?' . http_build_query($data) );
	
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
	<script language="JavaScript">
	function setVisibility(id, visibility) {
		document.getElementById(id).style.display = visibility;
	}
	</script>
	<input type="button" name="type" value="Show XML DEBUG" onclick="setVisibility('debug', 'block');";>
	<input type="button" name="type" value="Hide XML DEBUG" onclick="setVisibility('debug', 'none');";>
	<div id="debug" style="display: none; border:2px solid red">
		<pre>DEBUG:<?php print_r($xml); ?></pre>
	</div>
	<h1>Property Listing</h1>
	<?php $number_of_pages = ceil((float)$xml->Pagination->NumberOfInventories / (float)$xml->Pagination->InventoriesPerPage) ?>
	<?php unset($_GET['page']); ?>
	<?php if ($number_of_pages > 1): ?>
		<div id="page_nav">
			<ul>
		<?php for ($i = 1; $i <= $number_of_pages; $i++):?>
				<li><a href="<?php print($_SERVER['PHP_SELF'] . '?' . http_build_query($_GET) . '&page=' . $i); ?>"><?php print($i); ?></a></li>
		<?php endfor; ?>
			<ul>
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
						<h2><?php print($inventoryItem->UnitBedrooms__c); ?> bedroom <?php print(strtolower($inventoryItem->UnitType__c)); ?></h2>
						<h3><?php echo number_format((float)$inventoryItem->PurchaseListPrice__c,2,'.',',') ?> <?php print($inventoryItem->CurrencyIsoCode); ?></h3>
						<p><?php
						$desc_lenght = 255;
						if (strlen($inventoryItem->ItemDescription__c) >= $desc_lenght) {
							print(substr($inventoryItem->ItemDescription__c,0,$desc_lenght) . " (...)");
						} else {
							print($inventoryItem->ItemDescription__c);
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
		<?php if ($xml->Error): ?>
			<p>Error message: <span style="color: red;"><?php print($xml->Error); ?></span></p>
		<?php else: ?>
			<p>Sorry, your search resulted in no results.</p>
		<?php endif; ?>
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

?>