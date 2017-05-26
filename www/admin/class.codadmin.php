<?php
/**
* 
*/
class codadmin
{

	private $app;
	
	function __construct($app)
	{
		$this->app = $app;
	}

	public function AKASearch($term) {
		try {
			$term = "%".$term."%";
			$st = $this->app->coddb->prepare("SELECT * FROM users WHERE name LIKE ? OR ip LIKE ?");
			$st->bindParam(1, $term, PDO::PARAM_STR);
			$st->bindParam(2, $term, PDO::PARAM_STR);
			$st->execute();
			if($st->rowCount() == 0)
				$this->app->error = "No results found.";
			$result = $st->fetchAll();
			return $result;
		} catch (Exception $e) {
			$this->app->error = $e->getMessage();
		}
	}

	public function UnbanPlayer($name) {
		try {
			$st = $this->app->coddb->prepare("SELECT * FROM banned WHERE name = ? LIMIT 1");
			$st->bindParam(1, $name, PDO::PARAM_STR);
			$st->execute();
			if($st->rowCount() > 0) {
				$result = $st->fetch();
				if($result->admin != $this->app->user->name && $this->app->user->admin == 2) {
					$this->app->error = "The player is not banned by you.";
				} else {
					$st = $this->app->coddb->prepare("DELETE FROM banned WHERE name = ?");
					$st->bindParam(1, $name, PDO::PARAM_STR);
					if($st->execute()) {
						$this->app->success = htmlentities($name) . " has been successfully unbanned.";
						$this->app->log->AddACPLog($this->app->user->id, $this->app->info->GetUserInfoByName($name)->id, "unban", "Ban reason: " . $result->reason);
					}
					else
						$this->app->error = "Something went wrong while trying to unban " . htmlentities($name);
				}
			} else {
				$this->app->error = "That username is not banned.";
			}
		} catch(Exception $e) {
			$this->app->error = $e->getMessage();
		}
	}

	public function GetAllUserInfo($name)
	{
		try {
			$st = $this->app->coddb->prepare("SELECT * FROM users WHERE name = ?");
			$st->bindParam(1, $name, PDO::PARAM_STR);
			$st->execute();
			if($st->rowCount() == 0)
				$this->app->error = "The username you enetered doesn't exist.";
			else {
				$result = $st->fetch();
				return $result;
			}
		} catch (Exception $e) {
			$this->app->error = $e->getMessage();
		}
	}

	public function SetName($oldname, $newname) {
		try {
			if($this->app->info->IsValidUserByName($oldname)) {
				if($this->app->info->IsValidUserByName($newname)) {
					$this->app->error = "The new username you entered already exists.";
				} else {
					if(strlen($newname) > 24) {
						$this->app->error = "The new username can't be longer than 24 letter.";
					} else {
						if(!$this->app->functions->IsValidName($newname)) {
							$this->app->error = "The username you entered contains invalid characters.";
						} else {
							if($this->app->info->GetUserInfoByName($oldname)->connected == 1) {
								$this->app->error = "You can't change the name of an online player.";
							} else {
								try {
									$st = $this->app->coddb->prepare("UPDATE users SET name = ? WHERE name = ? AND connected = 0");
									$st->bindParam(1, $newname, PDO::PARAM_STR);
									$st->bindParam(2, $oldname, PDO::PARAM_STR);
									$execute = $st->execute();
									if($execute) {
										$this->app->success = "You have changed the name of " . htmlentities($oldname) . " to " . htmlentities($newname) . ".";
										$this->app->log->AddACPLog($this->app->user->id, $this->app->info->GetUserInfoByName($newname)->id, "setname", $oldname . " to " . $newname);
									} else {
										$this->app->error = "Something wrong happened. Contact any manager with the information you used to get this solved as soon as possible.";
									}
								} catch (Exception $e) {
									$this->app->error = $e->getMessage();
								}
							}
						}
					}
				}
			} else {
				$this->app->error = "The username name you entered doesn't exist.";
			}
		} catch(Exception $e) {
			$this->app->error = $e->getMessage();
		}
	}

