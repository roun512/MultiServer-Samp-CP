<?php
/**
* @author roun512
*/
class coduser
{
	
	private $app;

	function __construct($app)
	{
		$this->app = $app;
	}

	public function ChangePassword($oldpass, $newpass) {
		try {
			$st = $this->app->coddb->prepare("SELECT * FROM users WHERE id = ? AND password = ?");
			$st->bindParam(1, $this->app->user->id, PDO::PARAM_INT);
			$st->bindParam(2, $oldpass, PDO::PARAM_STR);
			$st->execute();
			if($st->rowCount() == 0) {
				$this->app->error = "Your current password you entered is incorrect.";
			} else {
				$result = $st->fetch();
				if($result->connected == 0) {
					if(strlen($newpass) < 4 || strlen($newpass) > 30) {
						$this->app->error = "Your new password must be between 4 and 30.";
					} else {
						$newpass = $this->app->functions->EncryptPassword($newpass);
						$st = $this->app->coddb->prepare("UPDATE users SET password = ? WHERE id = ? AND password = ?");
						$st->bindParam(1, $newpass, PDO::PARAM_STR);
						$st->bindParam(2, $this->app->user->id, PDO::PARAM_INT);
						$st->bindParam(3, $oldpass, PDO::PARAM_STR);
						if($st->execute()) {
							$this->app->success = "You have changed your password successfully.";
						} else {
							$this->app->error = "Something went wrong, please contact any Manager+ regarding this problem.";
						}
					}
				} else {
					$this->app->error = "You are already connected in-game, we could not change your password.";
				}
			}
		} catch(Exception $e) {
			$this->app->error = $e->getMessage();
		}
	}

	public function ChangeUsername($password, $username) {
		try {
			$password = $this->app->functions->EncryptPassword($password);
			$st = $this->app->coddb->prepare("SELECT * FROM users WHERE id = ? AND password = ?");
			$st->bindParam(1, $this->app->user->id, PDO::PARAM_INT);
			$st->bindParam(2, $password, PDO::PARAM_STR);
			$st->execute();
			$result = $st->fetch();
			if(!$result) {
				$this->app->error = "Your current password you entered is incorrect.";
			} else {
				if($result->connected == 0) {
					if($this->app->functions->IsValidName($username)) {
						$oldname = $result->name;
						$st = $this->app->coddb->prepare("SELECT * FROM users WHERE name = ?");
						$st->bindParam(1, $username, PDO::PARAM_STR);
						$st->execute();
						$result = $st->fetch();
						if($result) {
							$this->app->error = "The username you entered already exists.";
						} else {
							if(strpos($username, "[AG]") !== false && $this->app->user->tag == 0) {
								$this->app->error = "You can't wear AG tag. If you think there is a problem contact a Lead Admin to give you the permission to use the tag.";
								return;
							}
							if($this->IsAllowedClanTags($username)) {
								if($this->IsPlayerBanned($oldname)) {
									$this->app->error = "You can't change your name while being banned.";
								} elseif($this->IsPlayerBanned($username)) {
									$this->app->error = "The username you entered is banned.";
								} else {

									if($this->CanChangeName($this->app->user->id) == false)
									{
										$this->app->error = "You must wait 72 hours in order to change your name.";
									} else {

										$money = 300000;
										if($this->app->user->score < 1000)
											$money = 100000;
										elseif(strpos($username, $this->app->user->name) !== false)
											$money = 300000;
										else
											$money = 600000;

										if($this->app->user->money < $money)
										{
											$this->app->error = "You don't have enough money to change your name. ($".$money.")";
										} else {
											$st = $this->app->coddb->prepare("UPDATE users SET name = ?, money = money - ? WHERE id = ?");
											$st->bindParam(1, $username, PDO::PARAM_STR);
											$st->bindParam(2, $money, PDO::PARAM_INT);
											$st->bindParam(3, $this->app->user->id, PDO::PARAM_INT);
											if($st->execute()) {
												$this->app->success = "You have changed your username successfully. It costed you $".$money;
												$this->app->log->AddNameChangeLog($this->app->user->id, $_SERVER['REMOTE_ADDR'], $oldname, $username);
											} else {
												$this->app->error = "Something went wrong, please contact any Manager+ regarding this problem.";
											}
										}
									}
								}
							} else {
								$this->app->error = "You have entered a clan tag you're not in.";
							}
						}
					} else {
						$this->app->error = "The username you entered is invalid.";
					}
				} else {
					$this->app->error = "You are already connected in-game, we could not change your username.";
				}
			}
		} catch (Exception $e) {
			$this->app->error = $e->getMessage();
		}
	}

