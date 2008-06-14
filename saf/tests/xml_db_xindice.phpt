--TEST--
saf.XML.DB.Xindice
--FILE--
<?php

// test setup

// remove this when test is ready to be run
return;

include_once ('../init.php');

// include library

loader_import ('saf.XML.DB.Xindice');

// constructor method

$xindice = new Xindice ('$source', '$handler');

// _method() method

var_dump ($xindice->_method ('$name'));

// createCollection() method

var_dump ($xindice->createCollection ('$parent', '$name'));

// createIndexer() method

var_dump ($xindice->createIndexer ('$collection', '$index', '$pattern'));

// createNewOID() method

var_dump ($xindice->createNewOID ('$collection'));

// dropCollection() method

var_dump ($xindice->dropCollection ('$collection'));

// dropIndexer() method

var_dump ($xindice->dropIndexer ('$collection', '$index'));

// getDocument() method

var_dump ($xindice->getDocument ('$collection', '$id'));

// getDocumentCount() method

var_dump ($xindice->getDocumentCount ('$collection'));

// insertDocument() method

var_dump ($xindice->insertDocument ('$collection', '$id', '$content'));

// listCollections() method

var_dump ($xindice->listCollections ('$collection'));

// listDocuments() method

var_dump ($xindice->listDocuments ('$collection'));

// listIndexers() method

var_dump ($xindice->listIndexers ('$collection'));

// listXMLObjects() method

var_dump ($xindice->listXMLObjects ('$collection'));

// queryCollection() method

var_dump ($xindice->queryCollection ('$collection', '$type', '$query', '$namespaces'));

// queryDocument() method

var_dump ($xindice->queryDocument ('$collection', '$type', '$query', '$namespaces', '$id'));

// removeDocument() method

var_dump ($xindice->removeDocument ('$collection', '$id'));

// setDocument() method

var_dump ($xindice->setDocument ('$collection', '$id', '$content'));

?>
--EXPECT--
