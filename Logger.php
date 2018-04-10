<?php namespace SMBR;

class Logger {
    private $fp, $logfile;

    public function __construct($filename) {
        $this->logfile = $filename;
        $this->fp = fopen($this->logfile, 'w+') or exit("Can't open logfile $this->logfile !\n");
    }

    public function write($text) {
        if ($this->fp)
            fwrite($this->fp, $text);
    }

    public function close() {
        fclose($this->fp);
    }
}
