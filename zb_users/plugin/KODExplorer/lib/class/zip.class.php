<?php 
/*
 * php zip压缩类
 * auther by Sext (sext@neud.net) (Changed: 2003-03-01)
 * last changed & 代码优化 by：kalcaddle (kalcaddle@qq.com)
 * time:2012/6/3 星期日

	demo:
	------------------
	set_time_limit(0);
	$zipname = 'D:/wwwroot/'.time().'.zip';
	$basic_path = 'D:/wwwroot/'; //zip截取相对路径,否则zip自维持文件全路径
	$filelist = array('D:/wwwroot/api','D:/wwwroot/php.php');// 文件或者文件夹
	$z = new zip($zipname, $basic_path);
	if (!$z -> fp) echo ("{$zipname}不能写入,检查路径或权限");
	else {
		$z -> addFileList($filelist);
		$z -> addFileList('D:/wwwroot/list.php');//可持续添加。
		$z -> zipAll() or die('没有选择的文件或目录.');
		echo "压缩完成,共 $z->file_count 个文件.";
		echo "$zipname $z->sizeFormat(filesize($zipname))";
	}
	------------------
 */
class zip{
	var $file_count = 0 ;
	var $datastr_len = 0;
	var $dirstr_len = 0;
	var $dirstr = '';
	var $filedata = '';			//该变量只被类外部程序访问
	var $gzfilename;			//zip压缩保存路径
	var $fp;					//文件读指针
	var $basic_path;			//zip文件中相对文件夹路径
	var $file_list = array();	//文件列表
	
	// 构造，传入压缩保存文件名，截取目录。
	function __construct($zipname, $basic_path = ''){
		$this -> gzfilename = $zipname;
		$this -> basic_path = $basic_path;

		if ($this -> fp = fopen($this -> gzfilename, "w")) { // 初始化文件,建立文件
			return true;
		} 
		return false;
	} 

	/**
	 * 添加文件【夹】列表到待压缩队列
	 */
	function addFileList($list){
		if (is_array($list)) {
			foreach($list as $key => $val) {
				$this -> file_list[] = $val;
			} 
		} else {
			$this -> file_list[] = $list;
		} 
	}
	
	/*
	* 压缩执行。
	*/
	function zipAll(){
		if ($this -> file_list[0] == "") { // 数组为空
			return false;
		} 
		foreach($this -> file_list as $file) { // 参数数组。
			$this -> listFiles($file);
		} 
		$this -> createFile();
		return true;
	} 

	/**
	 * 返回文件的修改时间格式.只为本类内部函数调用.+
	 */
	function unix2DosTime($unixtime = 0){
		$timearray = ($unixtime == 0) ? getdate() : getdate($unixtime);
		if ($timearray['year'] < 1980) {
			$timearray['year'] = 1980;
			$timearray['mon'] = 1;
			$timearray['mday'] = 1;
			$timearray['hours'] = 0;
			$timearray['minutes'] = 0;
			$timearray['seconds'] = 0;
		} 
		return (($timearray['year'] - 1980) << 25) | ($timearray['mon'] << 21) | ($timearray['mday'] << 16) |
		($timearray['hours'] << 11) | ($timearray['minutes'] << 5) | ($timearray['seconds'] >> 1);
	}

	// 添加文件[夹]到压缩文件
	function listFiles($dir){
		if (is_file($dir)) {
			if (realpath($this -> gzfilename) != realpath($dir)) {
				$this -> addFile(implode('', file($dir)), $dir, $this -> basic_path);
				return 1;
			} 
			return 0;
		} 
		// 文件夹，则递归。
		$handle = opendir($dir);
		while ($file = readdir($handle)) {
			if ($file == "." || $file == "..") continue;
			if (is_dir($dir . '/' . $file)) {
				$this -> listFiles($dir . '/' . $file);
			} else {
				if (realpath($this -> gzfilename) != realpath($dir . '/' . $file)) {
					$this -> addFile(implode('', file($dir . '/' . $file)), $dir . '/' . $file);
					$sub_file_num ++;
				} 
			} 
		} 
		closedir($handle);
		if (!$sub_file_num) $this -> addFile("", $dir . '/');
		return $sub_file_num;
	} 

