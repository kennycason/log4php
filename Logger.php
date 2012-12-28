<?php

/**
 * PHP Logger
 * @author Kenny Cason
 * @site www.ken-soft.com
 */
class Logger {

    /**
     * static Logger for convenient quick use of logger
     */
    public static function getStaticLogger($resource, $config=array()) {
        $logger = new Logger();
        $logger->resource = $resource;
        Logger::configure($logger, $config);
        return $logger;
    }

    /**
     * singleton instances of a Logger indexed by a unique resource
     */
    private static $loggerInstances = array();

    /*
     * log levels, int value
     */

    const FATAL = 6;
    const ERROR = 5;
    const WARN = 4;
    const INFO = 3;
    const DEBUG = 2;
    const TRACE = 1;

    /*
     * log levels, string values
     */

    private static $LOG_LEVELS = array(
        Logger::TRACE => "TRACE",
        Logger::DEBUG => "DEBUG",
        Logger::INFO => "INFO",
        Logger::WARN => "WARN",
        Logger::ERROR => "ERROR",
        Logger::FATAL => "FATAL");
    
    public $handlers = array();
    public $logLevel = Logger::INFO;   // default logging level is info

    /*
     * formatting - move to LogFormatter Class later
     */
    public $resource = ""; // a unique identifier for this logger (i.e. a class name, or script name)
    
    public $config = array();

    /**
     * using singleton pattern return a logger instance
     * @param string $resource
     * @param array configuration
     * @return Logger instance
     */

    public static function getLogger($resource, $config=array()) {
        if (!isset(self::$loggerInstances[$resource])) {
            self::$loggerInstances[$resource] = new Logger();
        }
        Logger::$loggerInstances[$resource]->resource = $resource;
        Logger::configure(Logger::$loggerInstances[$resource], $config);
        return Logger::$loggerInstances[$resource];
    }

    private static function configure(Logger $logger, $config=null) {
        if($config != null) {
            if (isset($config['log_level'])) {
                $logger->logLevel = $config['log_level'];
            }
            if (isset($config['handlers']) && is_array($config['handlers'])) {
                $logger->clearHandlers();
                $logger->handlers = $config['handlers'];
            }
            if (isset($config['handler']) && !is_array($config['handler'])) {
                $logger->clearHandlers();
                $logger->addHandler($config['handler']);
            }
            if (empty($logger->handlers)) {
                $logger->addHandler(new LogFileHandler()); // create a default LogFileHandler
            }
        } else {
            Logger::configure($logger, Logger::getDefaultConfig());
        }
    }
    
    public static function getDefaultConfig() {
        $handler = new LogFileHandler();
        $config = array(
            'log_level' => Logger::INFO,
            'handler' => $handler
        );
        return $config;
    }
    
    public function setLevel($level=Logger::ERROR) {
        if ($level >= Logger::TRACE && $level <= Logger::FATAL) {
            $this->logLevel = $level;
        } else {
            $this->logLevel = Logger::INFO;
        }
    }

    /**
     * return the current log level
     * @return string
     */
    public function getLevel() {
        return Logger::$LOG_LEVELS[$this->logLevel];
    }

    /**
     * set the log mode. (i.e.log to LOG_TYPE_FILE / LOG_TYPE_SQL / LOG_TYPE_ERROR_LOG
     * @param int
     */
    public function setLogMode($logMode) {
        $this->logMode = $logMode;
    }

    /**
     * get lhe log mode
     * @return int
     */
    public function getLogMode() {
        return $this->logMode;
    }

    /**
     * get all handlers
     */
    public function getHandlers() {
        return $this->handlers;
    }

    /**
     * add a handler
     * @param ILogHandler $handler
     */
    public function addHandler(ILogHandler $handler) {
        $this->handlers[] = $handler;
    }

    /**
     * clear all handlers
     */
    public function clearHandlers() {
        $this->handlers = array();
    }

    /**
     * determine if the logger level is at least DEBUG level
     * @return bool
     */
    public function isDebugEnabled() {
        if ($this->getLevel() <= Logger::DEBUG) {
            return true;
        }
        return false;
    }

