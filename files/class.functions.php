<?php

/**
* @author roun512
*/
class functions
{
	
	private $app;

	function __construct($app)
	{
		$this->app = $app;
	}

	public function EncryptPassword($password) {
		return strtoupper(hash("whirlpool", $password));
	}

	public function CalculateActivity($activity) {
		return sprintf("%02d hours and %02d minutes.", floor($activity/3600), ($activity/60)%60);
	}


	public function GetCODPlayers() {
		try {
			$st = $this->app->coddb->prepare("SELECT name, adminlevel, viplevel FROM users WHERE connected = 1");
			$st->execute();
			$result = $st->fetchAll();
			return $result;
		} catch(Exception $e) {
			echo $e->getMessage();
		}
	}

	public function GetCNRPlayers() {
		try {
			$st = $this->app->cnrdb->prepare("SELECT name, admin FROM players_data WHERE online > -1");
			$st->execute();
			$result = $st->fetchAll();
			return $result;
		} catch(Exception $e) {
			echo $e->getMessage();
		}
	}

	public function GetCODPlayerCount() {
		try {
			$st = $this->app->coddb->prepare("SELECT COUNT(*) as players FROM users WHERE connected = 1");
			$st->execute();
			$result = $st->fetch();
			return $result->players;
		} catch(Exception $e) {
			echo $e->getMessage();
		}
	}

	public function GetCNRPlayerCount() {
		try {
			$st = $this->app->cnrdb->prepare("SELECT COUNT(*) as players FROM players_data WHERE online > -1");
			$st->execute();
			$result = $st->fetch();
			return $result->players;
		} catch(Exception $e) {
			echo $e->getMessage();
		}
	}

	public function GetGWPlayers() {
		try {
			$st = $this->app->gwdb->prepare("SELECT Nick as name, Admin as admin FROM accounts WHERE connected = 1");
			$st->execute();
			$result = $st->fetchAll();
			return $result;
		} catch(Exception $e) {
			echo $e->getMessage();
		}
	}

	public function GetGWPlayerCount() {
		try {
			$st = $this->app->gwdb->prepare("SELECT COUNT(*) as players FROM accounts WHERE connected = 1");
			$st->execute();
			$result = $st->fetch();
			return $result->players;
		} catch(Exception $e) {
			echo $e->getMessage();
		}
	}
	
	public function GetBFPlayers() {
		try {
			$st = $this->app->bfdb->prepare("SELECT pName as name, pAdmin as admin FROM accounts WHERE pOnline = 1");
			$st->execute();
			$result = $st->fetchAll();
			return $result;
		} catch(Exception $e) {
			echo $e->getMessage();
		}
	}

	public function GetBFPlayerCount() {
		try {
			$st = $this->app->bfdb->prepare("SELECT COUNT(*) as players FROM players WHERE pOnline = 1");
			$st->execute();
			$result = $st->fetch();
			return $result->players;
		} catch(Exception $e) {
			echo $e->getMessage();
		}
	}

	public function IsValidName($name)
	{
	    if(preg_match("/^[a-z0-9\[\]()$@._=]{3,20}$/i", $name) === 1)
	            return true;
	    return false;
	}

	public function GetWeaponName($id) {
		$name = null;
		switch ($id) {
			case 1:
				$name = "Brass Knuckles";
				break;
			case 2:
				$name = "Golf Club";
				break;
			case 3:
				$name = "Nightstick";
				break;
			case 4:
				$name = "Knife";
				break;
			case 5:
				$name = "Baseball Bat";
				break;
			case 6:
				$name = "Shovel";
				break;
			case 7:
				$name = "Pool Cue";
				break;
			case 8:
				$name = "Katana";
				break;
			case 9:
				$name = "Chainsaw";
				break;
			case 10:
				$name = "Purple Dildo";
				break;
			case 11:
				$name = "Dildo";
				break;
			case 12:
				$name = "Vibrator";
				break;
			case 13:
				$name = "Silver Vibrator";
				break;
			case 14:
				$name = "Flower";
				break;
			case 15:
				$name = "Cane";
				break;
			case 16:
				$name = "Grenade";
				break;
			case 17:
				$name = "Tear Gas";
				break;
			case 18:
				$name = "Molotov Cocktail";
				break;
			case 22:
				$name = "Colt 45";
				break;
			case 23:
				$name = "Silenced Pistol";
				break;
			case 24:
				$name = "Desert Eagle";
				break;
			case 25:
				$name = "Shotgun";
				break;
			case 26:
				$name = "Sawn-off Shotgun";
				break;
			case 27:
				$name = "Combat Shotgun";
				break;
			case 28:
				$name = "UZI";
				break;
			case 29:
				$name = "MP5";
				break;
			case 30:
				$name = "AK-47";
				break;
			case 31:
				$name = "M4";
				break;
			case 32:
				$name = "Tec-9";
				break;
			case 33:
				$name = "Country Rifle";
				break;
			case 34:
				$name = "Sniper Rifle";
				break;
			case 35:
				$name = "Rocket Launcher";
				break;
			case 36:
				$name = "Heat Seaker";
				break;
			case 37:
				$name = "Flame Thrower";
				break;
			case 38:
				$name = "Minigun";
				break;			
			default:
				$name = null;
				break;
		}
		return $name;
	}

	public function GetPlayerCODStats($id)
	{
		$st = $this->app->coddb->prepare("SELECT * FROM users WHERE id = ?");
		$st->bindParam(1, $id, PDO::PARAM_INT);
		$st->execute();
		return $st->fetch();

	}

	public function GetPlayerCODClanName($id, &$name, &$tag)
	{
	    $name = "Unknown";
	    $st = $this->app->coddb->prepare("SELECT id,name,tag FROM clans WHERE id = ? LIMIT 1");
	    $st->bindParam(1, $id, PDO::PARAM_INT);
	    $st->execute();
	    if($st->fetchColumn() == 1)
	    {
	    	$clan = $st->fetch();
	        $name = $clan->name;
	        $tag = $clan->tag;
	        return 'Clan';
	    }
	    else
	        return '';
	}
}
?>