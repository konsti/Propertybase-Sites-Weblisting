<?php
/* ====== General ====== */

/* Salesforce ORG Sites URL */
define("SALESFORCE_URL", "http://evdevo.force.com/"); // Please enter your Salesforce Sites URL here
define("SF_SECURITY_TOKEN", "8533b59bc907ca5817b4af7f76768695fsFsPGSVUZdRE"); // Please enter your security token from LeanParameters here

/* ====== Listing ====== */

/* Salesforce VisualForce Pages for Weblisting */
define("LISTING_PAGE", "XMLListingResult");
define("DETAIL_PAGE","XMLDetailResult");
define("LOCATION_PAGE","XMLLocationList");
define("IMAGE_PAGE","XMLImageResult");

/* Listing fields separated by comma */
define("LISTING_FIELDS",'Id'.
	',pb__IsForSale__c'.
	',pb__IsForLease__c'.
	',pb__ItemDescription__c'.
	',pb__PurchaseListPrice__c'.
	',CurrencyIsoCode'.
	',pb__UnitBedrooms__c'.
	',pb__UnitType__c'.
	'');

/* Sort Order for the Listing, SOQL Format */
define("LISTING_ORDER_BY", "pb__PurchaseListPrice__c ASC");

/* Listing fields separated by comma */
define("EXPORT_FIELDS",'Id'.
	',pb__IsForSale__c'.
	',pb__IsForLease__c'.
	',pb__ItemDescription__c'.
	',pb__PurchaseListPrice__c'.
	',CurrencyIsoCode'.
	',pb__UnitBedrooms__c'.
	',pb__UnitType__c'.
	',Name'.
	'');

/* Detail page fields separated by comma */
define("DETAIL_FIELDS",'Id'.
	',pb__IsForSale__c'.
	',pb__IsForLease__c'.
	',pb__ItemCompletionDate__c'.
	',pb__ItemCompletionStatus__c'.
	',pb__ItemDescription__c'.
	',pb__ItemName__c'.
	',Name'.
	',pb__PurchaseListPrice__c'.
	',CurrencyIsoCode'.
	',pb__UnitFloorNumber__c'.
	',pb__UnitBedrooms__c'.
	',pb__UnitType__c'.
	',pb__TotalAreaSqm__c'.
	'');

/* Maximum number of bedrooms in salesforce picklist */
define("MAXIMUM_BEDROOMS", 5);

/* ====== Web2Account ====== */

/* Salesforce VisualForce Page for Web2Account */
define("LEAD_CAPTURE_PAGE","pb__PHPLeadCapture");

/* Account fields separated by comma */
/* LastName MUST be set */
define("ACCOUNT_FIELDS",'LastName'.
	',FirstName'.
	',PersonEmail'.
	',Phone'.
	',pb__CountryOfResidence__pc'.
	',Description'.
	',PersonLeadSource'.
	'');


?>