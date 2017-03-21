<?php
/***
 *    8888888888       888  .d888 d8b 888 888                                 888
 *    888              888 d88P"  Y8P 888 888                                 888
 *    888              888 888        888 888                                 888
 *    8888888 888  888 888 888888 888 888 888 88888b.d88b.   .d88b.  88888b.  888888      .d8888b .d88b.  88888b.d88b.
 *    888     888  888 888 888    888 888 888 888 "888 "88b d8P  Y8b 888 "88b 888        d88P"   d88""88b 888 "888 "88b
 *    888     888  888 888 888    888 888 888 888  888  888 88888888 888  888 888        888     888  888 888  888  888
 *    888     Y88b 888 888 888    888 888 888 888  888  888 Y8b.     888  888 Y88b.  d8b Y88b.   Y88..88P 888  888  888
 *    888      "Y88888 888 888    888 888 888 888  888  888  "Y8888  888  888  "Y888 Y8P  "Y8888P "Y88P"  888  888  888
 */

// Download NuSOAP at https://sourceforge.net/projects/nusoap/
require_once('nusoap.php');

$client = new nusoap_client('https://app.atcostfulfillment.com/api/order?wsdl', true);
$err    = $client->getError();

if ($err)
{
	echo '<h2>Constructor error</h2><pre>' . $err . '</pre>';
	die();
}

$person = [
	'merchant_id'        => '59991',  // ENTER YOUR MERCHANT ID HERE
	'api_key'            => 'apiKey', // ENTER YOUR API KEY HERE
	'campaign_id'        => '',
	'order_id'           => date('U'),
	'ship_group_code'    => 'Track,Cheapest,Fastest',
	'ship_fname'         => 'test',
	'ship_lname'         => 'test',
	'ship_address1'      => '1600 Pennsylvania Ave NW',
	'ship_address2'      => '',
	'ship_city'          => 'Washington',
	'ship_state'         => 'DC',
	'ship_zip'           => '20500',
	'ship_country'       => 'US', # Please use 2 letter ISO 3166-1
	'ship_phone'         => '8005551212', # Phone & Email are required for international packages
	'ship_email'         => 'sales@fulfillment.com',
	'Comments'           => 'A Comment Goes Here',
	'product_1_sku'      => 'UXCV-Rebill',
	'product_1_quantity' => 1,
	'product_1_price'    => 49.42,
];

$result = $client->call('execute', ['person' => $person]);


if ($client->fault)
{
	echo '<h2>Fault</h2><pre>';
	print_r($result);
	echo '</pre>';
}
else
{
	$err = $client->getError();

	if ($err)
	{
		echo '<h2>Error</h2><pre>' . $err . '</pre>';
	}
	else
	{
		echo '<h2>Request</h2>';
		echo '<pre>' . htmlspecialchars($client->request, ENT_QUOTES) . '</pre>';
		echo '<h2>Response</h2>';
		echo '<pre>' . htmlspecialchars($client->response, ENT_QUOTES) . '</pre>';
		echo '<h2>Result</h2><pre>';
		print_r($result);
		echo '</pre>';
	}
}
