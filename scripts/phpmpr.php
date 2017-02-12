<?php

/*

http://www.lfs.net/?page=MPR

skins
80 * 32 = 2560 + 80 = 2640 = A50

heads
40 * 32 = 1280 + 2640 = 3920 = 

*/

class PHPMPR {

    public $file;
    public $headers;
    public $players;

    function __construct($file) {
        $this->file = trim($file);
        $this->players = array();
        
        $this->parse();
    }
    
    public function parse($file = null) {
    
        $this->stime = microtime(true);
        
        
        if ($file) {
            $this->file = trim($file);
        }
        
        if (!file_exists($this->file) or !is_file($this->file) or !preg_match('#\.mpr$#i', $this->file))
            throw new Exception("MPR not exists: ".basename($this->file)."");
            
        if (!($f = fopen($this->file, 'rb')))
            throw new Exception("Error opening ".basename($this->file)."");


        if (!($header = fread($f, 80)))
            throw new Exception("Error reading header");


        $this->headers = new PHPMPR_Header($header);


        for ($i = 0; $i < $this->headers->nfinished; $i++) {
            if (!($pheader = fread($f, 80)))
                throw new Exception("Error reading player #".$i);

            $player = new PHPMPR_Player($pheader);
            $this->players[$player->lfsuname] = $player;
        }
        
        
        $this->etime = microtime(true);
        $this->ptime = $this->etime - $this->stime;
        
        
        if ($file)
            return $this;
    }

}


class PHPMPR_Header {

    function __construct($bin) {
        $headers = unpack("A6LFSMPR/C1gamever/C1gamerev/C1mprrev/C1immstart/C2nul/I1rules/I1flags/C1laps/C1skill/C1wind/C1nplayers/A8lfsver/A4trackshort/I1starttime/A32trackfull/C1config/C1rev/C1weather/C1nfinished/I1zero", $bin);
        
        foreach ((array)$headers as $header => $value) {
            $this->$header = $value;
        }
        
        if ($this->LFSMPR != 'LFSMPR')
            throw new Exception("File has not a valid MPR format");
    }
    
    
    public function __set($name, $value) {
        if (is_string($value))
            $this->$name = trim($value);
        else
            $this->$name = $value;
    }
    
    
    public function starttime($format = 'd/m/Y') {
    		return date($format, $this->starttime);
    }
    
    
    public function laps() {
    	
    		if ($this->laps == 0)
    				$l = 'Q';
    		else if ($this->laps < 100)
    				$l = $this->laps;
    		else if ($this->laps <= 190)
    				$l = ($this->laps - 100) * 10 + 100;
    		else
    				$l = ($this->laps - 190) ."h";
    				
    		return $l;
    }
    
    
    public function wind($no='no', $weak='suave', $strong='fuerte') {
    	
    		switch ($this->wind) {
    				case 1: $w = $weak; break;
    				case 2: $w = $strong; break;
    				default: $w = $no;
    		}
    		
    		return $w;
    }
    
    
    public function weather($w0='despejado', $w1='nublado', $w2='atardecer') {
    	
    		switch ($this->weather) {
    				case 1: $w = $w1; break;
    				case 2: $w = $w2; break;
    				default: $w = $w0;
    		}
    		
    		return $w;
    }
    
}


class PHPMPR_Player {

    function __construct($bin) {
        $headers = unpack("A24pname/A8numplate/A4scarname/A24lfsuname/S1laps/S1pflags/C1cflags/C1pits/S1penalsecs/I1racetime/I1bestlaptime/C1zero/C1startpos/C1handicapmass/C1intakeres", $bin);
        
        foreach ((array)$headers as $header => $value) {
            $this->$header = $value;
        }
        
        if (!$this->lfsuname)
            throw new Exception("Player #".$i." malformed");
    }
    
    
    public function __set($name, $value) {
        if (is_string($value))
            $this->$name = trim($value);
        else
            $this->$name = $value;
    }
    
    
    public function racetime($format = 'd/m/Y') {
    		return Utils::lap_time($this->racetime);
    }
    
    
    public function bestlaptime($format = 'd/m/Y') {
    		return Utils::lap_time($this->bestlaptime);
    }
    
}



if (!class_exists('Utils')) {
	class Utils {
	
	  function lfs_colors($name) {
	
	    $str = utf8_decode($name);
	    $str = preg_replace("#(\^([0-8])(.*))#U", "</span><span class=\"color$2\">$3", $str);
	    $str = '<span class="lfs_name"><span>'.$str.'</span></span>';
	
	    $str2 = '';
	    for ($i = 0; $i < strlen($str); $i++) { 
	      if (ord($str{$i}) > 127)
	        $str2 .= '&#'.ord($str{$i}).';';
	      else
	        $str2 .= $str{$i};
	    }
	
	    return $str2;
	  }
	
	  public function hl_time($time) {
	    return sprintf("%02d", (date("H", substr($time, 0, -3)) - 1) ).
	        date(":i:s", substr($time, 0, -3)).
	        ".".substr($time, -3);
	  }
	
	  public function lap_time($time) {
	    $seconds = ($s = substr($time, 0, -3))? $s: 0;
	    return date("i:s", (int)$seconds).
	        ".".substr($time, -3);
	  }
	
	}
}

?>
