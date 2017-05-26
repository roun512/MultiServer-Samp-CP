	<div class="wrapper">
		<div class="header">

			<nav class="navbar navbar-inverse" style="border-radius:0;">
				 <div class="container-fluid">
				 	<div class="navbar-header">
					 	<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
						 	<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
						</button>
				    	<a class="navbar-brand" href="http://www.advance-gaming.com/">Advance Gaming</a>
				    </div>
				    <div class="collapse navbar-collapse">
					    <ul class="nav navbar-nav">
						    <li><a href="<?=$app->config['domain'];?>">Home</a></li>
						    <li><a href="http://advance-gaming.com/forum/index.php">Forums</a></li>
						    <li><a href="/servers.php">Servers</a></li>
						    <li><a href="#">Donate</a></li>
					    </ul>

					    <ul class="nav navbar-nav navbar-right">
					    	<?php if(!$app->user->LoggedIn): ?>
					    	<li><a href="./"><span class="glyphicon glyphicon-log-in"></span> Login</a></li>
					    	<?php else: ?>
					    		<?php if($app->user->admin > 0): ?>
					    	<li><a href="/admin/"><span class="glyphicon glyphicon-cog"></span> Admin</a></li>
					    		<?php endif; ?>
					    	<li><a href="?logout"><span class="glyphicon glyphicon-log-out"></span> Logout</a></li>
					    	<li><a href="/"><span class="glyphicon glyphicon-user"></span> <?=$app->user->name;?></a></li>
					    	<?php endif; ?>
					    </ul>
					</div>
				 </div>
			</nav>
		</div>
		<div class="container-fluid">
			<div class="row row-offcanvas row-offcanvas-left">