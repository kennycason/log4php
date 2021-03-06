log4php
=======

PHP Logger
```php
$logger = Logger::getLogger(__CLASS__, $options);
```
where `$options` is an associative array of options, below are some possible options, 
if $options is null it inherits default parameters, 
Logging Level `INFO`, and writes to `/tmp/output.log`
```php

$options = array(
   'log_level' => 'INFO',
   'handler' => new LogFileHandler()
);

// you can
$options = array(
   'log_level' => 'TRACE',
   'handlers' => array(
   	new LogFileHandler('/tmp/app/output.log'), 	// write to specific log file
   	new LogConsoleHandler(), 			// echo out log message
   	new LogApacheErrorLogHandler(),			// write to apache error log
   	new LogSQLHandler($db)				// write to DB, default version uses PDO
   	)
);
```
Log Levels
<table>
<tr><td>FATAL</td><td>A serious, and unrecoverable error occured</td></tr>
<tr><td>ERROR</td><td>A serious error, i.e. a DB call failed, but perhaps is recoverable</td></tr>
<tr><td>WARN</td><td>Notify of potential bugs, that while don't break the site, could result in undefined behavior</td></tr>
<tr><td>INFO</td><td>General level, default logging level</td></tr>
<tr><td>DEBUG</td><td>Detailed code primarily used to help debug general problems</td></tr>
<tr><td>TRACE</td><td>Very fine tuned in depth messages</td></tr>
</table>

Currently Log Formatters must be set at the handler level.
<table>
<tr><td>%L</td><td>Log Level</td></tr>
<tr><td>%T</td><td>Time</td></tr>
<tr><td>%R</td><td>Resource</td></tr>
<tr><td>%M</td><td>Message</td></tr>
<tr><td>%O</td><td>Object</td></tr>
</table>
```php
$formatter = new LogFormatter("%L [%T] %R %M %O");
$formatter->setDateFormat('Y-m-d H:i:s');

$handler = new LogFileHandler('/tmp/app/output.log');
$handler->setFormatter($formatter); 
```
By Default they inherit `LogDefaultFormatter`
 

Sample Usage
```php
// A simple Logger with default Formatter and a Default LogFileHandler (prints out to /tmp/output.log
         
$logger = Logger::getLogger(__CLASS__, array("log_level" => Logger::TRACE));

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
