if(isset($_REQUEST['asset']))
{
	$asset = $_REQUEST['asset'];
	$f = "/tmp/".$asset;
	$fh = fopen($f, "r");
	$data = fread($fh, filesize($f));
	fclose($fh);
	echo($data);
}	