    /**
     * determine if the logger level is at least TRACE level
     * @return bool
     */
    public function isTraceEnabled() {
        if ($this->getLevel() <= Logger::TRACE) {
            return true;
        }
        return false;
    }

    /**
     * log at trace level
     * @param string $msg
     * @param object $obj (optional)
     */
    public function trace($msg, $obj=null) {
        if ($this->logLevel <= Logger::TRACE) {
            $this->log(Logger::TRACE, $msg, $obj);
        }
    }

    /**
     * log at debug level
     * @param string $msg
     * @param object $obj (optional)
     */
    public function debug($msg, $obj=null) {
        if ($this->logLevel <= Logger::DEBUG) {
            $this->log(Logger::DEBUG, $msg, $obj);
        }
    }

    /**
     * log at info level
     * @param string $msg
     * @param object $obj (optional)
     */
    public function info($msg, $obj=null) {
        if ($this->logLevel <= Logger::INFO) {
            $this->log(Logger::INFO, $msg, $obj);
        }
    }

    /**
     * log at warn level
     * @param string $msg
     * @param object $obj (optional)
     */
    public function warn($msg, $obj=null) {
        if ($this->logLevel <= Logger::WARN) {
            $this->log(Logger::WARN, $msg, $obj);
        }
    }

    /**
     * log at error level
     * @param string $msg
     * @param object $obj (optional)
     */
    public function error($msg, $obj=null) {
        if ($this->logLevel <= Logger::ERROR) {
            $this->log(Logger::ERROR, $msg, $obj);
        }
    }

    /**
     * log at catastrophic level
     * @param string $msg
     * @param object $obj (optional)
     */
    public function fatal($msg, $obj=null) {
        if ($this->logLevel <= Logger::FATAL) {
            $this->log(Logger::FATAL, $msg, $obj);
        }
    }

    /**
     * private helper
     * @param int $logLevel
     * @param string $msg
     */
    private function log($logLevel, $msg, $obj=null) {
        $record = new LogRecord();
        $record->setLevel(Logger::$LOG_LEVELS[$logLevel]);
        $record->setMessage($msg);
        $record->setResource($this->resource);
        $record->setObject($obj);
        foreach ($this->handlers as $handler) {
            $handler->publish($record);
        }
    }

    /**
     * private constructor init using getLogger()
     */
    private function __construct() {
        
    }

    /**
     *  Prevent users to clone the instance
     */
    public function __clone() {
        $this->log('Clone is not allowed.');
    }

}

/**
 * This class encapsulates the Record to be logged
 */
class LogRecord {

    private $resource;
    private $level;
    private $msg;
    private $obj; // optional object to log along with $msg
    private $time;

    public function __construct() {
        $this->time = time();
    }

    public function setResource($resource) {
        $this->resource = $resource;
    }

    public function setLevel($level) {
        $this->level = $level;
    }

    public function setMessage($msg) {
        $this->msg = $msg;
    }

    public function setObject($obj) {
        $this->obj = $obj;
    }

    public function setTime($time) {
        $this->time = $time;
    }

    public function getResource() {
        return $this->resource;
    }

    public function getLevel() {
        return $this->level;
    }

    public function getMessage() {
        return $this->msg;
    }

    public function getObject() {
        return $this->obj;
    }

    public function getTime($format=null) {
        if ($format == null) {
            return $this->time;
        }
        return date($format, $this->time);
    }

}

/**
 * LogHandler interface
 */
interface ILogHandler {

    public function publish(LogRecord $record);

    public function setFormatter(ILogFormatter $formatter);

    public function getFormatter();
}

/**
 * contains common functionality between log handlers
 */
abstract class AbstractLogHandler implements ILogHandler {

    protected $formatter;

    public function __construct() {
        $this->formatter = new LogDefaultFormatter();
    }

    public function setFormatter(ILogFormatter $formatter) {
        $this->formatter = $formatter;
    }

    public function getFormatter() {
        return $this->formatter;
    }

}

/**
 * Outputs to a LogFile
 */
class LogFileHandler extends AbstractLogHandler {

    private $logFileName;

