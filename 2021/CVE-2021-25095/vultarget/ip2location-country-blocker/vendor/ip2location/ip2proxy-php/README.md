# IP2Proxy PHP Module
[![Latest Stable Version](https://img.shields.io/packagist/v/ip2location/ip2proxy-php.svg)](https://packagist.org/packages/ip2location/ip2proxy-php)
[![Total Downloads](https://img.shields.io/packagist/dt/ip2location/ip2proxy-php.svg?style=flat-square)](https://packagist.org/packages/ip2location/ip2proxy-php)

This module allows user to query an IP address if it was being used as open proxy, web proxy, VPN anonymizer and TOR exits. It lookup the proxy IP address from **IP2Proxy BIN Data** file. This data file can be downloaded at

* Free IP2Proxy BIN Data: https://lite.ip2location.com
* Commercial IP2Proxy BIN Data: https://www.ip2location.com/proxy-database

## Methods
Below are the methods supported in this class.



### BIN Database Class

| Function Name | Description |
|---|---|
|Constructor|Expect 2 input parameters:<ol><li>Full path of IP2Proxy BIN data file.</li><li>File Open Mode<ul><li>	SHARED_MEMORY</li><li>MEMORY_CACHE</li><li>FILE_IO</li></ul></li></ol>For SHARED_MEMORY and MEMORY_CACHE, it will require your server to have sufficient memory to hold the BIN data, otherwise it will raise the errors during the object initialization.|
|**string** getDatabaseVersion()|Return the database's compilation date as a string of the form 'YYYY-MM-DD',|
|**string** getPackageVersion()|Return the database's type, 1 to 10 respectively for PX1 to PX10. Please visit https://www.ip2location.com/databases/ip2proxy for details.|
|**string** getModuleVersion()|Return the version of module.|
|**array** lookup($ip)|Return the IP information in array. Below is the information returned:<ul><li>ipNumber</li><li>ipVersion</li><li>ipAddress</li><li>countryCode</li><li>countryName</li><li>regionName</li><li>cityName</li><li>isp</li><li>domain</li><li>usageType</li><li>asn</li><li>as</li><li>lastSeen</li><li>threat</li><li>proxyType</li><li>isProxy</li></ul>You can visit [IP2Location](https://www.ip2location.com/database/px10-ip-proxytype-country-region-city-isp-domain-usagetype-asn-lastseen-threat-residential) website for the description of each field. Note: although the above names are not exactly matched with the names given in this link, but they are self-described.|



### Web Service Class

| Method Name | Description                                                  |
| ----------- | ------------------------------------------------------------ |
| Constructor | Expect 3 input parameters:<ol><li>IP2Proxy API Key.</li><li>Package (PX1 - PX10)</li><li>Use HTTPS or HTTP</li></ol> |
| lookup      | Return the proxy information in array.<ul><li>countryCode</li><li>countryName</li><li>regionName</li><li>cityName</li><li>isp</li><li>domain</li><li>usageType</li><li>asn</li><li>as</li><li>lastSeen</li><li>threat</li><li>proxyType</li><li>isProxy</li></ul> |
| getCredit   | Return remaining credit of the web service account.          |



## Usage

### BIN Database

Open and read IP2Proxy binary database. There are 3 modes:

1. **\IP2Proxy\Database::FILE_IO** - File I/O reading. Slower look, but low resource consuming.
2. **\IP2Proxy\Database::MEMORY_CACHE** - Caches database into memory for faster lookup. Required high memory.
3. **\IP2Proxy\Database::SHARED_MEMORY** - Stores whole IP2Proxy database into system memory. Lookup is possible across all applications within the system.  Extremely resources consuming. Do not use this mode if your system do not have enough memory.

```php
require 'vendor/autoload.php';

$db = new \IP2Proxy\Database('vendor/ip2location/ip2proxy-php/data/PX10.SAMPLE.BIN', \IP2PROXY\Database::FILE_IO);
```

To start lookup result from database, use the following codes:

```php
$records = $db->lookup('1.0.0.8', \IP2PROXY\Database::ALL);
```

Results are returned in array.

```php
echo '<p><strong>IP Address: </strong>' . $records['ipAddress'] . '</p>';
echo '<p><strong>IP Number: </strong>' . $records['ipNumber'] . '</p>';
echo '<p><strong>IP Version: </strong>' . $records['ipVersion'] . '</p>';
echo '<p><strong>Country Code: </strong>' . $records['countryCode'] . '</p>';
echo '<p><strong>Country: </strong>' . $records['countryName'] . '</p>';
echo '<p><strong>State: </strong>' . $records['regionName'] . '</p>';
echo '<p><strong>City: </strong>' . $records['cityName'] . '</p>';

/*
  Type of proxy: VPN, TOR, DCH, PUB, WEB, RES (RES available in PX10 only)
*/
echo '<p><strong>Proxy Type: </strong>' . $records['proxyType'] . '</p>';

/*
  Returns -1 on errors
  Returns 0 is not proxy
  Return 1 if proxy
  Return 2 if it's data center IP
*/
echo '<p><strong>Is Proxy: </strong>' . $records['isProxy'] . '</p>';
echo '<p><strong>ISP: </strong>' . $records['isp'] . '</p>';
```



### Web Service API

To lookup by Web service, you will need to sign up for [IP2Proxy Web Service](https://www.ip2location.com/web-service/ip2proxy) to get a API key.

Start your lookup by following codes:

```php
require 'vendor/autoload.php';

// Lookup by Web API
$ws = new \IP2Proxy\WebService('YOUR_API_KEY',  'PX10', false);

$results = $ws->lookup('1.0.241.135');

if ($results !== false) {
    echo '<p><strong>Country Code: </strong>' . $results['countryCode'] . '</p>';
    echo '<p><strong>Country Name: </strong>' . $results['countryName'] . '</p>';
    echo '<p><strong>Region: </strong>' . $results['regionName'] . '</p>';
    echo '<p><strong>City: </strong>' . $results['cityName'] . '</p>';
    echo '<p><strong>ISP: </strong>' . $results['isp'] . '</p>';
    echo '<p><strong>Domain: </strong>' . $results['domain'] . '</p>';
    echo '<p><strong>Usage Type: </strong>' . $results['usageType'] . '</p>';
    echo '<p><strong>ASN: </strong>' . $results['asn'] . '</p>';
    echo '<p><strong>AS: </strong>' . $results['as'] . '</p>';
    echo '<p><strong>Last Seen: </strong>' . $results['lastSeen'] . ' Day(s)</p>';
    echo '<p><strong>Proxy Type: </strong>' . $results['proxyType'] . '</p>';
    echo '<p><strong>Threat: </strong>' . $results['threat'] . '</p>';
    echo '<p><strong>Is Proxy: </strong>' . $results['isProxy'] . '</p>';
}
```



# Reference

### Proxy Type

| Type | Description                                                  | Anonymity |
| ---- | ------------------------------------------------------------ | --------- |
| VPN  | Anonymizing VPN services. These services offer users a publicly accessible VPN for the purpose of hiding their IP address. | High      |
| TOR  | Tor Exit Nodes. The Tor Project is an open network used by those who wish to maintain anonymity. | High      |
| DCH  | Hosting Provider, Data Center or Content Delivery  Network. Since hosting providers and data centers can serve to provide  anonymity, the Anonymous IP database flags IP addresses associated with  them. | Low       |
| PUB  | Public Proxies. These are services which make connection requests on a user's behalf. Proxy server software can be configured by the administrator to listen on some specified port. These differ from  VPNs in that the proxies usually have limited functions compare to VPNs. | High      |
| WEB  | Web Proxies. These are web services which make web  requests on a user's behalf. These differ from VPNs or Public Proxies in that they are simple web-based proxies rather than operating at the IP  address and other ports level. | High      |
| SES  | Search Engine Robots. These are services which perform  crawling or scraping to a website, such as, the search engine spider or  bots engine. | Low       |
| RES  | Residential proxies. These services offer users proxy  connections through residential ISP with or without consents of peers to share their idle resources. Only available with PX10 | Medium    |



### Usage Type

- (COM) Commercial
- (ORG) Organization
- (GOV) Government
- (MIL) Military
- (EDU) University/College/School
- (LIB) Library
- (CDN) Content Delivery Network
- (ISP) Fixed Line ISP
- (MOB) Mobile ISP
- (DCH) Data Center/Web Hosting/Transit
- (SES) Search Engine Spider
- (RSV) Reserved