	/**
	 * 添加一个文件到 zip 压缩包中.文件路径被basic_path截取
	 */
	function addFile($data, $name){
		$name = str_replace('\\', '/', $name);
		$name = str_replace($this -> basic_path, '', $name); 
		//文件路径截取，保证内部相对路径
		
		if (strrchr($name, '/') == '/') return $this -> addDir($name);
		$dtime = dechex($this -> unix2DosTime());
		$hexdtime = '\x' . $dtime[6] . $dtime[7]
		 . '\x' . $dtime[4] . $dtime[5]
		 . '\x' . $dtime[2] . $dtime[3]
		 . '\x' . $dtime[0] . $dtime[1];
		eval('$hexdtime = "' . $hexdtime . '";');

		$unc_len = strlen($data);
		$crc = crc32($data);
		$zdata = gzcompress($data);
		$c_len = strlen($zdata);
		$zdata = substr(substr($zdata, 0, strlen($zdata) - 4), 2);
		// 新添文件内容格式化:
		$datastr = "\x50\x4b\x03\x04";
		$datastr .= "\x14\x00"; // ver needed to extract
		$datastr .= "\x00\x00"; // gen purpose bit flag
		$datastr .= "\x08\x00"; // compression method
		$datastr .= $hexdtime; // last mod time and date
		$datastr .= pack('V', $crc); // crc32
		$datastr .= pack('V', $c_len); // compressed filesize
		$datastr .= pack('V', $unc_len); // uncompressed filesize
		$datastr .= pack('v', strlen($name)); // length of filename
		$datastr .= pack('v', 0); // extra field length
		$datastr .= $name;
		$datastr .= $zdata;
		$datastr .= pack('V', $crc); // crc32
		$datastr .= pack('V', $c_len); // compressed filesize
		$datastr .= pack('V', $unc_len); // uncompressed filesize
		
		fwrite($this -> fp, $datastr); //写入新的文件内容
		$my_datastr_len = strlen($datastr);
		unset($datastr);
		// 新添文件目录信息
		$dirstr = "\x50\x4b\x01\x02";
		$dirstr .= "\x00\x00"; // version made by
		$dirstr .= "\x14\x00"; // version needed to extract
		$dirstr .= "\x00\x00"; // gen purpose bit flag
		$dirstr .= "\x08\x00"; // compression method
		$dirstr .= $hexdtime; // last mod time & date
		$dirstr .= pack('V', $crc); // crc32
		$dirstr .= pack('V', $c_len); // compressed filesize
		$dirstr .= pack('V', $unc_len); // uncompressed filesize
		$dirstr .= pack('v', strlen($name)); // length of filename
		$dirstr .= pack('v', 0); // extra field length
		$dirstr .= pack('v', 0); // file comment length
		$dirstr .= pack('v', 0); // disk number start
		$dirstr .= pack('v', 0); // internal file attributes
		$dirstr .= pack('V', 32); // external file  - 'archive' bit set
		$dirstr .= pack('V', $this -> datastr_len); // relative offset of local header
		$dirstr .= $name;

		$this -> dirstr .= $dirstr; //目录信息		
		$this -> file_count ++;
		$this -> dirstr_len += strlen($dirstr);
		$this -> datastr_len += $my_datastr_len;
	} 

