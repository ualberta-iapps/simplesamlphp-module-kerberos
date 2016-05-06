simplesamlphp-module-kerberos
==========================
Kerberos 5 authentication module for SimpleSAMLphp

Prerequisites
-------------
This document assumes that you already have the kerberos client on your server and have properly configured your `/etc/krb5.conf` file. If you are able to obtain a TGT from your KDC using the `kinit` command, you can continue with the installation of this module.

You will need to add support for Kerberos to your PHP instllation. By default, PHP does not ship with this functionality. You can obtain the krb5 module from PECL [here](https://pecl.php.net/package/krb5). This document will not go through installing the module or enabling it in your PHP configuration.

Configuring the module
----------------------

First you need to enable the kerberos module, touch an `enable` file, in the
expirycheck module:

    touch modules/kerberos/enable

Then you need to setup your authsource in the `config/authsources.php` file.

Example:

    // An authentication source which can authenticate against a MIT Kerberos v5 KDC
    'kerb' => array(
        'kerberos:Krb5',

        // The Kerberos realm
        // Do not include a leading @.
        'realm' => 'EXAMPLE.COM',
        
        // Strip realm
        // Remove the realm when passing the principal name into the UID attribute
        'stripRealm' => true,
    ),


You will need to supply a realm. The realm configured here should not include a leading @ symbol, and must be configured in your `/etc/krb5.conf` file.

Using the module
----------------
Once you have configured the module, you can test it on the "Test Configured Authentication Sources" page within SimpleSAMLphp. This module will only return a uid value which is the principal from Kerberos.

Note: When a user is authenticating, if they supply a username@scope.com, the @scope.com will be removed and will be replaced with the configured realm.
