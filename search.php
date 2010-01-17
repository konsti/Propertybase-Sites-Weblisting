<?php

/* Import the settings*/
require('config/settings.php');

/* Connecting Salesforce - Load Data */
$xml = simplexml_load_file(SALESFORCE_URL . LOCATION_PAGE . '?' . http_build_query(array('token'=>SF_SECURITY_TOKEN)));

/* Display the result */
__displaySearchForm($xml);

/**
 * HTML-Output function to display the search form
 *
 * @param object $xml XML Object with properties
 *
 */
function __displaySearchForm($xml) {
	include('templates/header.php');
	?>
			<h1>Property Search</h1>
			<div id="pb_searchBox">
				<form action="listing.php" method="get">
				<table>
					<tr>
						<td class="label">Price Range</td>
						<td class="col_1"><select class="input" name="price_from" size="1">
							<option value="30000">30,000</option>
							<option value="50000">50,000</option>
							<option value="100000">100,000</option>
							<option value="150000">150,000</option>
							<option value="200000">200,000</option>v
							<option value="300000">300,000</option>
							<option value="500000">500,000</option>
							<option value="1000000">1,000,000</option>
						</select></td>
						<td class="col_2"><select class="input" name="price_to" size="1">
							<option value="50000">50,000</option>
							<option value="100000">100,000</option>
							<option value="150000">150,000</option>
							<option value="200000">200,000</option>v
							<option value="300000">300,000</option>
							<option value="500000">500,000</option>
							<option value="1000000" selected="selected">1,000,000+</option>
						</select></td>
					</tr>
					<tr>
						<td class="label">Min. bedrooms</td>
						<td class="col_1"></td>
						<td class="col_2"><select class="input" name="minimum_bedrooms" size="1">
							<option value="any">Any</option>
							<option value="0">0</option>
							<option value="1">1</option>
							<option value="2">2</option>
							<option value="3">3</option>
							<option value="4">4</option>
							<option value="5">5</option>
						</select></td>
					</tr>
					<tr>
						<td class="label">Property Type</td>
						<td class="col_1"></td>
						<td class="col_2"><select class="input" name="type" size="1">
							<option value="any">Any</option>
							<option value="Apartment">Apartment</option>
							<option value="Villa">Villa</option>
							<option value="Office">Office</option>
							<option value="Retail">Retail</option>
							<option value="Mixed">Mixed</option>
							<option value="Parking">Parking</option>
						</select></td>
					</tr>
					<tr>
						<td class="label">Location</td>
						<td class="col_1"></td>
						<td class="col_2"><select class="input" name="location" size="5">
							<option value="any" selected="selected">Any</option>
							<?php foreach ($xml as $location): ?>
							<option value="<?php print($location->Id); ?>"><?php print($location->Name); ?></option>
							<?php endforeach; ?>
						</select></td>
					</tr>
					<tr>
						<td class="label">Reference Number</td>
						<td class="col_1"></td>
						<td class="col_2"><input class="input" name="reference_number" /></td>
					</tr>
					<tr>
						<td class="label">Property for</td>
						<td class="col_1"></td>
						<td class="col_2"><input class="checkbox" type="checkbox" name="is_for_lease" value="Lease" /><label>Lease</label>
						<input class="checkbox" type="checkbox" checked="checked" name="is_for_sale" value="Sale" /><label>Sale</label></td>
					</tr>
					<tr>
						<td class="label"></td>
						<td class="col_1"></td>
						<td class="col_2"><input class="formButton" name="send_button" id="send_button" value="Search" type="submit"></td>
					</tr>
				</table>
				</form>
			</div>
	<?php
	include('templates/footer.php');
}
?>