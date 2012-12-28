<?php
require_once('Logger.php');

class A {

    private $logger;

    public function __construct() {
        /*
         * A simple Logger with default Formatter
         */
        $config = array(
            'logLevel' => Logger::TRACE,
            'handlers' => array(
                new LogFileHandler(), // defaults to /tmp/output.log
                new LogConsoleHandler() 
            )
        );
        $this->logger = Logger::getLogger(__CLASS__, $config);
    }
    
    public function test() {

        $this->logger->trace("A Trace Statement (Level INFO)");
        $this->logger->debug("A Debug Statement (Level INFO)");
        $this->logger->info("An Info level msg (Level INFO)");
        $this->logger->warn("A Warning Occured (Level INFO)");
        $this->logger->error("An Error Occured (Level INFO)");
        $this->logger->fatal("A Fatal Error occured (Level INFO)");
        
        $this->logger->setLevel(Logger::TRACE);
        $this->logger->trace("A Trace Statement (Level TRACE)");
        $this->logger->debug("A Debug Statement (Level TRACE)");
        $this->logger->info("An Info level msg (Level TRACE)", $this);
        $this->logger->warn("A Warning Occured (Level TRACE)");
        $this->logger->error("An Error Occured (Level TRACE)");
        $this->logger->fatal("A Fatal Error occured (Level TRACE)");

        $this->logger->setLevel(Logger::FATAL);
        $this->logger->trace("A Trace Statement (Level FATAL)");
        $this->logger->debug("A Debug Statement (Level FATAL)");
        $this->logger->info("An Info level msg (Level FATAL)");
        $this->logger->warn("A Warning Occured (Level FATAL)", $this);
        $this->logger->error("An Error Occured (Level FATAL)");
        $this->logger->fatal("A Fatal Error occured (Level FATAL)");

        if($this->logger->isDebugEnabled()) {
            $this->logger->debug("First Debug Statement (shouldn't be printed)");
        }
        $this->logger->setLevel(Logger::DEBUG);
        if($this->logger->isDebugEnabled()) {
            $this->logger->debug("Second Debug Statement (should be printed)");
        }
        if($this->logger->isTraceEnabled()) {
            $this->logger->trace("First Trace Statement (shouldn't be printed)");
        }
        $this->logger->setLevel(Logger::TRACE);
        if($this->logger->isTraceEnabled()) {
            $this->logger->trace("Second Trace Statement (should be printed)");
        }
    }
    
}

$a = new A();
$a->test();

?>
