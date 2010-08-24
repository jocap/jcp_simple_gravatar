<html>
<head>
<title>plugin compiler 1.0</title>
</head>
<body>
<h1>compiler</h1>
<div class="message">
<?php
if (!empty($_GET['plugin'])){
    $s = '../plugins/' . $_GET['plugin'] . '/source/' . $_GET['plugin'];
    require_once($s . '.configuration.php');    
    $plugin['help_raw'] = trim(file_get_contents($s . '.documentation.txt'));
    $plugin['help_raw'] .= "\n\n" . trim(file_get_contents($s . '.history.txt'));
    @include('classTextile.php');
	if (class_exists('Textile')) {
		$textile = new Textile();
        $plugin['help']     = $textile->TextileThis($plugin['help_raw']);
	}
    $code               = file($s . '.php');
    $plugin['code']     = '';
    for ($i=1; $i < count($code) - 1; $i++) {
        $plugin['code'] .= rtrim($code[$i])."\n";
    };
    $plugin['md5']      = md5( $plugin['code'] );
    $f  = '../plugins/' . $_GET['plugin'] . '/current/' . $plugin['name'] . '.txt';
    //var_dump($plugin);
    $fp = fopen($f, 'w+');
    fwrite($fp, compile_plugin($plugin));
    fclose($fp);
    echo "Plugin compiled.";
};
?>
</div>
<table>
<tr>
<th>plugin</th>
<th>functions</th>
</tr>
<?php
$dir = dirIndex('../plugins');
foreach ($dir as $plugin){
    ?>
    <tr>
    <td><?php echo $plugin;?></td>
    <td><a href="?plugin=<?php echo $plugin;?>">compile</a></td>
    </tr>
    <?php
};
?>
</table>
</body>
</html><?php
/**
 * @return array Dateinamen des überprüften Verzeichnisses
 * @author Graumeister <devcon@GrauHirn.org>
 * @param $dir string absoluter Pfad zum Verzeichnis
 * @param $ordner bool sollen Ordner in die Liste aufgenommen werden?
 * @desc überprüft ein Verzeichnis und gibt alle enthaltenen Dateien (und Ordner) zurück
 */
function dirIndex($dir,$ordner = false){
	if (is_dir($dir)){
		$output = array();
		$handle = opendir($dir);
		if (!$handle) {
			return false;
		}
		while ($file = readdir($handle)) {
			if ($file <> "." && $file <> ".." && $file <> ".svn" && $file <> "CVS") {
				if (!is_dir($file)) {
					$output[] = $file;
				}
				elseif (is_dir($file) && $ordner == true) {
					$output[] = $file;
				}
			}
		}
		closedir($handle);
		if (is_array($output)) {
			sort($output);
		}
		return $output;
	}
	return false;
}

function compile_plugin($plugin = false) {
	//header('Content-type: text/plain');
	$compiled = date('r');
	$header = <<<EOF
# Plugin-Name:      {$plugin['name']}
# Plugin-Version:   {$plugin['version']}
# Description:      {$plugin['description']}
# Author:           {$plugin['author']}
# More Info at:     {$plugin['author_uri']}
# Compiled at:      $compiled

# ......................................................................
# This is a plugin for Textpattern - http://textpattern.com/
# To install: textpattern > admin > plugins
# Paste the following text into the 'Install plugin' box:
# ......................................................................
EOF;
	$body = trim(chunk_split(base64_encode(gzencode(serialize($plugin))), 72));
	return $header . "\n\n" . trim(chunk_split(base64_encode(serialize($plugin)), 72)). "\n";
}
?>