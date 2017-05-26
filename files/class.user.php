<?php

class user
{

	private $app;

	public $admin = 0;

	public $LoggedIn = false;

	public $errors = array();
	
	function __construct($app)
	{
		$this->app = $app;

		if(isset($_COOKIE['svr']) == false) {
			$this->app->COD = false;
			$this->app->CNR = false;
			$this->app->GW = false;
			$this->app->BF = false;
			$this->LoggedIn = false;
			if(isset($_SESSION['uid'])) unset($_SESSION['uid']);
		}

		if(isset($_SESSION['uid'])) {
			if(isset($_GET['logout'])) {
				$this->LoggedIn = false;
				$this->app->CNR = false;
				$this->app->COD = false;
				$this->app->GW = false;
				$this->app->BF = false;
				session_destroy();
				unset($_COOKIE['svr']);
				setcookie("svr", null, time() - 3600, "/");
				$this->app->success = "You have logged out.";
				header('Location: /');
			} else {
				$this->LoggedIn = true;
				$this->uid = $_SESSION['uid'];
				$this->getDetails();
			}
		} else {
			$this->LoggedIn = false;
			$this->app->CNR = false;
			$this->app->COD = false;
			$this->app->GW = false;
			$this->app->BF = false;
			if(isset($_GET['login'])) {
				if($this->LoggedIn) {
					header('Location: /');
					return;
				}

				if(isset($_POST['username']) == false || empty($_POST['username'])) $this->app->error = "You must enter a username.";
				elseif(isset($_POST['password']) == false || empty($_POST['password'])) $this->app->error = "You must enter a password.";
				else {
					if(isset($_POST['server'])) {
						if($_POST['server'] == "cod")
						{
							$this->app->COD = true;
							$this->app->CNR = false;
							$this->app->GW = false;
							$this->app->BF = false;
							$this->LoginCOD($_POST['username'], $_POST['password']);
						}
						if($_POST['server'] == "cnr")
						{
							$this->app->CNR = true;
							$this->app->COD = false;
							$this->app->GW = false;
							$this->app->BF = false;
							$this->LoginCNR($_POST['username'], $_POST['password']);
						}
						if($_POST['server'] == "gw")
						{
							$this->app->GW = true;
							$this->app->COD = false;
							$this->app->CNR = false;
							$this->app->BF = false;
							$this->LoginGW($_POST['username'], $_POST['password']);
						}
						if($_POST['server'] == "bf")
						{
							$this->app->BF = true;
							$this->app->COD = false;
							$this->app->CNR = false;
							$this->app->GW = false;
							$this->LoginBF($_POST['username'], $_POST['password']);
						}
					}
				}
			}
		}
	}

	public function getDetails()
	{
		if($this->app->COD) {
			$st = $this->app->coddb->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
			$st->bindParam(1, $this->uid, PDO::PARAM_INT);
			$st->execute();
			$st->setFetchMode(PDO::FETCH_INTO, $this);
			$st->fetch();
			$this->admin = $this->adminlevel;
		} elseif($this->app->CNR) {
			$st = $this->app->cnrdb->prepare("SELECT * FROM players_data WHERE id = ? LIMIT 1");
			$st->bindParam(1, $this->uid, PDO::PARAM_INT);
			$st->execute();
			$st->setFetchMode(PDO::FETCH_INTO, $this);
			$st->fetch();
			$this->admin = $this->admin;
		} elseif($this->app->GW) {
			$st = $this->app->gwdb->prepare("SELECT *, Nick as name, UID as id FROM accounts WHERE uid = ? LIMIT 1");
			$st->bindParam(1, $this->uid, PDO::PARAM_INT);
			$st->execute();
			$st->setFetchMode(PDO::FETCH_INTO, $this);
			$st->fetch();
			$this->admin = $this->Admin;
			$this->id = $this->id;
		} elseif($this->app->BF) {
			$st = $this->app->bfdb->prepare("SELECT *, pName as name, pId as id FROM players WHERE pId = ? LIMIT 1");
			$st->bindParam(1, $this->uid, PDO::PARAM_INT);
			$st->execute();
			$st->setFetchMode(PDO::FETCH_INTO, $this);
			$st->fetch();
			$this->admin = $this->pAdmin;
			$this->operator = $this->pOp;
			$this->id = $this->id;
		}
	}

