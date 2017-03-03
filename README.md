# CouchDB2-PHP-Client
Library that provides ways to use CouchDB2 for PHP developers.

## Install
Install the library like this `composer require fabs/couchdb2` 

## Quick Start
```
// Create an instance
$config = new Config('http://my.couch.server.com', 5984, 'username', 'password');
$client = new Couch($config);

// Get document by ID
$doc = $client
       ->selectDatabase('test_database')
       ->getDoc('some_doc_id')
       ->execute();

TODO

## More...

