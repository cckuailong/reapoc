<?php
return array(
    'version' => 2,
    'endpoints' => array(
        '*/*' => array(
            'endpoint' => '{service}.{region}.wasabisys.com'
        ),
        'cn-north-1/*' => array(
            'endpoint' => '{service}.{region}.wasabisys.com.cn',
            'signatureVersion' => 'v4'
        ),
        'us-gov-west-1/iam' => array(
            'endpoint' => 'iam.us-gov.wasabisys.com'
        ),
        'us-gov-west-1/sts' => array(
            'endpoint' => 'sts.us-gov-west-1.wasabisys.com'
        ),
        'us-gov-west-1/s3' => array(
            'endpoint' => 's3-{region}.wasabisys.com'
        ),
        '*/cloudfront' => array(
            'endpoint' => 'cloudfront.wasabisys.com',
            'credentialScope' => array(
                'region' => 'us-east-1'
            )
        ),
        '*/iam' => array(
            'endpoint' => 'iam.wasabisys.com',
            'credentialScope' => array(
                'region' => 'us-east-1'
            )
        ),
        '*/importexport' => array(
            'endpoint' => 'importexport.wasabisys.com',
            'credentialScope' => array(
                'region' => 'us-east-1'
            )
        ),
        '*/route53' => array(
            'endpoint' => 'route53.wasabisys.com',
            'credentialScope' => array(
                'region' => 'us-east-1'
            )
        ),
        '*/sts' => array(
            'endpoint' => 'sts.wasabisys.com',
            'credentialScope' => array(
                'region' => 'us-east-1'
            )
        ),
        'us-east-1/sdb' => array(
            'endpoint' => 'sdb.wasabisys.com'
        ),
        'us-east-2/s3' => array(
            'endpoint' => 's3.{region}.wasabisys.com'
        ),
        'us-west-1/s3' => array(
            'endpoint' => 's3.{region}.wasabisys.com'
        ),
        'eu-central-1/s3' => array(
            'endpoint' => 's3.{region}.wasabisys.com'
        )
    )
);
