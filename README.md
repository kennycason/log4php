log4php
=======

PHP Logger

Usage
```php
// A simple Logger with default Formatter and a Default LogFileHandler (prints out to /tmp/output.log
         
$logger = Logger::getLogger(__CLASS__, array("logLevel" => Logger::TRACE));

$logger->trace("A Trace Statement (Level INFO)");
$logger->debug("A Debug Statement (Level INFO)");
$logger->info("An Info level msg (Level INFO)");
$logger->warn("A Warning Occured (Level INFO)");
$logger->error("An Error Occured (Level INFO)");
$logger->fatal("A Fatal Error occured (Level INFO)");
        
$logger->setLevel(Logger::TRACE);
$logger->trace("A Trace Statement (Level TRACE)");
$logger->debug("A Debug Statement (Level TRACE)");
$logger->info("An Info level msg (Level TRACE)", $this);
$logger->warn("A Warning Occured (Level TRACE)");
$logger->error("An Error Occured (Level TRACE)");
$logger->fatal("A Fatal Error occured (Level TRACE)");

$logger->setLevel(Logger::FATAL);
$logger->trace("A Trace Statement (Level FATAL)");
$logger->debug("A Debug Statement (Level FATAL)");
$logger->info("An Info level msg (Level FATAL)");
$logger->warn("A Warning Occured (Level FATAL)", $this);
$logger->error("An Error Occured (Level FATAL)");
$logger->fatal("A Fatal Error occured (Level FATAL)");

if($logger->isDebugEnabled()) {
     $logger->debug("First Debug Statement (shouldn't be printed)");
}
$logger->setLevel(Logger::DEBUG);
if($logger->isDebugEnabled()) {
  	$logger->debug("Second Debug Statement (should be printed)");
}
if($logger->isTraceEnabled()) {
	$logger->trace("First Trace Statement (shouldn't be printed)");
}
$logger->setLevel(Logger::TRACE);
if($logger->isTraceEnabled()) {
 	$logger->trace("Second Trace Statement (should be printed)");
}
```
