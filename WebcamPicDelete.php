<?php
	
	include "ImageCompare.class.php";
	
	// Dateien einlesen
	$dir      = "webcam_images";
	$imgfiles = array();
	if ($handle = opendir($dir))
	{
		while (false !== ($file = readdir($handle)))
		{
			if ($file != "." && $file != ".." && preg_match('/\.jpg$/', $file))
			{
				$imgfiles[ filemtime($dir . "/" . $file) ] = $dir . "/" . $file;
			}
		}
		closedir($handle);
	}
	
	// Nach Datum sortieren
	ksort($imgfiles);
	
	// Alle Bilder durchgehen
	$lastfile = "";
	foreach ($imgfiles as $key => $val)
	{
		if (!empty($lastfile))
		{
			// Zwei Dateien vergeleichen
			$i = new ImageCompare($lastfile, $val);
			// Wieviel Prozent des Bildes müssen übereinstimmen?
			$i->setProzent(70);
			// Bilder vergleichen : true => Bilder stimmen überein
			$r = $i->compare();
			
			print("Pixel (erstes Bild)    : " . $i->getAllPixel() . "\n");
			print("Pixel-Uebereinstimmung : " . $i->getTruePixel() . "\n");
			print("Pixel-Unterschiede     : " . $i->getFalsePixel() . "\n");
			
			if ($r === true)
			{
				// Ein Bild löschen wenn gleich
				unlink($lastfile);
				print($lastfile . " geloescht\n");
			}
		}
		
		$lastfile = $val;
	}