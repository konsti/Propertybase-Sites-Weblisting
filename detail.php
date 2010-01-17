<?php

/* Import the settings*/
require('config/settings.php');

if ($_GET) {
	/* Build query */	
	$data = array('id'=>$_GET['id'],
		'DetailFields'=>DETAIL_FIELDS,
		'token'=>SF_SECURITY_TOKEN);

	$query = SALESFORCE_URL . DETAIL_PAGE . '?' . http_build_query($data);
	
	/* Connecting Salesforce - Load Data */
	$xml = simplexml_load_file($query);
	
	$images = simplexml_load_file(SALESFORCE_URL . IMAGE_PAGE . '?' . http_build_query(array('token'=>SF_SECURITY_TOKEN,'id'=>(string)$xml->Id)));
	
	/* Matching images with listing */
	$xml = __matchImagesForDetailPage($xml,$images);

	/* Display the property details */
	__displayProperty($xml);
	
} else {
	die("No detail values...");
}

/**
 * HTML-Output function to display the property details
 *
 * @param object $xml XML Object with the property
 *
 */
function __displayProperty($xml) {
	include('templates/header.php');
	?>
	
	<div id="pb_listingDetail">
		<div id="head">
			<div class="left">
				<h1>Property Details</h1>
			</div>
			<div class="right">
				<h2>more pictures</h2>
			</div>
		</div>
		<div id="body">
			<div class="left">
				<img src="<?php print($xml->InventoryImage__c[0]->MidResUrl__c); ?>" width="400" alt="<?php print($xml->InventoryImage__c[0]->ExternalId__c); ?>" />
				<p><?php print($xml->ItemName__c); ?></p>
				<input class="formButton" name="back" id="back" value="Back to results" onclick="history.go(-1);" type="submit">
				<input class="formButton" name="new_search" id="new_search" value="New search" onclick="document.location='search.php'" type="submit">
			</div>
			<div class="right">
				<div class="thumb_images">
					<?php $i = 0; ?>
					<?php foreach ($xml->InventoryImage__c as $image): ?>
					<?php if ($i % 2 == 0): ?>
					     <div class="cut img_left"><a class="lightwindow" href="<?php print($image->HighResUrl__c); ?>"><img src="<?php print($image->ThumbnailUrl__c); ?>" width="113" alt="<?php print($image->ExternalId__c); ?>" /></a></div>
					<?php else: ?>
						<div class="cut img_right"><a class="lightwindow" href="<?php print($image->HighResUrl__c); ?>"><img src="<?php print($image->ThumbnailUrl__c); ?>" width="113" alt="<?php print($image->ExternalId__c); ?>" /></a></div>
					<?php endif; ?>
					<?php $i++; ?>
					<?php endforeach; ?>
					<?php if ($i % 2 != 0): ?>
						<div class="cut img_right"></div>
					<?php endif; ?>
				</div>
				<div class="details">
					<h3>Details</h3>
					<ul>
						<li>Price: <?php echo number_format((float)$xml->PurchaseListPrice__c,2,'.',',') ?> <?php print($xml->CurrencyIsoCode); ?></li>
						<li>Bedrooms: <?php print($xml->UnitBedrooms__c); ?></li>
						<li>Type: <?php print($xml->UnitType__c); ?></li>
						<li>Reference Number: <?php print($xml->Name); ?></li>
						<li>Total Area (sqf): <?php print($xml->TotalAreaSqf__c); ?></li>
					</ul>
				</div>
				<div class="details">
					<h3>Description</h3>
					<p><?php print($xml->ItemDescription__c); ?></p>
				</div>
			</div>
		</div>
	</div>
	
	<?php
	include('templates/footer.php');
}

/**
 * Method used to match images with detail page data
 *
 * @param object $xml XMl Object with detail property information
 * @param object $images XML Object with images
 *
 * @return object Propertybase data with images
 */
function __matchImagesForDetailPage($xml, $images) {
	$i = 0;
	/* Loop through all Images */
	foreach ($images->InventoryImage__c as $image) {
		if ((string)$xml->Id == (string)$image->InventoryItemId__c) {
				$xml->addChild('InventoryImage__c');
				$xml->InventoryImage__c[$i]->addChild('ExternalId__c', $image->ExternalId__c);
				$xml->InventoryImage__c[$i]->addChild('ThumbnailUrl__c', $image->ThumbnailUrl__c);
				$xml->InventoryImage__c[$i]->addChild('MidResUrl__c', $image->MidResUrl__c);
				$xml->InventoryImage__c[$i]->addChild('HighResUrl__c', $image->HighResUrl__c);
				$i++;
		}
	}
	return $xml;
}

?>