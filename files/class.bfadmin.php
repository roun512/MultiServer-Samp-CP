<?php
/**
* 
*/
class bfadmin
{

	private $app;
	
	function __construct($app)
	{
		$this->app = $app;
	}

	public function AKASearch($term) {
		try {
			$term = "%".$term."%";
			$st = $this->app->bfdb->prepare("SELECT pName as name, pLastIP as ip, pId as id FROM players WHERE pName LIKE ? OR pLastIP LIKE ?");
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
			$st = $this->app->bfdb->prepare("SELECT * FROM tblbans WHERE nick = ? LIMIT 1");
			$st->bindParam(1, $name, PDO::PARAM_STR);
			$st->execute();
			if($st->rowCount() > 0) {
				$result = $st->fetch();
				if($result->by != $this->app->user->name && $this->app->user->admin == 2) {
					$this->app->error = "The player is not banned by you.";
				} else {
					$st = $this->app->bfdb->prepare("DELETE FROM tblbans WHERE nick = ?");
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
									$st = $this->app->bfdb->prepare("UPDATE players SET pName = ? WHERE pName = ? AND pOnline = 0");
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
							// $password2 = $this->app->functions->EncryptPassword($password);
							$password2 = md5($password);
							$st = $this->app->bfdb->prepare("UPDATE players SET pPass = ? WHERE nick = ? AND pOnline = 0");
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
						/* 
						try {
							$this->app->error = "Password changing is unavailable for now. Please contact Jarek for it to be done.";
						} */
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
				if($info->pAdmin > $this->app->user->pAdmin) {
					$this->app->error = "You can't ban a higher admin level.";
				} else {
					try {
						$unban = 0;
						$temp = 0;
						if($hours > 0) {
							$temp = 1;
							$unban = ($hours*3600) + time();
						}
						/* $st = $this->app->bfdb->prepare("INSERT INTO bans (nick, userip, reason, admin, date, temp, unban) VALUES (?, ?, ?, ?, CURDATE(), ?, ?)");
						$st->bindParam(1, $name, PDO::PARAM_STR);
						$st->bindParam(2, $info->IP, PDO::PARAM_STR);
						$st->bindParam(3, $reason, PDO::PARAM_STR);
						$st->bindParam(4, $this->app->user->name, PDO::PARAM_STR);
						$st->bindParam(5, $temp, PDO::PARAM_INT);
						$st->bindParam(6, $unban, PDO::PARAM_INT); */
						$st = $this->app->bfdb->prepare("INSERT INTO tblbans (nick, ip, reason, time, `by`, unban, server, exception) VALUES (?, ?, ?, CURDATE(), ?, ?, 2, 0)");
						$st->bindParam(1, $name, PDO::PARAM_STR);
						$st->bindParam(2, $info->IP, PDO::PARAM_STR);
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
		if($level > 7 || $level < -1) {
			$this->app->error = "The Admin level you entered is invalid.";
		} else {
			if($this->app->info->IsValidUserByName($name)) {
				if($this->app->info->GetUserInfoByName($name)->connected == 1) {
					$this->app->error = "The player is currently online.";
				} else {
					try {
						$olevel = $this->app->info->GetUserInfoByName($name)->pAdmin;
						if ($level == -1) {
							$st = $this->app->bfdb->prepare("UPDATE players SET pAdmin = ? WHERE pName = ?");
							$st->bindParam(1, $name, PDO::PARAM_STR);
						}
						else if ($level ==0) {
							$st = $this->app->bfdb->prepare("UPDATE players SET pAdmin = 0, pOp = 1 WHERE pName = ?");
							$st->bindParam(1, $name, PDO::PARAM_STR);
						}
						else {
							$st = $this->app->bfdb->prepare("UPDATE players SET pAdmin = ?, pOp = 0 WHERE pName = ?");
							$st->bindParam(1, $level, PDO::PARAM_INT);
							$st->bindParam(2, $name, PDO::PARAM_STR);
						}
						if($st->execute()) {
							$tag = "";
							switch ($level) {
								case -1:
									$tag = "<span style='color:gray'>None</span>";
									break;
								case 0:
									$tag = "<span style='color:blue'>Help Operator</span>";
									break;
								case 1:
									$tag = "<span style='color:green;'>Trial Admin</span>";
									break;
								case 2:
									$tag = "<span style='color:green;'>Server Admin</span>";
									break;
								case 3:
									$tag = "<span style='color:green;'>Confirmed Admin</span>";
									break;
								case 4:
									$tag = "<span style='color:green;'>Senior Admin</span>";
									break;
								case 5:
									$tag = "<span style='color:red;'>Head Admin</span>";
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
		$st = $this->app->log->db->prepare("UPDATE home_msgs SET content = ? WHERE server = 'bf'");
		$st->bindParam(1, $content, PDO::PARAM_STR);
		$st->execute();
		$this->app->success = "You've edited the notes.";
	}

	public function GetNotes()
	{
		$this->app->log->ConnectDB();
		$st = $this->app->log->db->prepare("SELECT content FROM home_msgs WHERE server = 'bf'");
		$st->execute();
		return $st->fetch()->content;
	}

	public function SetVIP($name, $level, $months = 0) {
		if($level > 5 || $level < 0) {
			$this->app->error = "The VIP level you entered is invalid.";
		} else {
			if($this->app->info->IsValidUserByName($name)) {
				if($this->app->info->GetUserInfoByName($name)->connected == 1) {
					$this->app->error = "The player is currently online.";
				} else {
					try {
						$olevel = $this->app->info->GetUserInfoByName($name)->pDonor;
						$st = $this->app->bfdb->prepare("UPDATE players SET pDonor = ? WHERE pName = ?");
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
					$st = $this->app->bfdb->prepare("UPDATE players SET pScore = ?, pMoney = ?, pKills = ?, pDeaths = ? WHERE pName = ? AND pOnline = 0");
					$st->bindParam(1, $score, PDO::PARAM_INT);
					$st->bindParam(2, $money, PDO::PARAM_INT);
					$st->bindParam(3, $kills, PDO::PARAM_INT);
					$st->bindParam(4, $deaths, PDO::PARAM_INT);
					$st->bindParam(5, $name, PDO::PARAM_STR);
					if($st->execute()) {
						$log = "(score: {$result->Score}, money: {$result->Money}, kills: {$result->Kills}, deaths: {$result->Deaths}) to: (score: {$score}, money: {$money}, kills: {$kills}, deaths: {$deaths})";
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
}
?>