    public function __construct($logFileName="") {
        parent::__construct();
        if ($logFileName != "") {
            $this->logFileName = $logFileName;
        } else {
            $this->logFileName = BASE_DIR . "tmp/logs/output.log";
        }
    }

    public function publish(LogRecord $record) {
        $log_msg = $this->formatter->format($record);

        $logFile = fopen($this->logFileName, 'a');
        if (!$logFile) {
            return array('status' => 'failure', 'msg' => 'Error Opening Log File');
        }
        fwrite($logFile, $log_msg);
        fclose($logFile);
    }

    /**
     * set the log file name to append to
     * @param string
     */
    public function setLogFileName($logFileName) {
        $this->logFileName = $logFileName;
    }

    public function getLogFileName() {
        return $this->logFileName;
    }

}

/**
 * Echo out the Log
 */
class LogConsoleHandler extends AbstractLogHandler {

    public function __construct() {
        parent::__construct();
    }

    public function publish(LogRecord $record) {
        echo $this->formatter->format($record);
    }

}

/**
 * Log via error_log()
 */
class LogApacheErrorLogHandler extends AbstractLogHandler {

    public function __construct() {
        parent::__construct();
    }

    public function publish(LogRecord $record) {
        error_log($this->formatter->format($record));
    }

}

/**
 * Logs to SQL DataBase
 */
class LogSQLHandler extends AbstractLogHandler {

    private $db = null; // mysqli instance

    public function __construct($db) {
        $this->db = $db;
    }

    public function publish(LogRecord $record) {
        $sql = "INSERT INTO `log` (log_level, date, resource, msg) VALUES ('{$record->getLevel()}','{$record->getTime()}', ?, ?);";
        $statement = $this->db->prepare($sql);
        $statement->bind_param("ss", $record->getResource(), $record->getMessage());
        if (!$statement->execute()) {
            return array('status' => 'failure', 'msg' => 'SQL Error');
        }
        $statement->close();
    }

    public function setDB(mysqli $db) {
        $this->db = $mysqli;
    }

    public function getDB() {
        return $this->db;
    }

    public static function init() {
        $sql = "CREATE TABLE `log` (
		`id` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
		`log_level` VARCHAR( 16 ) NOT NULL,
		`date` DATETIME NOT NULL,
		`resource` VARCHAR( 128 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
		`msg` VARCHAR( 1024 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
		) ENGINE = MYISAM;";
        $this->db->query($sql);
    }

}

/**
 * Interface for formatting log messages
 */
interface ILogFormatter {

    public function format(LogRecord $record);
}

/**
 * concrete implementation of a Log Formatter that is formattable
 * %L - Log Level
 * %T - Time of logging
 * %R - Resource of the logger
 * %M - The Message
 * %O - The Object
 */
class LogFormatter implements ILogFormatter {

    private $format = null;
    private $dateFormat = "Y-m-d H:i:s";

    public function __construct($format="%L [%T] %R %M %O") {
        $this->format = $format;
    }

    /**
     * return a formatted string
     * @param LogRecord $record
     */
    public function format(LogRecord $record) {
        $formatted_record = $this->format;
        $formatted_record = str_replace("%L", $record->getLevel(), $formatted_record);
        $formatted_record = str_replace("%T", $record->getTime($this->dateFormat), $formatted_record);
        $formatted_record = str_replace("%R", $record->getResource(), $formatted_record);
        $formatted_record = str_replace("%M", $record->getMessage(), $formatted_record);
        $formatted_record = str_replace("%O", $record->getObject(), $formatted_record);
        return $formatted_record;
    }

    public function setDateFormat($dateFormat) {
        $this->dateFormat = $dateFormat;
    }

}

/**
 * Default log formatter, format is immutable, but should suffice for most cases
 */
class LogDefaultFormatter implements ILogFormatter {

    private $dateFormat = "Y-m-d H:i:s";

    public function __construct() {
        
    }

    public function format(LogRecord $record) {
        return "{$record->getLevel()}\t[{$record->getTime($this->dateFormat)}]\t{$record->getResource()}\t{$record->getMessage()}\t" . print_r($record->getObject(), 1) . "\n";
    }

}