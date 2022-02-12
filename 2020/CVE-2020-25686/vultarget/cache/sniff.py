#!/usr/bin/python

from scapy.all import *


def packet_handler(pkt):
    if pkt.haslayer(DNSQR) and pkt.haslayer(UDP):
        print(f"Source port: {pkt[UDP].sport}, TXID: {pkt[DNS].id}, Query: {pkt[DNSQR].qname}")


print("Sniffing...")
sniff(filter="udp port 53 and ip src 10.10.0.2", prn=packet_handler)