	function addDir($name){
		$name = str_replace("\\", "/", $name);
		$datastr = "\x50\x4b\x03\x04\x0a\x00\x00\x00\x00\x00\x00\x00\x00\x00";

		$datastr .= pack("V", 0) . pack("V", 0) . pack("V", 0) . pack("v", strlen($name));
		$datastr .= pack("v", 0) . $name . pack("V", 0) . pack("V", 0) . pack("V", 0);

		fwrite($this -> fp, $datastr); //写入新的文件内容
		$my_datastr_len = strlen($datastr);
		unset($datastr);

		$dirstr = "\x50\x4b\x01\x02\x00\x00\x0a\x00\x00\x00\x00\x00\x00\x00\x00\x00";
		$dirstr .= pack("V", 0) . pack("V", 0) . pack("V", 0) . pack("v", strlen($name));
		$dirstr .= pack("v", 0) . pack("v", 0) . pack("v", 0) . pack("v", 0);
		$dirstr .= pack("V", 16) . pack("V", $this -> datastr_len) . $name;

		$this -> dirstr .= $dirstr; //目录信息
		$this -> file_count ++;
		$this -> dirstr_len += strlen($dirstr);
		$this -> datastr_len += $my_datastr_len;
	} 

	function createFile(){
		// 压缩包结束信息,包括文件总数,目录信息读取指针位置等信息
		$endstr = "\x50\x4b\x05\x06\x00\x00\x00\x00" .
		pack('v', $this -> file_count) .
		pack('v', $this -> file_count) .
		pack('V', $this -> dirstr_len) .
		pack('V', $this -> datastr_len) . "\x00\x00";

		fwrite($this -> fp, $this -> dirstr . $endstr);
		fclose($this -> fp);
	}

	// 字节转化可读
	function sizeFormat($bytes, $precision = 2){
		if ($bytes == 0) {
			return "0 B";
		} 
		$unit = array(// 优化，不使用幂函数
			'TB' => 1099511627776, // pow( 1024, 4)
			'GB' => 1073741824, // pow( 1024, 3)
			'MB' => 1048576, // pow( 1024, 2)
			'kB' => 1024, // pow( 1024, 1)
			'B ' => 1, // pow( 1024, 0)
			);
		foreach ($unit as $un => $mag) {
			if (doubleval($bytes) >= $mag)
				return round($bytes / $mag, $precision).' '.$un;
		} 
	} 
} 



/*
 * php zip解压类
 * auther by Sext (sext@neud.net) (Changed: 2003-03-01)
 * last changed & 代码优化 by：kalcaddle (kalcaddle@qq.com)
 * time:2012/6/3 星期日

	demo:
	------------------
	set_time_limit(0);
	$zip_file = 'D:/wwwroot/tt.zip';
	$unzip_to = 'D:/wwwroot/tt';
	$zip_file = iconv('utf-8','gbk',$zip_file);
	$unzip_to = iconv('utf-8','gbk',$unzip_to);
	$z = new unZip;
	if ($z->Extract($zip_file,$unzip_to) ==-1){
		echo("<br>文件 $zip_file 错误.<br>");
	}else {
		echo '<br>完成,共建立'.$z->total_folders.'个目录,'.$z->total_files.'个文件.<br>';
		clearstatcache();
	}
	------------------
 */

class unZip {
	var $total_files = 0;
	var $total_folders = 0;

	function iconv_out($str){
		global $config;
		return iconv(			
			$config['system_charset'],
			$config['app_charset'],
			urldecode($str)
		);
	}

	function Extract ($zn, $to, $index = Array(-1)){
		// linux下解压出错，故先使用系统函数
		$zip = new ziparchive();
		if ($zip -> open($zn) === true) {
			$zip -> extractto($to);
			$zip -> close();
			return 1;
		}else{
			return -1;
		}
//		$ok = 0;
//		$zip = @fopen($zn, 'rb');
//		if (!$zip) return(-1);
//		$cdir = $this -> ReadCentralDir($zip, $zn);
//		$pos_entry = $cdir['offset'];
//
//		if (!is_array($index)) {
//			$index = array($index);
//		} 
//		for($i = 0; $index[$i];$i++) {
//			if (intval($index[$i]) != $index[$i] || $index[$i] > $cdir['entries'])
//				return(-1);
//		} 
//		for ($i = 0; $i < $cdir['entries']; $i++) {
//			@fseek($zip, $pos_entry);
//			$header = $this -> ReadCentralFileHeaders($zip);
//			$header['index'] = $i;
//			$pos_entry = ftell($zip);
//			@rewind($zip);
//			fseek($zip, $header['offset']);
//			if (in_array("-1", $index) || in_array($i, $index))
//				$stat[$header['filename']] = $this -> ExtractFile($header, $to, $zip);
//		} 
//		fclose($zip);
//		return $stat;
	} 

