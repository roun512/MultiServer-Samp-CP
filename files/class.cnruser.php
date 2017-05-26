<?php
/**
* @author roun512
*/
class cnruser
{
	
	private $app;

	function __construct($app)
	{
		$this->app = $app;
	}

	public function ChangePassword($oldpass, $newpass) {
		try {
			$st = $this->app->cnrdb->prepare("SELECT * FROM players_data WHERE id = ? AND password = ?");
			$st->bindParam(1, $this->app->user->id, PDO::PARAM_INT);
			$st->bindParam(2, $oldpass, PDO::PARAM_STR);
			$st->execute();
			$result = $st->fetch();
			if(!$result) {
				$this->app->error = "Your current password you entered is incorrect.";
			} else {
				if($result->online == -1) {
					if(strlen($newpass) < 4 || strlen($newpass) > 30) {
						$this->app->error = "Your new password must be between 4 and 30.";
					} else {
						$st = $this->app->cnrdb->prepare("UPDATE players_data SET password = ? WHERE id = ? AND password = ?");
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
			$st = $this->app->cnrdb->prepare("SELECT * FROM players_data WHERE id = ? AND password = ?");
			$st->bindParam(1, $this->app->user->id, PDO::PARAM_INT);
			$st->bindParam(2, $password, PDO::PARAM_STR);
			$st->execute();
			$result = $st->fetch();
			if(!$result) {
				$this->app->error = "Your current password you entered is incorrect.";
			} else {
				if($result->online == -1) {
					if($this->app->functions->IsValidName($username)) {
						$oldname = $result->name;
						$st = $this->app->cnrdb->prepare("SELECT * FROM players_data WHERE name = ?");
						$st->bindParam(1, $username, PDO::PARAM_STR);
						$st->execute();
						$result = $st->fetch();
						if($result) {
							$this->app->error = "The username you entered already exists.";
						} else {
							if(strpos($username, "[AG]") !== false) {
								if(strpos($oldname, "[AG]") === false) {
									$this->app->error = "You don't have an AG tag in your current name, so you may not add it yourself. In case you should have it or your application got accepted, please contact an administrator.";
									return;
								}
							}
							if($this->IsAllowedFactionTags($username)) {
								if($this->app->info->IsPlayerBanned($oldname)) {
									$this->app->error = "You can't change your name while being banned.";
								} elseif($this->IsPlayerBanned($username)) {
									$this->app->error = "The username you entered is banned.";
								} else {
									$st = $this->app->cnrdb->prepare("UPDATE players_data SET name = ? WHERE id = ?");
									$st->bindParam(1, $username, PDO::PARAM_STR);
									$st->bindParam(2, $this->app->user->id, PDO::PARAM_INT);
									if($st->execute()) {
										$this->app->success = "You have changed your username successfully.";
										$this->app->log->AddNameChangeLog($this->app->user->id, $_SERVER['REMOTE_ADDR'], $oldname, $username);
									} else {
										$this->app->error = "Something went wrong, please contact any Manager+ regarding this problem.";
									}
								}
							} else {
								$this->app->error = "You have entered a faction tag you're not in.";
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

	public function GetPlayerWeapons($uid) {
		try {
			$st = $this->app->cnrdb->prepare("SELECT * FROM weapons_data WHERE player_id = ?");
			$st->bindParam(1, $uid, PDO::PARAM_INT);
			$st->execute();
			$result = $st->fetchAll();
			if(!$result) {
				return 0;
			} else {
				return $result;
			}
		} catch(Exception $e) {
			die($e->getMessage());
		}
	}

	public function GetBannedUsers($search = null, $limit, $offset)
	{
		try {
			$query = "SELECT * FROM bans_data";
			if($search != null) {
				$search = '%'.$search.'%';
				$query .= " WHERE name LIKE :name OR admin LIKE :admin";
			}
			$query .= " ORDER BY id DESC LIMIT :limit OFFSET :offset";
			$st = $this->app->cnrdb->prepare($query);
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
			$query = "SELECT COUNT(*) as total FROM bans_data";
			if($search != null) {
				$search = '%'.$search.'%';
				$query .= " WHERE name LIKE ? OR admin LIKE ?";
			}
			$st = $this->app->cnrdb->prepare($query);
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
	
	public function GetClassName($id) {
		$class = null;
		switch ($id) {
			case 1:
				$class = "Police";
				break;
			case 2:
				$class = "Civilian";
				break;
			case 3:
				$class = "FBI";
				break;
			case 4:
				$class = "SWAT";
				break;
			case 5:
				$class = "Army";
				break;
			case 6:
				$class = "Terrorist";
				break;
			default:
				$class = null;
				break;
		}
		return $class;
	}

	public function GetTopScore() {
		try {
			$st = $this->app->cnrdb->prepare("SELECT id, name, crskills + cskills as ascore FROM players_data ORDER BY crskills + cskills DESC LIMIT 10");
			$st->execute();
			$result = $st->fetchAll();
			return $result;
		} catch(Exception $e) {
			$this->app->error = $e->getMessage();
		}
	}

	public function GetTopMoney() {
		try {
			$st = $this->app->cnrdb->prepare("SELECT id, name, money + moneybank as amoney FROM players_data ORDER BY money + moneybank DESC LIMIT 10");
			$st->execute();
			$result = $st->fetchAll();
			return $result;
		} catch(Exception $e) {
			$this->app->error = $e->getMessage();
		}
	}

	public function GetTopRobberies() {
		try {
			$st = $this->app->cnrdb->prepare("SELECT id, name, robberies FROM players_data ORDER BY robberies DESC LIMIT 10");
			$st->execute();
			$result = $st->fetchAll();
			return $result;
		} catch(Exception $e) {
			$this->app->error = $e->getMessage();
		}
	}

	public function GetTopArrests() {
		try {
			$st = $this->app->cnrdb->prepare("SELECT id, name, arrests FROM players_data ORDER BY arrests DESC LIMIT 10");
			$st->execute();
			$result = $st->fetchAll();
			return $result;
		} catch(Exception $e) {
			$this->app->error = $e->getMessage();
		}
	}

	public function GetTopArrested() {
		try {
			$st = $this->app->cnrdb->prepare("SELECT id, name, arrested FROM players_data ORDER BY arrested DESC LIMIT 10");
			$st->execute();
			$result = $st->fetchAll();
			return $result;
		} catch(Exception $e) {
			$this->app->error = $e->getMessage();
		}
	}

	public function GetFactionMembers($fid) {
		try {
			$st = $this->app->cnrdb->prepare("SELECT id, name, factionrank, online, laston FROM players_data WHERE faction = ? ORDER BY factionrank DESC");
			$st->bindParam(1, $fid, PDO::PARAM_INT);
			$st->execute();
			$result = $st->fetchAll();
			return $result;
		} catch(Exception $e) {
			$this->app->error = $e->getMessage();
		}
	}

	public function IsValidFaction($id) 
	{
		try {
			$st = $this->app->cnrdb->prepare("SELECT * FROM factions_data WHERE id = ?");
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

	public function IsValidUserByName($id) 
	{
		try {
			$st = $this->app->cnrdb->prepare("SELECT * FROM players_data WHERE name = ?");
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

	public function IsValidUserByID($id) 
	{
		try {
			$st = $this->app->cnrdb->prepare("SELECT * FROM players_data WHERE id = ?");
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

	public function GetFactionInfoByID($fid) {
		try {
			$st = $this->app->cnrdb->prepare("SELECT * FROM factions_data WHERE id = ?");
			$st->bindParam(1, $fid, PDO::PARAM_INT);
			$st->execute();
			$result = $st->fetch();
			if($st->rowCount() > 0)
				if($result) return $result;
		} catch(Exception $e) {
			$this->app->error = $e->getMessage();
		}
	}

	public function GetFactionInfoByName($fid) {
		try {
			$st = $this->app->cnrdb->prepare("SELECT * FROM factions_data WHERE name = ?");
			$st->bindParam(1, $fid, PDO::PARAM_STR);
			$st->execute();
			$result = $st->fetch();
			if($st->rowCount() > 0)
				if($result) return $result;
		} catch(Exception $e) {
			$this->app->error = $e->getMessage();
		}
	}

	public function GetUserInfoByID($id) {
		try {
			$st = $this->app->cnrdb->prepare("SELECT * FROM players_data WHERE id = ?");
			$st->bindParam(1, $id, PDO::PARAM_INT);
			$st->execute();
			$result = $st->fetch();
			if($result) return $result;
			else return 0;
		} catch(Exception $e) {
			$this->app->error = $e->getMessage();
		}
	}

	public function GetUserInfoByName($id) {
		try {
			$st = $this->app->cnrdb->prepare("SELECT * FROM players_data WHERE name = ?");
			$st->bindParam(1, $id, PDO::PARAM_STR);
			$st->execute();
			$result = $st->fetch();
			if($result) return $result;
			else return 0;
		} catch(Exception $e) {
			$this->app->error = $e->getMessage();
		}
	}

	public function KickFromFaction($id) {
		$info = $this->GetUserInfoByID($id);
		if($id == $this->app->user->id) {
			$this->app->error = "You can't kick yourself from the faction.";
		} else {
			if($info->faction == $this->app->user->faction) {
				if($this->GetFactionInfoByID($this->app->user->faction)->owner == $id) {
					$this->app->error = "You can't kick the owner of the faction!";
				} else {
					if($info->online != -1) {
							$this->app->error = "The player is online thus you can't kick him.";
					} else {
						if($info->factionrank >= $this->app->user->factionrank) {
							$this->app->error = "You can't kick a player with same level or higher.";
						} else {
							try {
								$st = $this->app->cnrdb->prepare("UPDATE players_data SET faction = 0, factionrank = 0 WHERE id = ?; UPDATE factions_data SET members = members - 1 WHERE id = ?");
								$st->bindParam(1, $id, PDO::PARAM_INT);
								$st->bindParam(2, $this->app->user->faction, PDO::PARAM_INT);
								if($st->execute()) {
									$this->app->success = "You have kicked " . $info->name . " from the faction!";
								} else {
									$this->app->error = "Something went wrong while trying to kick " . htmlentities($info->name) . " from the faction.";
								}
							} catch (Exception $e) {
								$this->app->error = $e->getMessage();
							}
						}
					}
				}
			} else {
				$this->app->error = "The player is not in your faction to kick.";
			}
		}
	}

	public function ChangeFactionLevel($id, $level)
	{
		$info = $this->GetUserInfoByID($id);
		$finfo = $this->GetFactionInfoByID($this->app->user->faction);
		if($level < 0) {
			$this->app->error = "The level must be at least 0.";
		} elseif($level > $finfo->maxlevel) {
			$this->app->error = "The level you entered is more than the max level allowed.";
		} else {
			if($info->faction != $this->app->user->faction) {
				$this->app->error = "The player is not in your faction.";
			} else {
				if($id == $finfo->owner) {
					$this->app->error = "You can't change the level of the owner.";
				} else {
					if($this->app->user->factionrank < $finfo->levelperm) {
						$this->app->error = "You don't have permissions to change the level of a user.";
					} else {
						if($info->online != -1) {
							$this->app->error = "The player is online thus you can't change his level.";
						} else {
							if($info->factionrank >= $this->app->user->factionrank) {
								$this->app->error = "You can't change the level of a member that has the same level as you or higher.";
							} else {
								try {
									$st = $this->app->cnrdb->prepare("UPDATE players_data SET factionrank = ? WHERE id = ? AND online = -1");
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

	public function LeaveFaction()
	{
		if($this->IsValidFaction($this->app->user->faction) == false) {
			$this->app->error = "You are not in any faction to leave it.";
		} else {
			$finfo = $this->GetFactionInfoByID($this->app->user->faction);
			if($finfo->owner == $this->app->user->id) {
				$this->app->error = "You can't leave the faction because you are the owner.";
			} else {
				if($this->app->user->online != -1) {
					$this->app->error = "You are currently online in-game thus you can't leave the faction.";
				} else {
					try {
						$st = $this->app->cnrdb->prepare("UPDATE players_data SET faction = -1, factionrank = 0 WHERE id = ?; UPDATE factions_data SET members = members - 1 WHERE id = ?");
						$st->bindParam(1, $this->app->user->id, PDO::PARAM_INT);
						$st->bindParam(2, $this->app->user->faction, PDO::PARAM_INT);
						if($st->execute()) {
							header('Location: /');
							$this->app->success = "You have left the faction.";
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

	public function GetFactionName($fid) {
		if($fid == 0) return "Not in any faction.";
		try {
			$st = $this->app->cnrdb->prepare("SELECT name FROM factions_data WHERE id = ?");
			$st->bindParam(1, $fid, PDO::PARAM_INT);
			$st->execute();
			$result = $st->fetch();
			if($result) return $result->name;
			else return "Not in any faction.";
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function GetVIPs() {
		try {
			$st = $this->app->cnrdb->prepare("SELECT name, vip as rank FROM players_data WHERE vip > 0 ORDER BY vip DESC");
			$st->execute();
			$result = $st->fetchAll();
			return $result;
		} catch(Exception $e) {
			$this->app->error = $e->getMessage();
		}
	}

	public function GetAdmins() {
		try {
			$st = $this->app->cnrdb->prepare("SELECT name, admin as rank FROM players_data WHERE admin > 0 ORDER BY admin DESC");
			$st->execute();
			$result = $st->fetchAll();
			return $result;
		} catch(Exception $e) {
			$this->app->error = $e->getMessage();
		}
	}

	public function GetFactionsList() {
		try {
			$st = $this->app->cnrdb->prepare("SELECT * FROM factions_data");
			$st->execute();
			$result = $st->fetchAll();
			return $result;
		} catch(Exception $e)
		{
			$this->app->error = $e->getMessage();
		}
	}

	public function IsAllowedFactionTags($name) {
		preg_match_all("/\[([\w]+)\]/i", $name, $tags, PREG_SET_ORDER);
		$st = $this->app->cnrdb->prepare("SELECT tag FROM factions_data WHERE id != (SELECT faction FROM players_data WHERE id = ?)");
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

	public function GenerateUserLink($name) {
		return "<a href='/index.php?name=".$name."'>".htmlentities($name)."</a>";
	}

	public function GenerateFactionLink($name) {
		return "<a href='/faction/index.php?id=".$this->GetFactionInfoByName($name)->id."'>".htmlentities($name)."</a>";
	}

	public function IsPlayerBanned($name) {
		try {
			$st = $this->app->cnrdb->prepare("SELECT * FROM bans_data WHERE name = ?");
			$st->bindParam(1, $name, PDO::PARAM_STR);
			$st->execute();
			if($st->rowCount() > 0)
				return true;
			else
				return false;
		} catch (Exception $e) {
			$this->app->error = $e->getMessage();
		}
		return false;
	}
}
?>