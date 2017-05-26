<?php
/**
* @author roun512
*/
class gwuser
{
	
	private $app;

	function __construct($app)
	{
		$this->app = $app;
	}

	public function ChangePassword($oldpass, $newpass) {
		try {
			$st = $this->app->gwdb->prepare("SELECT * FROM accounts WHERE uid = ? AND password = ?");
			$st->bindParam(1, $this->app->user->uid, PDO::PARAM_INT);
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
						$st = $this->app->gwdb->prepare("UPDATE accounts SET password = ? WHERE uid = ? AND password = ?");
						$st->bindParam(1, $newpass, PDO::PARAM_STR);
						$st->bindParam(2, $this->app->user->uid, PDO::PARAM_INT);
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
			$st = $this->app->gwdb->prepare("SELECT * FROM accounts WHERE uid = ? AND password = ?");
			$st->bindParam(1, $this->app->user->uid, PDO::PARAM_INT);
			$st->bindParam(2, $password, PDO::PARAM_STR);
			$st->execute();
			$result = $st->fetch();
			if(!$result) {
				$this->app->error = "Your current password you entered is incorrect.";
			} else {
				if($result->connected == 0) {
					if($this->app->functions->IsValidName($username)) {
						$oldname = $result->Nick;
						$st = $this->app->gwdb->prepare("SELECT * FROM accounts WHERE nick = ?");
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
							if($this->IsPlayerBanned($oldname)) {
								$this->app->error = "You can't change your name while being banned.";
							} elseif($this->IsPlayerBanned($username)) {
								$this->app->error = "The username you entered is banned.";
							} else {
								$st = $this->app->gwdb->prepare("UPDATE accounts SET nick = ? WHERE uid = ?");
								$st->bindParam(1, $username, PDO::PARAM_STR);
								$st->bindParam(2, $this->app->user->uid, PDO::PARAM_INT);
								if($st->execute()) {
									$this->app->success = "You have changed your username successfully.";
									$this->app->log->AddNameChangeLog($this->app->user->uid, $_SERVER['REMOTE_ADDR'], $oldname, $username);
								} else {
									$this->app->error = "Something went wrong, please contact any Manager+ regarding this problem.";
								}
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

	/*public function IsValidClan($id)
	{
		try {
			$st = $this->app->gwdb->prepare("SELECT * FROM clans WHERE id = ?");
			$st->bindParam(1, $id, PDO::PARAM_INT);
			$st->execute();
			if($st->rowCount() > 0)
				return true;
			else
				return false;
		} catch(Exception $e) {
			$this->app->error = $e->getMessage();
		}
	}*/

	public function IsValidUserByID($id) 
	{
		try {
			$st = $this->app->gwdb->prepare("SELECT * FROM accounts WHERE uid = ?");
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
			$st = $this->app->gwdb->prepare("SELECT *, Nick as name, UID as id FROM accounts WHERE uid = ?");
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
			$st = $this->app->gwdb->prepare("SELECT * FROM accounts WHERE nick = ?");
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
			$st = $this->app->gwdb->prepare("SELECT *, Nick as name, UID as id FROM accounts WHERE nick = ?");
			$st->bindParam(1, $id, PDO::PARAM_STR);
			$st->execute();
			$result = $st->fetch();
			return $result;
		} catch(Exception $e) {
			$this->app->error = $e->getMessage();
		}
	}

	/*public function GetClanInfoByName($id) {
		try {
			$st = $this->app->gwdb->prepare("SELECT *, ROUND((score*SQRT(money)*(kills/deaths))/10000) as points FROM clans WHERE nick = ?");
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
			$st = $this->app->gwdb->prepare("SELECT *, ROUND((score*SQRT(money)*(kills/deaths))/10000) as points FROM clans WHERE id = ?");
			$st->bindParam(1, $id, PDO::PARAM_INT);
			$st->execute();
			$result = $st->fetch();
			return $result;
		} catch(Exception $e) {
			$this->app->error = $e->getMessage();
		}
	}*/

	public function GetTopScore() {
		try {
			$st = $this->app->gwdb->prepare("SELECT UID, Nick as name, Score as score FROM accounts ORDER BY score DESC LIMIT 10");
			$st->execute();
			$result = $st->fetchAll();
			return $result;
		} catch(Exception $e) {
			$this->app->error = $e->getMessage();
		}
	}

	public function GetTopMoney() {
		try {
			$st = $this->app->gwdb->prepare("SELECT UID, nick as name, Money as money FROM accounts ORDER BY money DESC LIMIT 10");
			$st->execute();
			$result = $st->fetchAll();
			return $result;
		} catch(Exception $e) {
			$this->app->error = $e->getMessage();
		}
	}

	public function GetTopKills() {
		try {
			$st = $this->app->gwdb->prepare("SELECT UID, nick as name, Kills as kills FROM accounts ORDER BY kills DESC LIMIT 10");
			$st->execute();
			$result = $st->fetchAll();
			return $result;
		} catch(Exception $e) {
			$this->app->error = $e->getMessage();
		}
	}

	public function GetTopDeaths() {
		try {
			$st = $this->app->gwdb->prepare("SELECT UID, nick as name, Deaths as deaths FROM accounts ORDER BY deaths DESC LIMIT 10");
			$st->execute();
			$result = $st->fetchAll();
			return $result;
		} catch(Exception $e) {
			$this->app->error = $e->getMessage();
		}
	}

	public function GetTopZoneCaptures() {
		try {
			$st = $this->app->gwdb->prepare("SELECT UID, Nick as name, ZoneCaps as zones FROM accounts ORDER BY zones DESC LIMIT 10");
			$st->execute();
			$result = $st->fetchAll();
			return $result;
		} catch(Exception $e) {
			$this->app->error = $e->getMessage();
		}
	}

	public function GetTopTurfsCaptures() {
		try {
			$st = $this->app->gwdb->prepare("SELECT UID, Nick as name, TurfCaps as turfs FROM accounts ORDER BY turfs DESC LIMIT 10");
			$st->execute();
			$result = $st->fetchAll();
			return $result;
		} catch(Exception $e) {
			$this->app->error = $e->getMessage();
		}
	}

	public function GetTopActivity() {
		try {
			$st = $this->app->gwdb->prepare("SELECT UID, Nick as name, Hours as hours FROM accounts ORDER BY hours DESC LIMIT 10");
			$st->execute();
			$result = $st->fetchAll();
			return $result;
		} catch(Exception $e) {
			$this->app->error = $e->getMessage();
		}
	}

	public function GetPlayerHouse($id) {
		try {
			$st = $this->app->gwdb->prepare("SELECT * FROM iHouse_Houses WHERE ownerid = ?");
			$st->bindParam(1, $id, PDO::PARAM_INT);
			$st->execute();
			$result = $st->fetch();
			return $result;
		} catch(Exception $e) {
			$this->app->error = $e->getMessage();
		}
	}

	public function PlayerHasHouse($id)
	{
		try {
			$st = $this->app->gwdb->prepare("SELECT COUNT(*) as house FROM iHouse_Houses WHERE ownerid = ?");
			$st->bindParam(1, $id, PDO::PARAM_INT);
			$st->execute();
			$result = $st->fetch();
			if($result->house > 0)
				return true;
			else 
				return false;
		} catch(Exception $e) {
			$this->app->error = $e->getMessage();
		}
	}

	public function GetVIPs() {
		try {
			$st = $this->app->gwdb->prepare("SELECT nick as name, donor as rank FROM accounts WHERE donor > 0 ORDER BY donor DESC");
			$st->execute();
			$result = $st->fetchAll();
			return $result;
		} catch(Exception $e) {
			echo $e->getMessage();
		}
	}

	public function GetAdmins() {
		try {
			$st = $this->app->gwdb->prepare("SELECT nick as name, admin as rank FROM accounts WHERE admin > 0 ORDER BY admin DESC");
			$st->execute();
			$result = $st->fetchAll();
			return $result;
		} catch(Exception $e) {
			$this->app->error = $e->getMessage();
		}
	}

	public function GetFactionsList() {
		try {
			$st = $this->app->gwdb->prepare("SELECT * FROM factions");
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
			$query = "SELECT *, nick as name, userip as ip FROM bans";
			if($search != null) {
				$search = '%'.$search.'%';
				$query .= " WHERE nick LIKE :name OR admin LIKE :admin";
			}
			$query .= " ORDER BY id DESC LIMIT :limit OFFSET :offset";
			$st = $this->app->gwdb->prepare($query);
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
			$query = "SELECT COUNT(*) as total FROM bans";
			if($search != null) {
				$search = '%'.$search.'%';
				$query .= " WHERE nick LIKE ? OR admin LIKE ?";
			}
			$st = $this->app->gwdb->prepare($query);
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

	/*public function GenerateClanLink($name) {
		return "<a href='/clan/index.php?id=".$this->GetClanInfoByName($name)->uid."'>".htmlentities($name)."</a>";
	}

	public function IsAllowedClanTags($name) {
		preg_match_all("/\[([\w]+)\]/i", $name, $tags, PREG_SET_ORDER);
		$st = $this->app->gwdb->prepare("SELECT tag FROM clans WHERE id != (SELECT clan FROM accounts WHERE id = ?)");
		$st->bindParam(1, $this->app->user->uid, PDO::PARAM_INT);
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
		$st = $this->app->gwdb->prepare("SELECT * FROM accounts WHERE clan = ? ORDER BY crank DESC");
		$st->bindParam(1, $id, PDO::PARAM_INT);
		$st->execute();
		return $st->fetchAll();
	}

	public function LeaveClan() {
		if($this->IsValidClan($this->app->user->clan) == false) {
			$this->app->error = "You are not in any clan to leave it.";
		} else {
			$info = $this->GetClanInfoByID($this->app->user->clan);
			if($info->owner == $this->app->user->uid) {
				$this->app->error = "You can't leave the clan because you are the owner.";
			} else {
				if($this->app->user->connected == 1) {
					$this->app->error = "You are currently online in-game thus you can't leave the clan.";
				} else {
					try {
						$st = $this->app->gwdb->prepare("UPDATE users SET clan = -1, crank = 0 WHERE id = ?");
						$st->bindParam(1, $this->app->user->uid, PDO::PARAM_INT);
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
									$st = $this->app->gwdb->prepare("UPDATE users SET crank = ? WHERE id = ?");
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
		if($id == $this->app->user->uid) {
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
								$st = $this->app->gwdb->prepare("UPDATE users SET clan = -1, crank = 0 WHERE id = ?");
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
	}*/

	public function IsPlayerBanned($name) {
		try {
			$st = $this->app->gwdb->prepare("SELECT * FROM bans WHERE nick = ?");
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

}

?>