[![PHP Version](http://img.shields.io/badge/php-5.5+-ff69b4.svg)](https://packagist.org/packages/soluble/japha)
[![Build Status](https://travis-ci.org/belgattitude/soluble-japha.svg?branch=master)](https://travis-ci.org/belgattitude/soluble-japha)
[![Code Coverage](https://scrutinizer-ci.com/g/belgattitude/soluble-japha/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/belgattitude/soluble-japha/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/belgattitude/soluble-japha/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/belgattitude/soluble-japha/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/soluble/japha/v/stable.svg)](https://packagist.org/packages/soluble/japha)
[![Total Downloads](https://poser.pugx.org/soluble/japha/downloads.png)](https://packagist.org/packages/soluble/japha)
[![License](https://poser.pugx.org/soluble/japha/license.png)](https://packagist.org/packages/soluble/japha)
[![HHVM Status](https://php-eye.com/badge/soluble/japha/hhvm.svg)](https://php-eye.com/package/soluble/japha)


In short **soluble-japha** allows to write Java code in PHP and interact with the JVM and its huge ecosystem. 
As a meaningless example, see the code below:

```php
<?php

use Soluble\Japha\Bridge\Adapter as BridgeAdapter;

$ba = new BridgeAdapter([
    'driver' => 'Pjb62', 
    'servlet_address' => 'localhost:8089/servlet.phpjavabridge'
]);

// An utf8 string
$string = $ba->java('java.lang.String', "Hello world!");
$hash   = $ba->java('java.util.HashMap', ['key1' => $string, 'key2' => 'hello']);
echo $hash->get('key1'); // prints "Hello world"
echo $hash->get('key2')->length(); // prints 4

// Some maths
$bigint = $ba->java("java.math.BigInteger", 1);
echo $bigint->intValue() + 10; // prints 11

```

Practically it works by communicating with a [PHP/Java bridge](https://github.com/belgattitude/php-java-bridge) server which exposes the JVM 
through a specific network protocol. This way all libraries registered on the the JVM can be used from PHP, almost just like you could write
in Java. The Java code is still executed on the JVM but send results back to PHP. 
      
## Use cases 

It differs from the idea of api, microservices... where communication requires a contract 
between the client and server in order to exchange information. With soluble-japha, 
you can transparently use Java objects, methods... which will be proxied to the JVM.  
  
With that in mind you can use it to broaden the PHP possibilities to the Java ecosystem and its bunch 
of compelling libraries *(i.e. Jasper Reports, Apache POI, iText, PDFBox, Machine Learning...)* or simply 
establish the bridge whenever a pure-PHP alternative does not exists, reveals itself nonviable 
or just for the fun.

## Features

- Write Java from PHP (with a little extra php-style ;)  
- Compatible with [PHP/Java bridge](https://github.com/belgattitude/php-java-bridge) server implementation.
- Efficient, no startup effort, native communication with the JVM ([JSR-223](https://en.wikipedia.org/wiki/Scripting_for_the_Java_Platform) spec).
- Java objects, methods calls... are proxied to the server through a fast XML-based network protocol. 
- *For support with older `Java.inc` client, see the [legacy compatibility layer](https://github.com/belgattitude/soluble-japha-pjb62-compat).*

## Requirements

- PHP 5.6, 7.0+, 7.1+ or HHVM >= 3.9 (for PHP5.5 use the 0.13.* releases).
- Installed [JRE or JDK 7/8+](./doc/server/install_java.md).
- A PHP-Java bridge server [installed](./doc/quick_install.md).

## Documentation

 - [Manual](http://docs.soluble.io/soluble-japha/manual/) in progress. 
 - [API documentation](http://docs.soluble.io/soluble-japha/api/) available.

## Installation

1. Installation in your PHP project **(client)**
 
   ```console
   $ composer require soluble/japha
   ```

2. PHP-Java-bridge **(server)**
     
   To get **a quick glimpse** use the [pjbserver-tools standalone server](https://github.com/belgattitude/pjbserver-tools).
   
   ```console
   $ git clone https://github.com/belgattitude/pjbserver-tools.git
   $ cd pjbserver-tools
   $ composer update   
   $ ./bin/pjbserver-tools pjbserver:start -vvv ./config/pjbserver.config.php.dist
   ```

   > The server will start on default port ***8089***. If you like to change it, create a local copy of `./config/pjbserver.config.php.dist`
   > and refer it in the above command.
   >
   > Use the commands `pjbserver:stop`, `pjbserver:restart`, `pjbserver:status` to control or query the server status.
   >
   > Read the [doc](https://github.com/belgattitude/pjbserver-tools) about the standalone server to learn how to add java libs. 
      
   For **production or distribution** please have a look to the [server installation guide](./doc/quick_install.md) to get an overview of possible strategies.

          
## Examples

Here's some quick examples to get a feeling, don't forget to check out the [official documentation site](http://docs.soluble.io/soluble-japha/manual/).

### 1. Connection example

Configure your bridge adapter with the correct driver (currently only Pjb62 is supported) and the PHP-Java-bridge server address.

```php
<?php

use Soluble\Japha\Bridge\Adapter as BridgeAdapter;

$ba = new BridgeAdapter([
    'driver' => 'Pjb62', 
    'servlet_address' => 'localhost:8089/servlet.phpjavabridge'
]);
```
 

### 2. Hello word

```php
<?php

// $ba = new BridgeAdapter(...); 

$myJavaString = $ba->java('java.lang.String', "Hello");

// concat method on java string object
// see http://docs.oracle.com/javase/7/docs/api/java/lang/String.html

$myJavaString->concat(" world");  

echo $myJavaString;  

// -> Outputs Hello world

```

### 3. Get your JVM info

```php
<?php

// $ba = new BridgeAdapter(...); 

$system = $ba->javaClass('java.lang.System');
echo  $system->getProperties()->get('java.vm_name');

```


### 4. JDBC example

Ensure your servlet installation can locate the JDBC driver and try :

```php
<?php

use Soluble\Japha\Bridge\Exception;

// $ba = new BridgeAdapter(...); 

$driverClass = 'com.mysql.jdbc.Driver';
$dsn = "jdbc:mysql://localhost/my_database?user=login&password=pwd";

try {

    $driverManager = $ba->javaClass('java.sql.DriverManager');

    $class = $ba->javaClass('java.lang.Class');
    $class->forName($driverClass);
    
    $conn = $driverManager->getConnection($dsn);

} catch (Exception\ClassNotFoundException $e) {
    // Probably the jdbc driver is not registered
    // on the JVM side. Check that the mysql-connector.jar
    // is installed
    echo $e->getMessage();
    echo $e->getStackTrace();
} catch (Exception\JavaException $e) {
    echo $e->getMessage();
    echo $e->getStackTrace();
}
try {
    $stmt = $conn->createStatement();
    $rs = $stmt->executeQuery('select * from product');
    while ($rs->next()) {
        $title = $rs->getString("title");
        echo $title;            
    }        
    if (!$ba->isNull($rs)) {
        $rs->close();
    }
    if (!$ba->isNull($stmt)) {
        $stmt->close();
    }
    $conn->close();
} catch (Exception\JavaException $e) {
    echo $e->getMessage();
    // Because it's a JavaException
    // you can use the java stack trace
    echo $e->getStackTrace();
} catch (\Exception $e) {
    echo $e->getMessage();
}

```

### SSL client socket, readers and writers

```php
<?php

// $ba = new BridgeAdapter(...); 

$serverPort = 443;
$host = 'www.google.com';

$socketFactory = $ba->javaClass('javax.net.ssl.SSLSocketFactory')->getDefault();
$socket = $socketFactory->createSocket($host, $serverPort);

$socket->startHandshake();
$bufferedWriter = $ba->java('java.io.BufferedWriter',
            $ba->java('java.io.OutputStreamWriter',
                    $socket->getOutputStream()
            )
        );

$bufferedReader = $ba->java('java.io.BufferedReader',
            $ba->java('java.io.InputStreamReader',
                $socket->getInputStream()
            )
        );

$bufferedWriter->write("GET / HTTP/1.0");
$bufferedWriter->newLine();
$bufferedWriter->newLine(); // end of HTTP request
$bufferedWriter->flush();

$lines = [];
do {
    $line = $bufferedReader->readLine();
    $lines[] = (string) $line;
} while(!$ba->isNull($line));

$content = implode("\n", $lines);
echo $content;

$bufferedWriter->close();
$bufferedReader->close();
$socket->close();

```


For more examples and recipes, have a look at the [official documentation site](http://docs.soluble.io/soluble-japha/manual/). 


### Original PHPJavaBridge (Java.inc) differences

> **soluble-japha** is the client part and was completly refactored from the original [Java.inc client](http://php-java-bridge.sourceforge.net/pjb/).   


- New API (not backward compatible)

    All global functions have been removed (`java_*`) in favour of a more object oriented approach. 
    By doing so, the new API breaks compatibility with existing code (see the 
    [legacy compatibility guide](./doc/pjb62_compatibility.md) if you have code written against 
    the `Java.inc` original client), but offers the possibility to rely on different driver implementations 
    without breaking your code.

- PHP version and ecosystem

    - PHP7, HHVM ready (PHP 5.5+ supported).
    - Installable with composer
    - Compliant with latests standards: PSR-2, PSR-3, PSR-4

- Enhancements    
    
    - Namespaces introduced everywhere.
    - Removed global namespace pollution (java_* functions)
    - Removed global variables, functions and unscoped statics.
    - No more get_last_exception... (All exceptions are thrown with reference to context)
    - Autoloading performance (no more one big class, psr4 autoloader is used, less memory)
    - Removed long time deprecated features in Java.inc
    - By design, no more allow_url_fopen needed.
    
- Fixes
    
    - All notices, warnings have been removed
    - Some minor bugs found thanks to the unit tests suite

- Testing
   
    - All code is tested (phpunit, travis), analyzed (scrunitizer)
 

## Compatibility layer

Take a look to [legacy compatibility guide](./doc/pjb62_compatibility.md) for more information.

## Future ideas

- Original code improvements
  - Achieve at least 80% of unit testing for legacy code.
  - Refactor as much as possible and remove dead code.

- Supporting more drivers or techs
  - [Zend Java bridge](http://files.zend.com/help/Zend-Platform/about.htm) driver compatibility.
  - [GRPC](http://www.grpc.io/) 
  - Support the [MethodHandles](http://docs.oracle.com/javase/7/docs/api/java/lang/invoke/MethodHandles.html) and [InvokeDynamic](http://docs.oracle.com/javase/7/docs/api/java/lang/invoke/package-summary.html) APIs described in [JSR-292](https://jcp.org/en/jsr/detail?id=292).

- Improve proxy
  - see [ProxyManager](https://github.com/Ocramius/ProxyManager)

- Explore new possibilities 
  - Create a JSR-223 php extension in Go, like this [experiment](https://github.com/do-aki/gophp_sample)


### Credits

* This code is principally developed and maintained by [Sébastien Vanvelthem](https://github.com/belgattitude).
* Special thanks to [all of these awesome contributors](https://github.com/belgattitude/soluble-japha/network/members)
* This project wouldn't be possible without the [PHPJavaBridge project leaders and contributors](http://php-java-bridge.sourceforge.net/pjb/contact.php#code_contrib). 
  
## Coding standards and interop

* [PSR 4 Autoloader](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md)
* [PSR 3 Logger interface](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md)
* [PSR 2 Coding Style Guide](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md)
* [PSR 1 Coding Standards](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md)