	public function SetPass($name, $password) {
		try {
			if($this->app->info->IsValidUserByName($name)) {
				if(strlen($password) > 30 || strlen($password) < 4) {
					$this->app->error = "The password must be between 4 and 30 character.";
				} else {
					if($this->app->info->GetUserInfoByName($name)->connected == 1) {
						$this->app->error = "You can't change the name of an online player.";
					} else {
						try {
							$password2 = $this->app->functions->EncryptPassword($password);
							$st = $this->app->coddb->prepare("UPDATE users SET password = ? WHERE name = ? AND connected = 0");
							$st->bindParam(1, $password2, PDO::PARAM_STR);
							$st->bindParam(2, $name, PDO::PARAM_STR);
							$execute = $st->execute();
							if($execute) {
								$this->app->success = "You have changed the password of " . htmlentities($name) . " to <b>" . htmlentities($password) . "</b>.";
								$this->app->log->AddACPLog($this->app->user->id, $this->app->info->GetUserInfoByName($name)->id, "setpass", "");
							} else {
								$this->app->error = "Something wrong happened. Contact any manager with the information you used to get this solved as soon as possible.";
							}
						} catch (Exception $e) {
							$this->app->error = $e->getMessage();
						}
					}
				}
			} else {
				$this->app->error = "The username name you entered doesn't exist.";
			}
		} catch(Exception $e) {
			$this->app->error = $e->getMessage();
		}
	}

	public function OfflineBan($name, $reason, $hours = 0) {
		if($this->app->info->IsValidUserByName($name)) {
			if($this->app->info->IsPlayerBanned($name) == false) {
				$info = $this->app->info->GetUserInfoByName($name);
				if($info->adminlevel > $this->app->user->admin) {
					$this->app->error = "You can't ban a higher admin level.";
				} else {
					try {
						$unban = 0;
						if($hours > 0) {
							$unban = ($hours*3600) + time();
						}
						$st = $this->app->coddb->prepare("INSERT INTO banned (name, ip, reason, admin, date, time, unban) VALUES (?, ?, ?, ?, CURDATE(), CURTIME(), ?)");
						$st->bindParam(1, $name, PDO::PARAM_STR);
						$st->bindParam(2, $info->ip, PDO::PARAM_STR);
						$st->bindParam(3, $reason, PDO::PARAM_STR);
						$st->bindParam(4, $this->app->user->name, PDO::PARAM_STR);
						$st->bindParam(5, $unban, PDO::PARAM_INT);
						if($st->execute()) {
							$this->app->success = htmlentities($name) . " has been banned successfully. Reason: " . $reason;
							if($unban == 0)
								$this->app->log->AddACPLog($this->app->user->id, $this->app->info->GetUserInfoByName($name)->id, "oban", $reason);
							else
								$this->app->log->AddACPLog($this->app->user->id, $this->app->info->GetUserInfoByName($name)->id, "otban", $reason);
						}
						else
							$this->app->error = "Something went wrong while trying to ban " . $name . ". Please contact any manager about this.";
					} catch (Exception $e) {
						$this->app->error = $e->getMessage();
					}
				}
			} else {
				$this->app->error = htmlentities($name) . " is already banned.";
			}
		} else {
			$this->app->error = "The username name you entered doesn't exist.";
		}
	}

