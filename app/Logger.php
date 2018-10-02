<?php namespace SMBR;

class Logger
{
    private $fp, $logfile, $actuallySave;

    public function __construct($filename, $actuallySave = true)
    {
        $this->actuallySave = $actuallySave;
        if ($actuallySave) {
            $this->logfile = $filename;
            $this->fp = fopen($this->logfile, 'w+') or exit("Can't open logfile $this->logfile !\n");
        }
    }

    public function write($text)
    {
        if ($this->actuallySave && $this->fp) {
            fwrite($this->fp, $text);
        }
    }

    public function close()
    {
        if ($this->actuallySave && $this->fp) {
            fclose($this->fp);
        }
    }
}
