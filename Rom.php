<?php namespace SMBR;

/**
 * ROM class
 *
 * read and check a rom, and write a new one.
 *
 * inspired by alttp randomizer
 *
 *
 * TODO: Allow changing text?
 */


class Rom {
    const HASH = "811b027eaf99c2def7b933c5208636de";
    const SIZE = 40976;

    private $tmpfile;
    private $log;
    protected $rom;
    protected $level;

    /**
     * constructor
	 *
	 * @param string $source_location location of source ROM to edit
	 *
	 * @return void
	 */
	public function __construct(string $source_location = null) {
        $this->tmpfile = tempnam(sys_get_temp_dir(), __CLASS__);
        if ($source_location !== null) {
            copy($source_location, $this->tmpfile);
        }

        $this->rom = fopen($this->tmpfile, "r+");
    }

    public function setLogger($l) {
        $this->log = $l;
    }

    /**
     * Get MD5 hash/checksum of current file
     *
     * @return string
     */
    public function getMD5() : string  {
        return hash_file('md5', $this->tmpfile);
    }

    /**
     * Check the MD5 hash of current file
     *
     * @return bool
     */
    public function checkMD5() : bool {
        return $this->getMD5() === static::HASH;
    }

    /**
	 * Read data from the ROM file into an array
	 *
	 * @param int $offset location in the ROM to begin reading
	 * @param int $length data to read
	 * // TODO: this should probably always be an array, or a new Bytes object
	 * @return array
	 */
	public function read(int $offset, int $length = 1) {
		fseek($this->rom, $offset);
		$unpacked = unpack('C*', fread($this->rom, $length));
		return count($unpacked) == 1 ? $unpacked[1] : array_values($unpacked);
	}

    /**
	 * Save the changes to this output file
	 *
	 * @param string $output_location location on the filesystem to write the new ROM.
	 *
	 * @return bool
	 */
	public function save(string $output_location) : bool {
		return copy($this->tmpfile, $output_location);
	}

	/**
	 * Write packed data at the given offset
	 *
	 * @param int $offset location in the ROM to begin writing
	 * @param string $data data to write to the ROM
	 * @param bool $log write this write to the log
	 *
	 * @return $this
	 */
	public function write(int $offset, string $data, bool $log = true) : self {
		//if ($log) {
		//	$this->write_log[] = [$offset => array_values(unpack('C*', $data))];
		//}
        //printf("ROM WRITE AT 0x%04x - 0x%02x\n", $offset, $data[0]);
        //print_r($data . "\n");
		fseek($this->rom, $offset);
		fwrite($this->rom, $data);

        $d = array_values(unpack('C*', $data));
        $m = sprintf("rom::write addr: %04x ", $offset);
        $this->log->write($m);
        foreach($d as $value) {
            $this->log->write(sprintf("%02x ", $value));
        }
        $this->log->write("\n");
		return $this;
	}

    public function writeArray(int $offset, array $adata, bool $log = true) : self {
        // log the write...
        $d = array_values($adata);
        $m = sprintf("rom::write addr: %04x ", $offset);
        $this->log->write($m);
        foreach($d as $value) {
            $this->log->write(sprintf("%02x ", $value));
        }
        $this->log->write("\n");

        $data = pack('C*', $adata);
        fseek($this->rom, $offset);
        fwrite($this->rom, $data);
        return $this;
    }

    public function writeGame(Game $game) {
        $offset = 0x1ccc;
        $index = 0;

        // Write all maps
        foreach ($game->worlds as $world) {
            foreach ($world->levels as $level) {
                $this->write($offset + $index, pack('C*', $level->map));
                $index++;
            }
        }

        // Write the table with how many levels are in each world
        $offset = 0x1cc4;
        $data = 0;
        $index = 0;
        foreach ($game->worlds as $world) {
            $this->write($offset + $index, pack('C*', $data));
            $data += count($world->levels);
            $index++;
        }
    }

    public function setMarioOuterColor(int $color) : self {
        $this->write(0x005E8, pack('C*', $color));
        return $this;
    }

    public function setMarioSkinColor(int $color) : self {
        $this->write(0x005E9, pack('C*', $color));
        return $this;
    }

    public function setMarioInnerColor(int $color) : self {
        $this->write(0x005EA, pack('C*', $color));
        return $this;
    }

    public function setLuigiOuterColor(int $color) : self {
        $this->write(0x005EC, pack('C*', $color));
        return $this;
    }

    public function setLuigiSkinColor(int $color) : self {
        $this->write(0x005ED, pack('C*', $color));
        return $this;
    }

    public function setLuigiInnerColor(int $color) : self {
        $this->write(0x005EE, pack('C*', $color));
        return $this;
    }

    public function setFireOuterColor(int $color) : self {
        $this->write(0x005F0, pack('C*', $color));
        return $this;
    }

    public function setFireSkinColor(int $color) : self {
        $this->write(0x005F1, pack('C*', $color));
        return $this;
    }

    public function setFireInnerColor(int $color) : self {
        $this->write(0x005F2, pack('C*', $color));
        return $this;
    }

    public function readLevels() : self {
        $level[1][1] = $this->read(0x2502F, 0x25099-0x2502F);
        $level[1][2] = $this->read(0x2562D, 0x256D1-0x2562D);
        $this->writeArray(0x2502F, $level[1][2]);

        return $this;
    }
    /**
     * destructor
     *
     * @return void
     */
    public function __destruct() {
		if ($this->rom) {
			fclose($this->rom);
		}
		unlink($this->tmpfile);
	}
}

