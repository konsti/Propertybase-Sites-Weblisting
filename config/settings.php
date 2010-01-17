<?php
/* ====== General ====== */

/* Salesforce ORG Sites URL */
define("SALESFORCE_URL", "http://pbdev3-developer-edition.na6.force.com/"); // Please enter your Salesforce Sites URL here
define("SF_SECURITY_TOKEN", "3ca89d425b8e23f94aef8c0a1f5c0bad83847008"); // Please enter your security token from LeanParameters here

/* ====== Listing ====== */

/* Salesforce VisualForce Pages for Weblisting */
define("LISTING_PAGE", "XMLListingResult");
define("DETAIL_PAGE","XMLDetailResult");
define("LOCATION_PAGE","XMLLocationList");
define("IMAGE_PAGE","XMLImageResult");

/* Listing fields separated by comma */
define("LISTING_FIELDS",'Id'.
	',IsForSale__c'.
	',IsForLease__c'.
	',ItemDescription__c'.
	',PurchaseListPrice__c'.
	',CurrencyIsoCode'.
	',UnitBedrooms__c'.
	',UnitType__c'.
	'');

/* Sort Order for the Listing, SOQL Format */
define("LISTING_ORDER_BY", "PurchaseListPrice__c ASC");

/* Detail page fields separated by comma */
define("DETAIL_FIELDS",'Id'.
	',IsForSale__c'.
	',IsForLease__c'.
	',ItemCompletionDate__c'.
	',ItemCompletionStatus__c'.
	',ItemDescription__c'.
	',ItemName__c'.
	',Name'.
	',PurchaseListPrice__c'.
	',CurrencyIsoCode'.
	',UnitFloorNumber__c'.
	',UnitBedrooms__c'.
	',UnitType__c'.
	',TotalAreaSqm__c'.
	'');

/* Maximum number of bedrooms in salesforce picklist */
define("MAXIMUM_BEDROOMS", 5);

?>