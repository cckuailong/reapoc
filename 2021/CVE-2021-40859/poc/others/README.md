# CVE-2021-40859
Auerswald COMpact 8.0B Backdoors exploit


# About
Backdoors were discovered in Auerswald COMpact 5500R 7.8A and 8.0B devices, that allow attackers with access to the web based management application full administrative access to the device.

# Product Details
Product: COMpact 3000 ISDN, COMpact 3000 analog, COMpact 3000 VoIP, COMpact 4000, COMpact 5000(R), COMpact 5200(R), COMpact 5500R, COMmander 6000(R)(RX), COMpact 5010 VoIP, COMpact 5020 VoIP, COMmander Business(19"), COMmander Basic.2(19").

Affected Versions: &lt;= 8.0B (COMpact 4000, COMpact 5000(R), COMpact 5200(R), COMpact 5500R, COMmander 6000(R)(RX)), &lt;= 4.0S (COMpact 3000 ISDN, COMpact 3000 analog, COMpact 3000 VoIP).

Fixed Versions: 8.2B, 4.0T.

Vulnerability Type: Backdoor.

Security Risk: high.


## Installation


```bash
pip3 install requests lxml BeautifulSoup pandas 
```

## Usage

```python
python3 CVE-2021-40859.py ip:port
```

## Note

This exploit code checks if your Auerswald COMpact server is accessible using the backdoors.

## Developer
[D0rkerDevil](https://twitter.com/D0rkerDevil)
[wabaf3t](https://twitter.com/wabafet1)

## References
https://packetstormsecurity.com/files/165168/Auerswald-COMpact-8.0B-Backdoors.html

https://www.redteam-pentesting.de/advisories/rt-sa-2021-007

## License
[MIT](https://choosealicense.com/licenses/mit/)

