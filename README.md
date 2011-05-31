requires JSMin installed

use:
	
	$tpl = new TemplateOptimizer();
	$tpl->someLocalVariable = true;
	$tpl->display('templates/someTemplate.php',[$compress(bool)]);
	
Right now, TemplateOptimizer saves compiled JS and CSS in the temp directory and serves cacheable, static files. Script and Link tags are placed at the end of the document (before the closing body tag) that reference these temp files.  This requires an asset handler that mediates requests for these temp files by the client. I
ll update with this shortly.