	function ReadFileHeader($zip){
		$binary_data = fread($zip, 30);
		$data = unpack('vchk/vid/vversion/vflag/vcompression/vmtime/vmdate/Vcrc/Vcompressed_size/Vsize/vfilename_len/vextra_len', $binary_data);

		$header['filename'] = fread($zip, $data['filename_len']);
		if ($data['extra_len'] != 0) {
			$header['extra'] = fread($zip, $data['extra_len']);
		} else {
			$header['extra'] = '';
		} 

		$header['compression'] = $data['compression'];
		$header['size'] = $data['size'];
		$header['compressed_size'] = $data['compressed_size'];
		$header['crc'] = $data['crc'];
		$header['flag'] = $data['flag'];
		$header['mdate'] = $data['mdate'];
		$header['mtime'] = $data['mtime'];

		if ($header['mdate'] && $header['mtime']) {
			$hour = ($header['mtime']&0xF800) >> 11;
			$minute = ($header['mtime']&0x07E0) >> 5;
			$seconde = ($header['mtime']&0x001F) * 2;
			$year = (($header['mdate']&0xFE00) >> 9) + 1980;
			$month = ($header['mdate']&0x01E0) >> 5;
			$day = $header['mdate']&0x001F;
			$header['mtime'] = mktime($hour, $minute, $seconde, $month, $day, $year);
		} else {
			$header['mtime'] = time();
		} 

		$header['stored_filename'] = $header['filename'];
		$header['status'] = "ok";
		return $header;
	} 

	function ReadCentralFileHeaders($zip){
		$binary_data = fread($zip, 46);
		$header = unpack('vchkid/vid/vversion/vversion_extracted/vflag/vcompression/vmtime/vmdate/Vcrc/Vcompressed_size/Vsize/vfilename_len/vextra_len/vcomment_len/vdisk/vinternal/Vexternal/Voffset', $binary_data);

		if ($header['filename_len'] != 0)
			$header['filename'] = fread($zip, $header['filename_len']);
		else $header['filename'] = '';

		if ($header['extra_len'] != 0)
			$header['extra'] = fread($zip, $header['extra_len']);
		else $header['extra'] = '';

		if ($header['comment_len'] != 0)
			$header['comment'] = fread($zip, $header['comment_len']);
		else $header['comment'] = '';

		if ($header['mdate'] && $header['mtime']) {
			$hour = ($header['mtime'] &0xF800) >> 11;
			$minute = ($header['mtime'] &0x07E0) >> 5;
			$seconde = ($header['mtime'] &0x001F) * 2;
			$year = (($header['mdate'] &0xFE00) >> 9) + 1980;
			$month = ($header['mdate'] &0x01E0) >> 5;
			$day = $header['mdate'] &0x001F;
			$header['mtime'] = mktime($hour, $minute, $seconde, $month, $day, $year);
		}
		else {
			$header['mtime'] = time();
		} 
		$header['stored_filename'] = $header['filename'];
		$header['status'] = 'ok';
		if (substr($header['filename'], -1) == '/')
			$header['external'] = 0x41FF0010;
		return $header;
	} 