	public function GetRankName($score) {
		$rank = null;
		if($score > 80000)
			$rank = "Hero Veteran";
		elseif($score > 60000)
			$rank = "Senior Veteran";
		elseif($score > 50000)
			$rank = "Junior Veteran";
		elseif($score > 45000)
			$rank = "Overlord";
		elseif($score > 40000)
			$rank = "General";
		elseif($score > 35000)
			$rank = "Brigadier";
		elseif($score > 30000)
			$rank = "Colonel";
		elseif($score > 25000)
			$rank = "Lt. Colonel";
		elseif($score > 20000)
			$rank = "Major";
		elseif($score > 18000)
			$rank = "Captain";
		elseif($score > 15000)
			$rank = "Commander";
		elseif($score > 13500)
			$rank = "I. Lieutenant";
		elseif($score > 10000)
			$rank = "II. Lieutenant";
		elseif($score > 7500)
			$rank = "Officer";
		elseif($score > 6000)
			$rank = "Sgt. Major";
		elseif($score > 4500)
			$rank = "Master Sgt.";
		elseif($score > 2500)
			$rank = "Gunnery Sgt.";
		elseif($score > 1500)
			$rank = "Staff Sgt.";
		elseif($score > 900)
			$rank = "Sergeant";
		elseif($score > 600)
			$rank = "Corporal";
		elseif($score > 300)
			$rank = "Lance Cpl.";
		elseif($score > 150)
			$rank = "Specialist";
		elseif($score > 50)
			$rank = "Private";
		else
			$rank = "Recruit";
		return $rank;
	}

	public function GetStyleName($style) {
		$name = null;
		switch ($style) {
			case 0:
				$name = "Normal";
				break;
			case 4:
				$name = "Normal";
				break;
			case 5:
				$name = "Boxing";
				break;
			case 6:
				$name = "Kung-Fu";
				break;
			case 7:
				$name = "KneeHead";
				break;
			case 15:
				$name = "GrabKick";
				break;
			case 16:
				$name = "Elbow";
				break;
			default:
				$name = "Normal";
				break;
		}

		return $name;
	}

	public function IsValidClan($id)
	{
		try {
			$st = $this->app->coddb->prepare("SELECT * FROM clans WHERE id = ?");
			$st->bindParam(1, $id, PDO::PARAM_INT);
			$st->execute();
			if($st->rowCount() > 0)
				return true;
			else
				return false;
		} catch(Exception $e) {
			$this->app->error = $e->getMessage();
		}
	}

	public function IsValidUserByID($id) 
	{
		try {
			$st = $this->app->coddb->prepare("SELECT * FROM users WHERE id = ?");
			$st->bindParam(1, $id, PDO::PARAM_INT);
			$st->execute();
			if($st->rowCount() > 0)
				return true;
			else
				return false;
		} catch(Exception $e) {
			$this->app->error = $e->getMessage();
		}
	}

	public function GetUserInfoByID($id) {
		try {
			$st = $this->app->coddb->prepare("SELECT * FROM users WHERE id = ?");
			$st->bindParam(1, $id, PDO::PARAM_INT);
			$st->execute();
			$result = $st->fetch();
			if($result) return $result;
			else return 0;
		} catch(Exception $e) {
			$this->app->error = $e->getMessage();
		}
	}

	public function IsValidUserByName($id) 
	{
		try {
			$st = $this->app->coddb->prepare("SELECT * FROM users WHERE name = ?");
			$st->bindParam(1, $id, PDO::PARAM_STR);
			$st->execute();
			if($st->rowCount() > 0)
				return true;
			else
				return false;
		} catch(Exception $e) {
			$this->app->error = $e->getMessage();
		}
	}

