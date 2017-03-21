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

/*
 * Sample order data
 */

$clientDetails  = [
	'merchant_id' => '50001',
	'api_key'     => 'ABC123',
];
$orderDetails   = [
	'order_id'        => date('U'),
	'campaign_id'     => '',
	'ship_group_code' => 'Track,Cheapest,Fastest',
	'ship_fname'      => 'test',
	'ship_lname'      => 'test',
	'ship_address1'   => '1600 Pennsylvania Ave NW',
	'ship_address2'   => '',
	'ship_city'       => 'Washington',
	'ship_state'      => 'DC',
	'ship_zip'        => '20500',
	'ship_country'    => 'US', # Please use 2 letter ISO 3166-1
	'ship_phone'      => '8005551212', # Phone & Email are required for international packages
	'ship_email'      => 'sales@fulfillment.com',
	'Comments'        => '',
];
$productDetails = [
	[
		'sku'      => 'sku1',
		'quantity' => 1,
		'price'    => 3.99,
	],
	[
		'sku'      => 'sku2',
		'quantity' => 1,
		'price'    => 3.99,
	],
];

/*
 * Build our XML request
 */

$productXml = '';

$i = 1;
foreach ($productDetails as $productDetail)
{
	$noun   = ($i === 1) ? 'product' : 'upsell';
	$format = '<%1$s_%2$d_sku>%3$s</%1$s_%2$d_sku><%1$s_%2$d_quantity>%4$d</%1$s_%2$d_quantity><%1$s_%2$d_price>%5$s</%1$s_%2$d_price>';

	$productXml .= sprintf($format, $noun, $i, $productDetail['sku'], $productDetail['quantity'], $productDetail['price']);
	$i++;
}
unset($noun, $i, $format, $productDetail);

$xmlRequest = "<?xml version=\"1.0\" encoding=\"UTF-8\"?><SOAP-ENV:Envelope>
    <SOAP-ENV:Body>
        <tns:execute>
            <person>
                <merchant_id>{$clientDetails['merchant_id']}</merchant_id>
                <api_key>{$clientDetails['api_key']}</api_key>
                <campaign_id>{$orderDetails['campaign_id']}</campaign_id>
                <order_id>{$orderDetails['order_id']}</order_id>
                <ship_group_code>{$orderDetails['ship_group_code']}</ship_group_code>
                <ship_fname>{$orderDetails['ship_fname']}</ship_fname>
                <ship_lname>{$orderDetails['ship_lname']}</ship_lname>
                <ship_address1>{$orderDetails['ship_address1']}</ship_address1>
                <ship_address2>{$orderDetails['ship_address2']}</ship_address2>
                <ship_city>{$orderDetails['ship_city']}</ship_city>
                <ship_state>{$orderDetails['ship_state']}</ship_state>
                <ship_zip>{$orderDetails['ship_zip']}</ship_zip>
                <ship_country>{$orderDetails['ship_country']}</ship_country>
                <ship_phone>{$orderDetails['ship_phone']}</ship_phone>
                <ship_email>{$orderDetails['ship_email']}</ship_email>
                <Comments></Comments>
                {$productXml}
            </person>
        </tns:execute>
    </SOAP-ENV:Body>
</SOAP-ENV:Envelope>";

/*
 * Submit Order to Fulfillment.com
 */
try
{
	$curl = curl_init();

	curl_setopt_array($curl, [
		CURLOPT_URL            => 'https://app.atcostfulfillment.com/api/order',
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING       => '',
		CURLOPT_MAXREDIRS      => 10,
		CURLOPT_TIMEOUT        => 30,
		CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST  => 'POST',
		CURLOPT_POSTFIELDS     => $xmlRequest,
		CURLOPT_HTTPHEADER     => [
			'cache-control: no-cache',
			'content-type: text/xml',
			'soapaction: urn:order#execute',
			'user-agent: crmName',
		],
	]);

	$fdcResponse  = curl_exec($curl);
	$fdcCurlError = curl_error($curl);

	curl_close($curl);

	if ($fdcCurlError)
	{
		throw new Exception("cURL Error #:$fdcCurlError");
	}

	/*
	 * You have a response from Fulfillment.com
	 * Parse it and decide how to move forward
	 */

	$doc = new DOMDocument('1.0', 'utf-8');
	$doc->loadXML($fdcResponse);

	$fdcResult = $doc->getElementsByTagName("status");
	$fdcResult = $fdcResult->item(0)->nodeValue;

	$fdcResultDesc = $doc->getElementsByTagName("error_desc");
	$fdcResultDesc = $fdcResultDesc->item(0)->nodeValue;

	if ($fdcResult == 'Success' || $fdcResultDesc == 'Duplicate order!')
	{
		// Fulfillment.com has received your order
		// If you want to record our order ID you can however you can ask us about your order using your order ID
		$fdcOrderId = $doc->getElementsByTagName("acf_id");
		$fdcOrderId = (int) $fdcOrderId->item(0)->nodeValue;
	}
	else
	{
		$errorMsg = 'FDC responded with the following error "%s" / "%s"';
		throw new Exception(sprintf($errorMsg, $fdcResult, $fdcResultDesc));
	}

}
catch (Exception $e)
{
	// do something
}
