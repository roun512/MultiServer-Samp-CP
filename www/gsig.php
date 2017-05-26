<?php
require_once 'init.php';

if(isset($_GET['svr']) && isset($_GET['id']))
{
	if(strtolower($_GET['svr']) == 'cod')
	{
		$stats = $app->functions->GetPlayerCODStats($_GET['id']);

		$user_arr = array(
            "Score" => number_format($stats->score, 0, ',', '.'),
            "Money" => number_format($stats->money, 0, ',', '.'),
            "Kills" => number_format($stats->kills, 0, ',', '.'),
            "Deaths" => number_format($stats->deaths, 0, ',', '.'),
            "K/D Ratio" => number_format($stats->kills / ($stats->deaths != 0 ? $stats->deaths : 1), 2),
            "Captured Zones" => number_format($stats->zones, 0, ',', '.'),
            "new-column" => '',
            "Admin Level" => number_format($stats->adminlevel, 0, ',', '.'),
            "VIP Level" => number_format($stats->viplevel, 0, ',', '.'),
            ($stats->clan > 0 ? $app->functions->GetPlayerCODClanName(intval($stats->clan), $clan_name, $clan_tag) : '') => (isset($clan_name) ? $clan_name : 'Unknown'),
            (isset($clan_tag) ? 'Clan Tag' : '') => (isset($clan_tag) ? $clan_tag : ''),
            "Last Seen" => $stats->laston,
            "Reg Date" => $stats->regdate
        );

		$image = imagecreatefrompng("files/images/sig.png");
		$imagesize = getimagesize("files/images/sig.png");
		$font_size_header = 15;
        $sig_header = array(
            "enabled" => true,
            "font" => 'files/fonts/Trivial-Regular.otf',
            "text" => $stats->name,
            "fontcolor" => imagecolorallocate($image, 255, 255, 255),
            "fontsize" => $font_size_header,
            "y-start" => $font_size_header+32,
            "max-width" => 150
        );
        $font_size = 9;
        $sig = array(
            "font" => 'files/fonts/Orbitron-Bold.ttf',
            "fontcolor" => imagecolorallocate($image, 255, 255, 255),
            "fontsize" => $font_size,
            "angle" => 0, // DO NOT CHANGE
            "x-start" => $imagesize[0]/6.8, // Previous: 8
            "x-end" => $imagesize[0]-($imagesize[0]/7.8), // 160 to not overflow
            "x-add" => 0, // DO NOT CHANGE
            "y-start" => $font_size+47,
            "y-end" => $imagesize[1]-51,
            "y-add" => 0, // DO NOT CHANGE
            "x-add-new-column" => 190, // 200
            "first-in-line" => true, // DO NOT CHANGE
            "new-line" => $font_size * 2,
            "new-line-per-key" => true
        );

        if($sig_header['enabled'] == true)
        {
            $header_validated = false;
            while($header_validated == false)
            {
                $bbox_header = imagettfbbox($sig_header['fontsize'], $sig['angle'], $sig_header['font'], $sig_header['text']);
                $text_width = max($bbox_header[2]-$bbox_header[0], $bbox_header[4]-$bbox_header[6]);
                if($text_width <= $sig_header['max-width'])
                    $header_validated = true;
                else
                {
                    $sig_header['y-start'] -= 0.4;
                    if(--$sig_header['fontsize'] < 1)
                        break;
                }
            }
            if($header_validated)
            {
                $y_add = max($bbox_header[1]-$bbox_header[7], $bbox_header[3]-$bbox_header[5]);
                $x_start = (max($bbox_header[2]-$bbox_header[0], $bbox_header[4]-$bbox_header[6]) / 2) + 4;
                $sig['y-start'] += $y_add + 5;
                imagettftext($image, $sig_header['fontsize'], $sig['angle'], $imagesize[0] / 2 - $x_start, $sig_header['y-start'], $sig_header['fontcolor'], $sig_header['font'], $sig_header['text']);
            }

            foreach(array_keys($user_arr) as $key)
            {
                $validated = false;
                if(strcasecmp($key, "new-column") == 0)
                {
                    $sig['y-add'] = 0;
                    $sig['x-start'] += ($sig['x-add-new-column']);
                    $validated = 'false';
                }
                else if(empty($key))
                {
                    $validated = 'false';
                }
                while($validated === false)
                {
                    $text = $key . ': ' . $user_arr[$key];
                    if($sig['first-in-line'] == false)
                        $text = ', ' . $text;
                    $bbox = imagettfbbox($sig['fontsize'], $sig['angle'], $sig['font'], $text);
                    $y_add = max($bbox[1]-$bbox[7], $bbox[3]-$bbox[5]);
                    if($sig['y-start'] + $sig['y-add'] + $y_add >= $sig['y-end'])
                    {
                        $validated = NULL;
                        break;
                    }
                    $x_add = max($bbox[2]-$bbox[0], $bbox[4]-$bbox[6]);
                    if($sig['x-start'] + $sig['x-add'] + $x_add <= $sig['x-end'])
                    {
                        $validated = true;
                        imagettftext($image, $sig['fontsize'], $sig['angle'], $sig['x-start']+$sig['x-add'], $sig['y-start']+$sig['y-add'], $sig['fontcolor'], $sig['font'], $text);
                        if($sig['x-add-new-column'] < $x_add)
                            $sig['x-add-new-column'] = $x_add;
                        if($sig['new-line-per-key'] == true)
                            $sig['y-add'] += $sig['new-line'];
                        else
                        {
                            $sig['x-add'] += $x_add;
                            $sig['first-in-line'] = false;
                        }
                    }
                    else
                    {
                        $sig['x-add'] = 0;
                        //$sig['y-add'] += $sig['new-line'];
                        $sig['first-in-line'] = true;
                        $validated = 'false';
                        continue;
                    }
                }
                if($validated == NULL)
                    break;
            }
            /*$sitetext = imagettfbbox($sig['fontsize'], $sig['angle'], $sig['font'], "Advance-Gaming.com");
            $site_x = $imagesize[0] - max($sitetext[2]-$sitetext[0], $sitetext[4]-$sitetext[6]) - 5;
            $site_y = $imagesize[1] - 5;// - max($sitetext[1]-$sitetext[7], $sitetext[3]-$sitetext[5]);
            imagettftext($image, $sig['fontsize'], 0, $site_x, $site_y, $sig['fontcolor'], $sig['font'], "Advance-Gaming.com");*/
            header('Content-type: ' . $imagesize['mime']);
            imagealphablending($image, true);
            imagesavealpha($image, true);
            imagepng($image);
            imagedestroy($image);
        }
	}
}