	public function GetUserInfoByName($id) {
		try {
			$st = $this->app->coddb->prepare("SELECT * FROM users WHERE name = ?");
			$st->bindParam(1, $id, PDO::PARAM_STR);
			$st->execute();
			$result = $st->fetch();
			return $result;
		} catch(Exception $e) {
			$this->app->error = $e->getMessage();
		}
	}

	public function GetClanInfoByName($id) {
		try {
			$st = $this->app->coddb->prepare("SELECT *, ROUND((score*SQRT(money)*(kills/deaths))/10000) as points FROM clans WHERE name = ?");
			$st->bindParam(1, $id, PDO::PARAM_STR);
			$st->execute();
			$result = $st->fetch();
			return $result;
		} catch(Exception $e) {
			$this->app->error = $e->getMessage();
		}
	}

	public function GetClanInfoByID($id) {
		try {
			$st = $this->app->coddb->prepare("SELECT *, ROUND((score*SQRT(money)*(kills/deaths))/10000) as points FROM clans WHERE id = ?");
			$st->bindParam(1, $id, PDO::PARAM_INT);
			$st->execute();
			$result = $st->fetch();
			return $result;
		} catch(Exception $e) {
			$this->app->error = $e->getMessage();
		}
	}

	public function GetTopScore() {
		try {
			$st = $this->app->coddb->prepare("SELECT id, name, score FROM users ORDER BY score DESC LIMIT 10");
			$st->execute();
			$result = $st->fetchAll();
			return $result;
		} catch(Exception $e) {
			$this->app->error = $e->getMessage();
		}
	}

	public function GetTopMoney() {
		try {
			$st = $this->app->coddb->prepare("SELECT id, name, money FROM users ORDER BY money DESC LIMIT 10");
			$st->execute();
			$result = $st->fetchAll();
			return $result;
		} catch(Exception $e) {
			$this->app->error = $e->getMessage();
		}
	}

	public function GetTopKills() {
		try {
			$st = $this->app->coddb->prepare("SELECT id, name, kills FROM users WHERE name != 'roun512' ORDER BY kills DESC LIMIT 10");
			$st->execute();
			$result = $st->fetchAll();
			return $result;
		} catch(Exception $e) {
			$this->app->error = $e->getMessage();
		}
	}

	public function GetTopDeaths() {
		try {
			$st = $this->app->coddb->prepare("SELECT id, name, deaths FROM users ORDER BY deaths DESC LIMIT 10");
			$st->execute();
			$result = $st->fetchAll();
			return $result;
		} catch(Exception $e) {
			$this->app->error = $e->getMessage();
		}
	}

	public function GetTopClans() {
		try {
			$st = $this->app->coddb->prepare("SELECT *, ROUND((score*SQRT(money)*(kills/deaths))/10000) as points FROM `clans` ORDER BY points DESC LIMIT 10");
			$st->execute();
			$result = $st->fetchAll();
			return $result;
		} catch(Exception $e) {
			$this->app->error = $e->getMessage();
		}
	}

	public function GetTopRatio() {
		try {
			$st = $this->app->coddb->prepare("SELECT id, name, kills / deaths as ratio FROM users WHERE name != 'roun512' ORDER BY ratio DESC LIMIT 10");
			$st->execute();
			$result = $st->fetchAll();
			return $result;
		} catch(Exception $e) {
			$this->app->error = $e->getMessage();
		}
	}

	public function GetVIPs() {
		try {
			$st = $this->app->coddb->prepare("SELECT name, viplevel as rank, laston, connected FROM users WHERE viplevel > 0 ORDER BY viplevel DESC");
			$st->execute();
			$result = $st->fetchAll();
			return $result;
		} catch(Exception $e) {
			echo $e->getMessage();
		}
	}

	public function GetAdmins() {
		try {
			$st = $this->app->coddb->prepare("SELECT name, adminlevel as rank, laston, connected FROM users WHERE adminlevel > 0 ORDER BY adminlevel DESC");
			$st->execute();
			$result = $st->fetchAll();
			return $result;
		} catch(Exception $e) {
			$this->app->error = $e->getMessage();
		}
	}