	private function LoginCOD($username, $password) {
		try {
			$password = $this->app->functions->EncryptPassword($password);
			$st = $this->app->coddb->prepare("SELECT * FROM users WHERE name = ? AND password = ?");
			$st->bindParam(1, $username, PDO::PARAM_STR);
			$st->bindParam(2, $password, PDO::PARAM_STR);
			$st->execute();
			$st->setFetchMode(PDO::FETCH_INTO, $this);
			$result = $st->fetch();
			if(!$result) {
				$this->app->error = "Username and password doesn't match.";
			} else {
				$this->LoggedIn = true;
				$_SESSION['uid'] = $result->id;
				$_SESSION['username'] = $result->name;
				$this->admin = $result->adminlevel;
				setcookie("remember", "1", 2147483647);
			}
			setcookie('svr', $this->app->config['hash']['cod']);
		} catch(Exception $e) {
			$this->app->error = $e->getMessage();
		}
	}

	private function LoginCNR($username, $password) {
		try {
			$st = $this->app->cnrdb->prepare("SELECT * FROM players_data WHERE name = ? AND password = ?");
			$st->bindParam(1, $username, PDO::PARAM_STR);
			$st->bindParam(2, $password, PDO::PARAM_STR);
			$st->execute();
			$st->setFetchMode(PDO::FETCH_INTO, $this);
			$result = $st->fetch();
			if(!$result) {
				$this->app->error = "Username and password doesn't match.";
			} else {
				$this->LoggedIn = true;
				$_SESSION['uid'] = $result->id;
				$_SESSION['username'] = $result->name;
				$this->admin = $result->admin;
				setcookie("remember", "1", 2147483647);
			}
			setcookie('svr', $this->app->config['hash']['cnr']);
		} catch(Exception $e) {
			$this->app->error = $e->getMessage();
		}
	}

	private function LoginGW($username, $password) {
		try {
			$password = $this->app->functions->EncryptPassword($password);
			$st = $this->app->gwdb->prepare("SELECT *, Nick as name, UID as id FROM accounts WHERE nick = ? AND password = ?");
			$st->bindParam(1, $username, PDO::PARAM_STR);
			$st->bindParam(2, $password, PDO::PARAM_STR);
			$st->execute();
			$st->setFetchMode(PDO::FETCH_INTO, $this);
			$result = $st->fetch();
			if(!$result) {
				$this->app->error = "Username and password doesn't match.";
			} else {
				$this->LoggedIn = true;
				$_SESSION['uid'] = $result->UID;
				$_SESSION['username'] = $result->Nick;
				$this->admin = $result->Admin;
				setcookie("remember", "1", 2147483647);
			}
			setcookie('svr', $this->app->config['hash']['gw']);
		} catch(Exception $e) {
			$this->app->error = $e->getMessage();
		}
	}
	
	private function LoginBF($username, $password) {
		try {
			$password = md5($password);
			$st = $this->app->bfdb->prepare("SELECT *,pId as id,pName as name,pOp as operator FROM players WHERE pName = ? AND pPass = ?");
			$st->bindParam(1, $username, PDO::PARAM_STR);
			$st->bindParam(2, $password, PDO::PARAM_STR);
			$st->execute();
			$st->setFetchMode(PDO::FETCH_INTO, $this);
			$result = $st->fetch();
			if(!$result) {
				$this->app->error = "Username and password doesn't match.";
			} else {
				$this->LoggedIn = true;
				$_SESSION['uid'] = $result->pId;
				$_SESSION['username'] = $result->pName;
				$this->admin = $result->pAdmin;
				$this->operator = $result->pOp;
				setcookie("remember", "1", 2147483647);
			}
			setcookie('svr', $this->app->config['hash']['bf']);
		} catch(Exception $e) {
			$this->app->error = $e->getMessage();
		}
	}
}
?>