<?php
/*prende la lista originale e la converte nel formato nuovo, questo viene poi aperto da geojson.io per generare il file geojson */

$csv=csvRead("dati.txt");

foreach ($csv as &$line){
$proc=inverseMercator($line['lat'],$line['lon']);
$line['lat']=$proc[0];
$line['lon']=$proc[1];
}

echo csvDump($csv);

function csvRead($file)
{
	if (($handle = fopen($file, "r")) !== FALSE) {
		$k=0;

		while (($data = fgetcsv($handle, 1000, ";",'"')) !== FALSE) {
			++$k;
			if ($k==1)
			{
				$i=-1;
				foreach ($data as $cell)
				{
					$titles[++$i]=$cell;
				}
			continue;
			}

			$i=-1;
				foreach ($data as $cell)
				{
				$csv[$k-2][$titles[++$i]]=$cell;
				}
		}
		fclose($handle);
	}
	return $csv;
}

function csvDump($arr)
{
	$tmp='';
	foreach ($arr as $line)
	{
        if (!is_array($line))$tmp.=$line;
        else
		$tmp.= implode(";",$line)."\n";
    }	
    return 	$tmp;
}

function inverseMercator($lat,$lon) {
  $lon = ($lon / 20037508.34) * 180;
  $lat =  ($lat / 20037508.34) * 180;

  $lat = 180/pi() * (2 * atan(exp($lat * pi() / 180)) - pi() / 2);

	return array ($lat, $lon);
}
?>