	function ReadCentralDir($zip, $zip_name){
		$size = filesize($zip_name);
		if ($size < 277) $maximum_size = $size;
		else $maximum_size = 277;

		fseek($zip, $size - $maximum_size);
		$pos = ftell($zip);
		$bytes = 0x00000000;

		while ($pos < $size) {
			$byte = fread($zip, 1);
			$bytes = ($bytes << 8) | ord($byte);
			if ($bytes == 0x504b0506 or $bytes == 0x2e706870504b0506) {
				$pos++;
				break;
			} 
			$pos++;
		} 

		$fdata = fread($zip, 18);
		$data = unpack('vdisk/vdisk_start/vdisk_entries/ventries/Vsize/Voffset/vcomment_size', $fdata);

		if ($data['comment_size'] != 0) $centd['comment'] = fread($zip, $data['comment_size']);
		else $centd['comment'] = '';
		$centd['entries'] = $data['entries'];
		$centd['disk_entries'] = $data['disk_entries'];
		$centd['offset'] = $data['offset'];
		$centd['disk_start'] = $data['disk_start'];
		$centd['size'] = $data['size'];
		$centd['disk'] = $data['disk'];
		return $centd;
	} 

	function ExtractFile($header, $to, $zip){
		$header = $this -> readfileheader($zip);
		if (substr($to, -1) != "/") $to .= "/";
		if ($to == './') $to = '';
		$pth = explode("/", $to . $header['filename']);
		$mydir = '';
		for($i = 0;$i < count($pth)-1;$i++) {
			if (!$pth[$i]) continue;
			$mydir .= $pth[$i] . "/";
			if (
				(!is_dir($mydir) && mkdir($mydir, 0755)) ||
				(($mydir == $to . $header['filename'] || 
				($mydir == $to && $this -> total_folders == 0)
				)&& is_dir($mydir))
			){
				chmod($mydir, 0755);
				$this -> total_folders ++;
				//echo "<li>dir: {$this->iconv_out($mydir)}</li>";
			} 
		} 

		if (strrchr($header['filename'], '/') == '/') return;
		if (!($header['external'] == 0x41FF0010) && !($header['external'] == 16)) {
			if ($header['compression'] == 0) {
				$fp = fopen($to . $header['filename'], 'wb');
				if (!$fp) return(-1);
				$size = $header['compressed_size'];

				while ($size != 0) {
					$read_size = ($size < 2048 ? $size : 2048);
					$buffer = fread($zip, $read_size);
					$binary_data = pack('a' . $read_size, $buffer);
					fwrite($fp, $binary_data, $read_size);
					$size -= $read_size;
				} 
				fclose($fp);
				touch($to . $header['filename'], $header['mtime']);
			}
			else {
				$fp = fopen($to . $header['filename'].'.gz','wb');
				if (!$fp) return(-1);
				$binary_data = pack(
					'va1a1Va1a1',
					0x8b1f,
					Chr($header['compression']),
					Chr(0x00),
					time(),
					Chr(0x00),
					Chr(3)
				);

				fwrite($fp, $binary_data, 10);
				$size = $header['compressed_size'];

				while ($size != 0) {
					$read_size = ($size < 1024 ? $size : 1024);
					$buffer = fread($zip, $read_size);
					$binary_data = pack('a' . $read_size, $buffer);
					fwrite($fp, $binary_data, $read_size);
					$size -= $read_size;
				} 

				$binary_data = pack('VV', $header['crc'], $header['size']);
				fwrite($fp, $binary_data, 8);
				fclose($fp);

				$gzp = gzopen($to . $header['filename'] . '.gz', 'rb') or die("Cette archive est compress閑");
				if (!$gzp) return(-2);
				$fp = fopen($to . $header['filename'], 'wb');
				if (!$fp) return(-1);
				$size = $header['size'];

				while ($size != 0) {
					$read_size = ($size < 2048 ? $size : 2048);
					$buffer = gzread($gzp, $read_size);
					$binary_data = pack('a' . $read_size, $buffer);
					fwrite($fp, $binary_data, $read_size);
					$size -= $read_size;
				} 
				fclose($fp);
				gzclose($gzp);

				touch($to . $header['filename'], $header['mtime']);
				unlink($to . $header['filename'].'.gz');
			} 
		} 

		$this -> total_files ++;
		//echo "<li>file: {$this->iconv_out($to.$header[filename])}</li>";
		return true;
	} 
}
