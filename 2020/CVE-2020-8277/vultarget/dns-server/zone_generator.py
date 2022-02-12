import sys

n = int(sys.argv[1])

print(
'''
$ORIGIN pudim.com.
$TTL 3600           ; [1h]
@ IN SOA sid.pudim.com. root.pudim.com. (
        2021072001  ; Serial
        3600        ; Refresh [1h]
        600         ; Retry   [10m]
        86400       ; Expire  [1d]
        600         ; Negative Cache TTL [1h]
        )
;
@       IN  NS      sid.pudim.com.

sid     IN  A       192.168.1.0
'''
)

for i in range(n):
  addr = 'www' if i == 0 else ' '*3
  print(
    f'{addr}     IN  A       192.168.{i//255 + 1}.{i%255 + 1}'
  )