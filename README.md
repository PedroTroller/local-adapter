#Gaufrette Local Adapter
------------------------

Connect Gaufrette filesystem to your local filesystem.

[![Build Status](https://travis-ci.org/Gaufrette/local-adapter.svg?branch=master)](https://travis-ci.org/Gaufrette/local-adapter)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Gaufrette/local-adapter/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Gaufrette/local-adapter/?branch=master)

##Installation

```bash
composer require gaufrette/local-adapter
```

#Configuration and usage

```php
$adapter = new Local('/tmp/folder');
$content = $adapter->readContent('file.txt'); // Return the file content
$adapter->writeContent('file.txt', $content); // Overwrite the file content
// ...
```