	public function SetLevel($name, $level) {
		if($level > 7 || $level < 0) {
			$this->app->error = "The Admin level you entered is invalid.";
		} else {
			if($this->app->info->IsValidUserByName($name)) {
				if($this->app->info->GetUserInfoByName($name)->connected == 1) {
					$this->app->error = "The player is currently online.";
				} else {
					try {
						$olevel = $this->app->info->GetUserInfoByName($name)->adminlevel;
						$st = $this->app->coddb->prepare("UPDATE users SET adminlevel = ? WHERE name = ?");
						$st->bindParam(1, $level, PDO::PARAM_INT);
						$st->bindParam(2, $name, PDO::PARAM_STR);
						$st->execute();
						if($st->execute()) {
							$tag = "";
							switch ($level) {
								case 0:
									$tag = "<span style='color:gray'>None</span>";
									break;
								case 1:
									$tag = "<span style='color:purple;'>Moderator</span>";
									break;
								case 2:
									$tag = "<span style='color:pink;'>Junior Admin</span>";
									break;
								case 3:
									$tag = "<span style='color:green;'>Senior Admin</span>";
									break;
								case 4:
									$tag = "<span style='color:blue;'>Lead Admin</span>";
									break;
								case 5:
									$tag = "<span style='color:red;'>Server Manager</span>";
									break;
								case 6:
									$tag = "<span style='color:red;'>Community Manager</span>";
									break;
								case 7:
									$tag = "<span style='color:red;'>Community Owner</span>";
									break;
								
								default:
									break;
							}
							$this->app->success = "You have changed the Admin level of <b>" . htmlentities($name) . "</b> to " . $tag . " (" . $level . ").";
							$this->app->log->AddACPLog($this->app->user->id, $this->app->info->GetUserInfoByName($name)->id, "osetlevel", $olevel." to ".$level);
						} else {
							$this->app->error = "Something went wrong while trying to change the Admin level of " . htmlentities($name) . ". Please contact any manager about this error.";
						}
					} catch(Exception $e) {
						$this->app->error = $e->getMessage();
					}
				}
			} else {
				$this->app->error = "The username you entered doesn't exist.";
			}
		}
	}

	public function ManageNotes($content)
	{
		$this->app->log->ConnectDB();
		$st = $this->app->log->db->prepare("UPDATE home_msgs SET content = ? WHERE server = 'cod'");
		$st->bindParam(1, $content, PDO::PARAM_STR);
		$st->execute();
		$this->app->success = "You've edited the notes.";
	}

	public function GetNotes()
	{
		$this->app->log->ConnectDB();
		$st = $this->app->log->db->prepare("SELECT content FROM home_msgs WHERE server = 'cod'");
		$st->execute();
		return $st->fetch()->content;
	}

	public function SetVIP($name, $level) {
		if($level > 5 || $level < 0) {
			$this->app->error = "The VIP level you entered is invalid.";
		} else {
			if($this->app->info->IsValidUserByName($name)) {
				if($this->app->info->GetUserInfoByName($name)->connected == 1) {
					$this->app->error = "The player is currently online.";
				} else {
					try {
						$olevel = $this->app->info->GetUserInfoByName($name)->viplevel;
						$st = $this->app->coddb->prepare("UPDATE users SET viplevel = ? WHERE name = ?");
						$st->bindParam(1, $level, PDO::PARAM_INT);
						$st->bindParam(2, $name, PDO::PARAM_STR);
						$st->execute();
						if($st->execute()) {
							$tag = "";
							switch ($level) {
								case 0:
									$tag = "<span style='color:gray'>None</span>";
									break;
								case 1:
									$tag = "<span style='color:#CD7F32;'>Bronze</span>";
									break;
								case 2:
									$tag = "<span style='color:#C0C0C0;'>Silver</span>";
									break;
								case 3:
									$tag = "<span style='color:#FFDF00;'>Gold</span>";
									break;
								case 4:
									$tag = "<span style='color:#b9f2ff;'>Diamond</span>";
									break;
								case 5:
									$tag = "<span style='color: orange;text-shadow: 0px 0px 6px rgb(242, 198, 51);'>Titanium</span>";
									break;								
								default:
									break;
							}
							$this->app->success = "You have changed the VIP level of <b>" . htmlentities($name) . "</b> to " . $tag . " (" . $level . ").";
							$this->app->log->AddACPLog($this->app->user->id, $this->app->info->GetUserInfoByName($name)->id, "osetvip", $olevel." to ".$level);
						} else {
							$this->app->error = "Something went wrong while trying to change the VIP level of " . htmlentities($name) . ". Please contact any manager about this error.";
						}
					} catch(Exception $e) {
						$this->app->error = $e->getMessage();
					}
				}
			} else {
				$this->app->error = "The username you entered doesn't exist.";
			}
		}
	}

