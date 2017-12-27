# Fulfillment.com SOAP API

Four years ago FDC launched support for receiving orders, getting tracking information, 
canceling an order, and retrieving returned orders via a SOAP API. We have since
developed a REST-ful API and will be working with our clients and integrated partners
over the next few months (early to mid 2017) to finalize standards.

At this point (March 2017) we encourage new integrations to call their account
executive for additional details on our [REST API](https://fulfillment.github.io/api/).

Please note, our SOAP API was developed when we were a much younger company known as ACF 
(not Fulfillment.com or FDC) so you will see a few things that seem out-of-place like the
use of "ACF_ID". Consider this our (FDC's) internal ID. Also we support HTTPS!

# New Order

All aspects of your request should be straight forward however the second product is
referenced as "upsell_1_sku", while your third product is referenced as "upsell_2_sku".
If you use the [newOrderCurl.php](https://github.com/fulfillment/soap-integration/blob/master/newOrderCurl.php)
example we've accounted for this oddity and you can simply use an array of products.

**Request**

```xml
POST /api/order?wsdl HTTP/1.1
Host: app.atcostfulfillment.com
Content-Type: text/xml
SOAPAction: urn:order#execute
Cache-Control: no-cache

<?xml version="1.0" encoding="UTF-8"?>
<SOAP-ENV:Envelope>
    <SOAP-ENV:Body>
      <tns:execute>
         <person>
            <merchant_id>50001</merchant_id>
            <api_key>***APIKEY***</api_key>
            <campaign_id>direct</campaign_id>
            <order_id>1490125006108.108</order_id>
            <ship_group_code>Trackable,Cheapest,Fastest</ship_group_code>
            <ship_fname>Mike</ship_fname>
            <ship_lname>Garde</ship_lname>
            <ship_address1>730 King George Blvd</ship_address1>
            <ship_address2></ship_address2>
            <ship_city>Savannah</ship_city>
            <ship_zip>31401</ship_zip>
            <ship_state>GA</ship_state>
            <ship_country>US</ship_country>
            <ship_phone>912-555-1212</ship_phone>
            <ship_email>dev@fulfillment.com</ship_email>
            <Comments>A Comment Goes Here</Comments>
            <product_1_sku>SKU1</product_1_sku>
            <product_1_quantity>1</product_1_quantity>
            <product_1_price>1.00</product_1_price>
         </person>
      </tns:execute>
    </SOAP-ENV:Body>
</SOAP-ENV:Envelope>
```

**Response**

```xml
<?xml version="1.0" encoding="ISO-8859-1"?>
<SOAP-ENV:Envelope SOAP-ENV:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/" xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/" xmlns:tns="urn:order">
    <SOAP-ENV:Body>
        <executeResponse>
            <return xsi:type="tns:SweepstakesGreeting">
                <order_id xsi:type="xsd:string">1490125006108.108</order_id>
                <status xsi:type="xsd:string">Success</status>
                <acf_id xsi:type="xsd:int">10994804</acf_id>
                <error_code xsi:type="xsd:int">0</error_code>
                <error_desc xsi:type="xsd:string"></error_desc>
            </return>
        </executeResponse>
    </SOAP-ENV:Body>
</SOAP-ENV:Envelope>
```

### PHP

A comprehensive example that demonstrates multiple products and error handling can be found here 
[newOrderCurl.php](https://github.com/fulfillment/soap-integration/blob/master/newOrderCurl.php).
I believe most PHP developers will prefer this version.

### NuSOAP (PHP)

Although NuSOAP may look slightly easier it requires your server to place a GET to our server 
prior to the POST of your new order. Regardless of your volume this places an extra step
in communicating your order and additional bandwidth. We have seen this approach cause
problems for clients that batch send their orders during high volume days.

With that said we recommend constructing your final payload and sending it to us, if 
however you are familiar with [NuSOAP](https://sourceforge.net/projects/nusoap/) 
please see [newOrderNuSOAP.php](https://github.com/fulfillment/soap-integration/blob/master/newOrderNuSOAP.php) 

**Known Problems**

* NuSOAP does not handel ISO-8859-1 to UTF-8 encoding well and can obscure special characters 
used in addresses of non-native-english speaking countries
* Additional bandwidth can overwhelm well provisioned servers

# Cancel Order

**Request**

```text
GET /api/order_cancel?merchant_id=50001&amp;api_key=***apiKey***&amp;acf_id=123 HTTP/1.1
Host: app.atcostfulfillment.com
Cache-Control: no-cache
```

**Response**

```xml
<?xml version="1.0" encoding="UTF-8"?>
<request>
    <order>
        <acf_id>123</acf_id>
        <action>Cancel</action>
        <status>Success</status>
    </order>
</request>
```

# Shipment Status

You can use your order id or the "ACF_ID" that is passed back to you.

**Your ID**

```xml
POST /api/track?wsdl HTTP/1.1
Host: app.atcostfulfillment.com
Content-Type: text/xml
SOAPAction: urn:track#execute"
Cache-Control: no-cache

<?xml version="1.0" encoding="UTF-8"?>
<SOAP-ENV:Envelope>
    <SOAP-ENV:Body>
        <tns:execute>
            <person>
                <merchant_id>***merchantId***</merchant_id>
                <api_key>***apiKey</api_key>
                <order_id>123</order_id>
            </person>
        </tns:execute>
    </SOAP-ENV:Body>
</SOAP-ENV:Envelope>
```

**ACF ID**

```xml
POST /api/track?wsdl HTTP/1.1
Host: app.atcostfulfillment.com
Content-Type: text/xml
SOAPAction: urn:track#execute"
Cache-Control: no-cache

<?xml version="1.0" encoding="UTF-8"?>
<SOAP-ENV:Envelope>
    <SOAP-ENV:Body>
        <tns:execute>
            <person>
                <merchant_id>***merchantId***</merchant_id>
                <api_key>***apiKey</api_key>
                <acf_id>123</acf_id>
            </person>
        </tns:execute>
    </SOAP-ENV:Body>
</SOAP-ENV:Envelope>
```

**Response**

```xml
<?xml version="1.0" encoding="ISO-8859-1"?>
<SOAP-ENV:Envelope SOAP-ENV:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/" xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/" xmlns:tns="urn:track">
    <SOAP-ENV:Body>
        <executeResponse>
            <return xsi:type="tns:SweepstakesGreeting">
                <acf_id xsi:type="xsd:int">123</acf_id>
                <order_id xsi:type="xsd:string">456</order_id>
                <tracking_number xsi:type="xsd:string">927400012356789</tracking_number>
                <carrier xsi:type="xsd:string">(FDC) DHL</carrier>
                <ship_method xsi:type="xsd:string">(FDC) DHL Global Parcel Expedited</ship_method>
                <status xsi:type="xsd:string">Shipped</status>
                <date xsi:type="xsd:string"></date>
                <error_code xsi:type="xsd:int">0</error_code>
                <error_desc xsi:type="xsd:string"></error_desc>
                <url xsi:type="xsd:string">http://tracking.fulfillment.com/927400012356789/</url>
            </return>
        </executeResponse>
    </SOAP-ENV:Body>
</SOAP-ENV:Envelope>
```

# Returns

**Request**

```text
GET /api/returns?merchant_id=***merchantId***&amp;api_key=***apiKey***&amp;start_date=2017-01-01&amp;end_date=2017-03-31 HTTP/1.1
Host: app.atcostfulfillment.com
Content-Type: text/xml
Cache-Control: no-cache
```

**Response**

```xml
<?xml version="1.0" encoding="UTF-8"?>
<returns>
    <return acf_id="123" order_id="456">
        <order_date>2016-09-23 09:12:35</order_date>
        <status>RETURNED</status>
        <name>John Doe</name>
        <company />
        <address1>123 Main St</address1>
        <address2></address2>
        <city>Savannah</city>
        <state>GA</state>
        <postal_code>31401</postal_code>
        <country>US</country>
        <email>email@gmail.net</email>
        <phone>9125551212</phone>
        <carrier>USPS Priority Mail 4.95</carrier>
        <tracking_number>927400012356789</tracking_number>
        <postage_due>0</postage_due>
        <rma_present>false</rma_present>
        <rma_number />
        <reason>Customer Returned</reason>
        <condition></condition>
        <notes></notes>
        <is_archive>false</is_archive>
        <return_date>2016-01-02 15:03:58</return_date>
        <items>
            <item id="1000000" >
                <sku>ACME-100</sku>
                <quantity_returned>1</quantity_returned>
                <quantity_returned_to_stock>1</quantity_returned_to_stock>
            </item>
        </items>
    </return>
    <return acf_id="124" order_id="457">
    
    </return>
</returns>
```