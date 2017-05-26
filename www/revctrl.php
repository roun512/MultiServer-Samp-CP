<?php
$projectId = 141; 

$revisionTypes = ['addition' => "{00D619}added\t",
					'removal' => "{FF4F4F}removed",
					'change' => "{FFA500}changed",
					'fix' => "{FFA500}fixed\t"
				];

$jsonData = json_decode(file_get_contents("https://www.revctrl.com/api/projects/" . $projectId), true);
if($jsonData)
{
	$latestChangelog = $jsonData['latest_changelog'];
	$changelogString = "{FFFFFF}List of updates for " . $latestChangelog['version']  . ", published on " . $latestChangelog['updated_at'] . ".\n\n";
	$latestRevisionsString = null;
	foreach ($latestChangelog['revisions'] as $key) 
	{
		$revisionString = $revisionTypes[$key['type']] . "{FFFFFF}\t" . html_entity_decode(strip_tags($key['revision'])) . "\n";
		if ($key['is_recent']) 
		{
			$latestRevisionsString .= $revisionString;
		} 
		else 
		{
			$changelogString .= $revisionString;
		}
	}

	if (is_null($latestRevisionsString) == false) 
	{
		$changelogString .= "\nRevisions made in last 24 hours:\n\n" . $latestRevisionsString;
	}
        echo $changelogString;
} 
else 
{
	echo "Could not load the updates.";
}
?>