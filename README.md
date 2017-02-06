# CouchDB2-PHP-Client
Library that provides ways to use CouchDB2 for PHP developers.

## Install
Install the library like this `composer require fabsolute/couchdb2-php-client` 

## Quick Start
```
// Create an instance
$config = new Config('http://my.couch.server.com', 5984, 'admin', '123456');
$client = new CouchDB($config);

// Get document by ID
$doc = $client->get_doc('some_doc_id');

TODO

## More...