	public function GetClansList() {
		try {
			$st = $this->app->coddb->prepare("SELECT * FROM clans");
			$st->execute();
			$result = $st->fetchAll();
			return $result;
		} catch(Exception $e)
		{
			$this->app->error = $e->getMessage();
		}
	}

	public function GetBannedUsers($search = null, $limit, $offset)
	{
		try {
			$query = "SELECT * FROM banned";
			if($search != null) {
				$search = '%'.$search.'%';
				$query .= " WHERE name LIKE :name OR admin LIKE :admin";
			}
			$query .= " ORDER BY id DESC LIMIT :limit OFFSET :offset";
			$st = $this->app->coddb->prepare($query);
			if($search != null){
				$st->bindParam(':name', $search, PDO::PARAM_STR);
				$st->bindParam(':admin', $search, PDO::PARAM_STR);
			}
			$st->bindParam(':limit', $limit, PDO::PARAM_INT);
			$st->bindParam(':offset', $offset, PDO::PARAM_INT);
			$st->execute();
			return $st->fetchAll();
		} catch(Exception $e) {
			$this->app->error = $e->getMessage(); 
		}
	}

	public function GetBannedUsersCount($search = null)
	{
		try {
			$query = "SELECT COUNT(*) as total FROM banned";
			if($search != null) {
				$search = '%'.$search.'%';
				$query .= " WHERE name LIKE ? OR admin LIKE ?";
			}
			$st = $this->app->coddb->prepare($query);
			if($search != null){
				$st->bindParam(1, $search, PDO::PARAM_STR);
				$st->bindParam(2, $search, PDO::PARAM_STR);
			}
			$st->execute();
			return $st->fetch()->total;
		} catch(Exception $e) {
			$this->app->error = $e->getMessage(); 
		}
	}

	public function GenerateUserLink($name) {
		return "<a href='/index.php?name=".$name."'>".htmlentities($name)."</a>";
	}

	public function GenerateClanLink($name) {
		return "<a href='/clan/index.php?id=".$this->GetClanInfoByName($name)->id."'>".htmlentities($name)."</a>";
	}

	public function IsAllowedClanTags($name) {
		preg_match_all("/\[([\w]+)\]/i", $name, $tags, PREG_SET_ORDER);
		$st = $this->app->coddb->prepare("SELECT tag FROM clans WHERE id != (SELECT clan FROM users WHERE id = ?)");
		$st->bindParam(1, $this->app->user->id, PDO::PARAM_INT);
		$st->execute();
		$result = $st->fetchAll();
		foreach($tags as $tag) {
			foreach ($tag as $rtag) {
				foreach ($result as $r) {
					if($rtag == $r->tag) return false;
				}
			}
		}
		return true;
	}

	public function GetClanMembers($id)
	{
		$st = $this->app->coddb->prepare("SELECT * FROM users WHERE clan = ? ORDER BY crank DESC");
		$st->bindParam(1, $id, PDO::PARAM_INT);
		$st->execute();
		return $st->fetchAll();
	}

	public function LeaveClan() {
		if($this->IsValidClan($this->app->user->clan) == false) {
			$this->app->error = "You are not in any clan to leave it.";
		} else {
			$info = $this->GetClanInfoByID($this->app->user->clan);
			if($info->owner == $this->app->user->id) {
				$this->app->error = "You can't leave the clan because you are the owner.";
			} else {
				if($this->app->user->connected == 1) {
					$this->app->error = "You are currently online in-game thus you can't leave the clan.";
				} else {
					try {
						$st = $this->app->coddb->prepare("UPDATE users SET clan = -1, crank = 0 WHERE id = ?");
						$st->bindParam(1, $this->app->user->id, PDO::PARAM_INT);
						if($st->execute()) {
							header('Location: /');
							$this->app->success = "You have left the clan.";
						} else {
							$this->app->error = "Something went wrong while trying to change the level of " . htmlentities($info->name) . " to " . $level . ".";
						}
					} catch(Exception $e) {
						$this->app->error = $e->getMessage();
					}
				}
			}
		}
	}

