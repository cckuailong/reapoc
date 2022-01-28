<?php

use IPTools\Network;
use IPTools\IP;

class NetworkTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $ipv4 = new IP('127.0.0.1');
        $ipv4Netmask = new IP('255.255.255.0');

        $ipv6 = new IP('2001::');
        $ipv6Netmask = new IP('ffff:ffff:ffff:ffff:ffff:ffff:ffff::');

        $ipv4Network = new Network($ipv4, $ipv4Netmask);
        $ipv6Network = new Network($ipv6, $ipv6Netmask);

        $this->assertEquals('127.0.0.0/24', (string)$ipv4Network);
        $this->assertEquals('2001::/112', (string)$ipv6Network);
    }

    public function testProperties()
    {
        $network = Network::parse('127.0.0.1/24');

        $network->ip = new IP('192.0.0.2');

        $this->assertEquals('192.0.0.2', $network->ip);
        $this->assertEquals('192.0.0.0/24', (string)$network);
        $this->assertEquals('0.0.0.255', (string)$network->wildcard);
        $this->assertEquals('192.0.0.0', (string)$network->firstIP);
        $this->assertEquals('192.0.0.255', (string)$network->lastIP);
    }

    /**
     * @dataProvider getTestParseData
     */
    public function testParse($data, $expected)
    {
        $this->assertEquals($expected, (string)Network::parse($data));
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage Invalid IP address format
     */
    public function testParseWrongNetwork()
    {
        Network::parse('10.0.0.0/24 abc');
    }

    /**
     * @dataProvider getPrefixData
     */
    public function testPrefix2Mask($prefix, $version, $mask)
    {
        $this->assertEquals($mask, Network::prefix2netmask($prefix, $version));
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage Wrong IP version
     */
    public function testPrefix2MaskWrongIPVersion()
    {
        Network::prefix2netmask('128', 'ip_version');
    }

    /**
     * @dataProvider getInvalidPrefixData
     * @expectedException Exception
     * @expectedExceptionMessage Invalid prefix length
     */
    public function testPrefix2MaskInvalidPrefix($prefix, $version)
    {
        Network::prefix2netmask($prefix, $version);
    }

    /**
     * @dataProvider getHostsData
     */
    public function testHosts($data, $expected)
    {
        foreach(Network::parse($data)->getHosts as $ip) {
            $result[] = (string)$ip;
        }

        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider getExcludeData
     */
    public function testExclude($data, $exclude, $expected)
    {
        $result = array();

        foreach(Network::parse($data)->exclude($exclude) as $network) {
            $result[] = (string)$network;
        }

        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider getExcludeExceptionData
     * @expectedException Exception
     * @expectedExceptionMessage Exclude subnet not within target network
     */
    public function testExcludeException($data, $exclude)
    {
        Network::parse($data)->exclude($exclude);
    }

    /**
     * @dataProvider getMoveToData
     */
    public function testMoveTo($network, $prefixLength, $expected)
    {
        $result = array();

        foreach (Network::parse($network)->moveTo($prefixLength) as $network) {
            $result[] = (string)$network;
        }

        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider getMoveToExceptionData
     * @expectedException Exception
     * @expectedExceptionMessage Invalid prefix length
     */
    public function testMoveToException($network, $prefixLength)
    {
        Network::parse($network)->moveTo($prefixLength);
    }

     /**
     * @dataProvider getTestIterationData
     */
    public function testNetworkIteration($data, $expected)
    {
        foreach (Network::parse($data) as $key => $ip) {
           $result[] = (string)$ip;
        }

        $this->assertEquals($expected, $result);
    }

     /**
     * @dataProvider getTestCountData
     */
    public function testCount($data, $expected)
    {
        $this->assertEquals($expected, count(Network::parse($data)));
    }

    public function getTestParseData()
    {
        return array(
            array('192.168.0.54/24', '192.168.0.0/24'),
            array('2001::2001:2001/32', '2001::/32'),
            array('127.168.0.1 255.255.255.255', '127.168.0.1/32'),
            array('1234::1234', '1234::1234/128'),
        );
    }

    public function getPrefixData()
    {
        return array(
            array('24', IP::IP_V4, IP::parse('255.255.255.0')),
            array('32', IP::IP_V4, IP::parse('255.255.255.255')),
            array('64', IP::IP_V6, IP::parse('ffff:ffff:ffff:ffff::')),
            array('128', IP::IP_V6, IP::parse('ffff:ffff:ffff:ffff:ffff:ffff:ffff:ffff'))
        );
    }

    public function getInvalidPrefixData()
    {
        return array(
            array('-1', IP::IP_V4),
            array('33', IP::IP_V4),
            array('prefix', IP::IP_V4),
            array('-1', IP::IP_V6),
            array('129', IP::IP_V6),
        );
    }

    public function getHostsData()
    {
        return array(
            array('192.0.2.0/29',
                array(
                    '192.0.2.1',
                    '192.0.2.2',
                    '192.0.2.3',
                    '192.0.2.4',
                    '192.0.2.5',
                    '192.0.2.6',
                )
            ),
        );
    }

    public function getExcludeData()
    {
        return array(
            array('192.0.2.0/28', '192.0.2.1/32',
                array(
                    '192.0.2.0/32',
                    '192.0.2.2/31',
                    '192.0.2.4/30',
                    '192.0.2.8/29',
                )
            ),
            array('192.0.2.2/32', '192.0.2.2/32', array()),
        );
    }

    public function getExcludeExceptionData()
    {
        return array(
            array('192.0.2.0/28', '192.0.3.0/24'),
            array('192.0.2.2/32', '192.0.2.3/32'),
        );
    }

    public function getMoveToData()
    {
        return array(
            array('192.168.0.0/22', '24',
                array(
                    '192.168.0.0/24',
                    '192.168.1.0/24',
                    '192.168.2.0/24',
                    '192.168.3.0/24'
                )
            ),
            array('192.168.2.0/24', '25',
                array(
                    '192.168.2.0/25',
                    '192.168.2.128/25'
                )
            ),
            array('192.168.2.0/30', '32',
                array(
                    '192.168.2.0/32',
                    '192.168.2.1/32',
                    '192.168.2.2/32',
                    '192.168.2.3/32'
                )
            ),
        );
    }

    public function getMoveToExceptionData()
    {
        return array(
            array('192.168.0.0/22', '22'),
            array('192.168.0.0/22', '21'),
            array('192.168.0.0/22', '33'),
            array('192.168.0.0/22', 'prefixLength')
        );
    }

    public function getTestIterationData()
    {
        return array(
            array('192.168.2.0/29',
                array(
                    '192.168.2.0',
                    '192.168.2.1',
                    '192.168.2.2',
                    '192.168.2.3',
                    '192.168.2.4',
                    '192.168.2.5',
                    '192.168.2.6',
                    '192.168.2.7',
                )
            ),
            array('2001:db8::/125',
                array(
                    '2001:db8::',
                    '2001:db8::1',
                    '2001:db8::2',
                    '2001:db8::3',
                    '2001:db8::4',
                    '2001:db8::5',
                    '2001:db8::6',
                    '2001:db8::7',
                )
            ),
        );
    }

    public function getTestCountData()
    {
        return array(
            array('127.0.0.0/31', 2),
            array('2001:db8::/120', 256),
        );
    }
}
