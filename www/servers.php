<?php
require_once 'init.php';

$app->page->title = "Servers";

require_once 'header.php';

?>

<div class="panel panel-info" style="width: 90%; margin: auto;">
  <div class="panel-heading">Servers</div>
</div>
<div class="panel panel-default" style="width: 90%; margin: auto;">
  <div class="panel-heading">
  	<div class="container-fluid panel-container" style="width:43%; float:left;">
  		<span>
  			<b>Call of Duty Global Warfare</b>
  		</span>
	  	<span><a href="samp://ucp.advance-gaming.com:7777" class="btn btn-success" style="float: right;">Play</a></span>
  	</div>
  	<div class="container-fluid panel-container" style="width:50%; float:right;">
  		<span>
  			<b>Advance Cops 'n' Robbers</b>
  		</span>
	  	<span class="text-right"><a href="samp://ucp.advance-gaming.com:8888" class="btn btn-success" style="float: right;">Play</a></span>
  	</div>
  	<div class="clearfix"></div>
  </div>
  <div class="panel-body">
  	<table class="table" style="width: 50%; float:left; border-right: 2px solid #ddd;">
  		<thead>
		  	<tr>
		  		<th>Player</th>
		  	</tr>
		</thead>
		<tbody>
		  	<?php
		  		$CODplayers = $app->functions->GetCODPlayers();
		  		if(count($CODplayers) == 0) {
		  			echo "<tr><td>The server is currently empty.</td></tr>";
		  		} else {
			  		foreach ($CODplayers as $player): ?>
			  			<tr><td>
			  			<?=$player->name;?>
			  			<?php
			  			$tag = "";
			  			if($player->adminlevel > 4) $tag = "<span style='color:red;'>(Manager)</span>";
			  			elseif($player->adminlevel > 1) $tag = "<span style='color:#FC00EC;'>(Administrator)</span>";
			  			elseif($player->adminlevel > 0) $tag = "<span style='color:purple;'>(Moderator)</span>";
			  			elseif($player->viplevel > 0) $tag = "<span style='color:yellow;'>(Donator)</span>";
			  			?>
			  			<?=$tag;?>
			  			</td></tr>
			  		<?php endforeach;
			  	} ?>
		</tbody>
  	</table>
  	<table class="table" style="width: 50%; float:right; border-left: 2px solid #ddd;">
  		<thead>
		  	<tr>
		  		<th>Player</th>
		  	</tr>
		</thead>
		<tbody>
		  	<?php
		  		$CNRplayers = $app->functions->GetCNRPlayers();
		  		if(count($CNRplayers) == 0) {
		  			echo "<tr><td>The server is currently empty.</td></tr>";
		  		} else {
			  		foreach ($CNRplayers as $player): ?>
			  			<tr><td>
			  			<?=$player->name;?>
			  			<?php
			  			$tag = "";
			  			if($player->admin > 4) $tag = "<span style='color:red;'>(Manager)</span>";
			  			elseif($player->admin > 1) $tag = "<span style='color:#FC00EC;'>(Administrator)</span>";
			  			elseif($player->admin > 0) $tag = "<span style='color:purple;'>(Moderator)</span>";
			  			?>
			  			<?=$tag;?>
			  			</td></tr>
			  		<?php endforeach;
			  	} ?>
		</tbody>
  	</table>
  </div>
  <div class="panel-footer"></div>
</div>
<?php require_once 'footer.php'; ?>