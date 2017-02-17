# Fulfillment.com SOAP API

Four years ago FDC launched support for receiving orders, getting tracking information, 
canceling an order, and retrieving returned orders via a SOAP API. Details can be found
here, [Integration Guide](https://www.fulfillment.com/integration-guide/). We have since
developed a REST-ful API and will be working with our clients and integrated partners
over the next few months (early to mid 2017) to finalize standards.

At this point (February 2017) we encourage new integrations to call their account
executive for additional details on our [REST API](https://api.fulfillment.com/docs/api).

## NuSOAP

Our [Integration Guide](https://www.fulfillment.com/integration-guide/) outlines the fastest way to get started.
However NuSOAP is no longer being supported and carries with it unnecessary GETs as we will not be changing our
API setup.

## SOAP Integration Without NuSOAP

Use [newOrderCurl.php](https://github.com/fulfillment/soap-integration/blob/master/newOrderCurl.php)
as an example. I believe most developers will prefer this version.