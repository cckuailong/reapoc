# CVE-2020-11978:  Remote code execution in Apache Airflow's Example DAGs

## Information
**Description:** This vulnerability allows RCE when Airflow's example DAGs are loaded, potentially unauthenticated with CVE-2020-13927  
**CVE Credit**: xuxiang of DtDream security   
**Versions Affected:** <1.10.11  
**Disclosure Link:** https://lists.apache.org/thread.html/r7255cf0be3566f23a768e2a04b40fb09e52fcd1872695428ba9afe91%40%3Cusers.airflow.apache.org%3E  
**NIST CVE Link:** https://nvd.nist.gov/vuln/detail/CVE-2020-11978  

## Proof-of-Concept Exploit
### Description

This exploits the example DAG that is vulnerable to command injection along using the experimental REST API that is public by default, even if web interface has authentication set.

### Usage/Exploitation
`python CVE-2020-11978.py <url> <command>`


### On CVE-2020-13927

If `example_trigger_target_dag` is not loaded and you have knowledge of the particular DAG you want to trigger, then you can use `CVE-2020-11978-min.py` as a template on how to trigger that specific DAG.

## Remediation

### Remove Example DAGs

If you already have examples disabled by setting `load_examples=False` in the config then you are not vulnerable. 

You can update to `>=1.10.11` or remove the vulnerable DAG is `example_trigger_target_dag` for `<1.10.11`

### Deny access to experimental API

If you start a new Airflow instance using `>=1.10.11` , then `deny_all` is already set for `auth_backend` by default in `airflow.cfg`.

```
[api]
auth_backend = airflow.api.auth.backend.deny_all
```

Note that `airflow.api.auth.backend.default` still allows unauthenticated requests to the API even for `>=1.10.11`. So if you have an existing Airflow instance which `auth_backend = airflow.api.auth.backend.default` then even after upgrading to  `>=1.10.11`, then the REST API is still public.

For `>=2.0.0`, the experimental API is disabled but has a more powerful stable API.