	public function ChangeClanLevel($id, $level)
	{
		$info = $this->GetUserInfoByID($id);
		$cinfo = $this->GetClanInfoByID($this->app->user->clan);
		if($level < 0) {
			$this->app->error = "The level must be at least 0.";
		} elseif($level > $cinfo->maxlvl) {
			$this->app->error = "The level you entered is more than the max level allowed.";
		} else {
			if($info->clan != $this->app->user->clan) {
				$this->app->error = "The player is not in your clan.";
			} else {
				if($id == $cinfo->owner) {
					$this->app->error = "You can't change the level of the owner.";
				} else {
					if($this->app->user->crank < $cinfo->levelperm) {
						$this->app->error = "You don't have permissions to change the level of a user.";
					} else {
						if($info->connected == 1) {
							$this->app->error = "The player is online thus you can't change his level.";
						} else {
							if($info->crank >= $this->app->user->crank) {
								$this->app->error = "You can't change the level of a member that has the same level as you or higher.";
							} else {
								try {
									$st = $this->app->coddb->prepare("UPDATE users SET crank = ? WHERE id = ?");
									$st->bindParam(1, $level, PDO::PARAM_INT);
									$st->bindParam(2, $id, PDO::PARAM_INT);
									if($st->execute()) {
										$this->app->success = "You have changed the level of " . htmlentities($info->name) . " to " . $level . ".";
									} else {
										$this->app->error = "Something went wrong while trying to change the level of " . htmlentities($info->name) . " to " . $level . ".";
									}
								} catch(Exception $e) {
									$this->app->error = $e->getMessage();
								}
							}
						}
					}
				}
			}
		}
	}

	public function KickFromClan($id) {
		$info = $this->GetUserInfoByID($id);
		if($id == $this->app->user->id) {
			$this->app->error = "You can't kick yourself from the clan.";
		} else {
			if($info->clan == $this->app->user->clan) {
				if($this->GetClanInfoByID($this->app->user->clan)->owner == $id) {
					$this->app->error = "You can't kick the owner of the clan!";
				} else {
					if($info->connected == 1) {
							$this->app->error = "The player is online thus you can't kick him.";
					} else {
						if($info->crank >= $this->app->user->crank) {
							$this->app->error = "You can't kick a player with the same level as you or higher.";
						} else {
							try {
								$st = $this->app->coddb->prepare("UPDATE users SET clan = -1, crank = 0 WHERE id = ?");
								$st->bindParam(1, $id, PDO::PARAM_INT);
								if($st->execute()) {
									$this->app->success = "You have kicked " . $info->name . " from the clan!";
								} else {
									$this->app->error = "Something went wrong while trying to kick " . htmlentities($info->name) . " from the clan.";
								}
							} catch (Exception $e) {
								$this->app->error = $e->getMessage();
							}
						}
					}
				}
			} else {
				$this->app->error = "The player is not in your clan to kick.";
			}
		}
	}

	public function IsPlayerBanned($name) {
		try {
			$st = $this->app->coddb->prepare("SELECT * FROM banned WHERE name = ?");
			$st->bindParam(1, $name, PDO::PARAM_STR);
			$st->execute();
			if($st->rowCount() > 0)
				return true;
			else
				return false;
		} catch (Exception $e) {
			echo $e->getMessage();
		}
		return false;
	}

	public function CanChangeName($id)
	{
		$this->app->log->ConnectDB();

		$st = $this->app->log->db->prepare("SELECT * FROM logs WHERE uid = ? AND server = 'cod' ORDER BY id DESC limit 1");
		$st->bindParam(1, $id, PDO::PARAM_INT);
		$st->execute();

		if($st->rowCount() == 0)
			return true;

		$result = $st->fetch();

		$date1 = $result->date . " " . $result->time;
		$date2 = date("Y-m-d h:i:s");

		$timestamp1 = strtotime($date1);
		$timestamp2 = strtotime($date2);

		$hours = abs($timestamp2 - $timestamp1)/(60*60);

		if(round($hours) > 72) return true;

		return false;
	}

}

?>