	public function SetStats($name, $score, $money, $kills, $deaths) {
		if($this->app->info->IsValidUserByName($name)) {
			$result = $this->app->info->GetUserInfoByName($name);
			if($result->connected == 0) {
				try {
					$st = $this->app->coddb->prepare("UPDATE users SET score = ?, money = ?, kills = ?, deaths = ? WHERE name = ? AND connected = 0");
					$st->bindParam(1, $score, PDO::PARAM_INT);
					$st->bindParam(2, $money, PDO::PARAM_INT);
					$st->bindParam(3, $kills, PDO::PARAM_INT);
					$st->bindParam(4, $deaths, PDO::PARAM_INT);
					$st->bindParam(5, $name, PDO::PARAM_STR);
					if($st->execute()) {
						$log = "(score: {$result->score}, money: {$result->money}, kills: {$result->kills}, deaths: {$result->deaths}) to: (score: {$score}, money: {$money}, kills: {$kills}, deaths: {$deaths})";
						$this->app->success = "You have changed the stats of " . htmlentities($name) . "<br/>The new stats are:<br/><b>Score</b>: " . $score . " | <b>Money</b>: " . $money . " | <b>Kills</b>: " . $kills . " | <b>Deaths</b>: " . $deaths;
						$this->app->log->AddACPLog($this->app->user->id, $this->app->info->GetUserInfoByName($name)->id, "osetstats", $log);
					} else {
						$this->app->error = "Something wrong happened. Contact any manager with the information you used to get this solved as soon as possible.";
					}
				} catch (Exception $e) {
					echo $e->getMessage();
				}
			} else {
				$this->app->error = "The player is currently online.";
			}
		} else {
			$this->app->error = "The username name you entered doesn't exist.";
		}
	}

	public function TakeActivitySnapShot() {
		try {
			$st = $this->app->coddb->prepare("DELETE FROM activity");
			$st->execute();

			$st = $this->app->coddb->prepare("INSERT INTO activity (id, activity) SELECT id, activity FROM users WHERE adminlevel > 0");
			$st->execute();
			$this->app->success = "A snapshot of the activity has been taken.";
			$this->app->log->AddACPLog($this->app->user->id, $this->app->user->id, "snapshot", "");
		} catch(Exception $e) {
			$this->app->error = $e->getMessage();
		}
	}

	public function GetActivity() {
		try {
			$st = $this->app->coddb->prepare("SELECT (u.activity - a.activity) as activity, u.name, u.id FROM users u LEFT JOIN activity a ON u.id = a.id WHERE u.adminlevel > 0 ORDER BY activity DESC");
			$st->execute();
			if($st->rowCount() == 0) {
				$this->app->error = "You didn't take a snapshot of the activity!";
				return null;
			} else {
				return $st->fetchAll();
			}
		} catch(Exception $e) {
			$this->app->error = $e->getMessage();
		}
	}

	public function GetLastSnapShot() {
		$st = $this->app->coddb->prepare("SELECT * FROM activity LIMIT 1");
		$st->execute();
		$result = $st->fetch();
		if($st->rowCount() > 0) {
			$date = date_create($result->start_date);
			return "The last snapshot was taken on " . date_format($date, "d/m/Y - H:i:s");
		} else {
			return "No snapshots has been taken.";
		}
	}

	public function GetPlayerRecords($name) {
		if($this->app->info->IsValidUserByName($name)) {
			try {
				$st = $this->app->coddb->prepare("SELECT r.admin, r.reason, r.date, r.action, a.name as aname FROM admin_record r LEFT JOIN users a ON r.admin = a.id WHERE r.player = ?");
				$st->bindParam(1, $this->app->info->GetUserInfoByName($name)->id, PDO::PARAM_INT);
				$st->execute();
				if($st->rowCount() == 0) {
					$this->app->error = "No records found on " . htmlentities($name);
					return null;
				} else {
					return $st->fetchAll();
				}
			} catch(Exception $e) {
				$this->app->error = $e->getMessage();
			}
		}
	}

	public function PlayerHasRecords($name)
	{
		if($this->app->info->IsValidUserByName($name)) {
			try {
				$st = $this->app->coddb->prepare("SELECT COUNT(*) as records FROM admin_record WHERE player = ?");
				$st->bindParam(1, $this->app->info->GetUserInfoByName($name)->id, PDO::PARAM_INT);
				$st->execute();
				return $st->fetch();
			} catch(Exception $e) {
				$this->app->error = $e->getMessage();
			}
		}
	}
	}
}
?>