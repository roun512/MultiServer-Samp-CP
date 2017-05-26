<?php
	class app {


		public $COD = false, $CNR = false, $GW = false, $BF = false;

		public $error = null, $success = null;

		function __construct() {
			require_once 'config.php';
			if(!isset($config) || !is_array($config))
				throw new Exception("Couldn\'t load the Config file.");

			$this->config = $config;

			if(isset($_COOKIE['svr'])) {
				if($_COOKIE['svr'] == $this->config['hash']['cod']) $this->COD = true;
				elseif($_COOKIE['svr'] == $this->config['hash']['cnr']) $this->CNR = true;
				elseif($_COOKIE['svr'] == $this->config['hash']['gw']) $this->GW = true;
				elseif($_COOKIE['svr'] == $this->config['hash']['bf']) $this->BF = true;
			}
			
			$this->CNRconnectDB($this->config['db']);
			$this->CODconnectDB($this->config['db']);
			$this->GWconnectDB($this->config['db']);
			$this->BFconnectDB($this->config['db']);

			$this->functions = new functions($this);

			$this->user = new user($this);

			$this->log = new log($this);
			
			if($this->COD) $this->info = new coduser($this);
			elseif($this->CNR) $this->info = new cnruser($this);
			elseif($this->GW) $this->info = new gwuser($this);
			elseif($this->BF) $this->info = new bfuser($this);


			if($this->COD) $this->admin = new codadmin($this);
			elseif($this->CNR) $this->admin = new cnradmin($this);
			elseif($this->GW) $this->admin = new gwadmin($this);
			elseif($this->BF) $this->admin = new bfadmin($this);
			
			$this->page = new page();




		}

		protected function CODconnectDB($config) {
			try {
				$dsn = "mysql:host=127.0.0.1;dbname=" . $config['cod_database'];
				$this->coddb = new PDO($dsn, $config['username'], $config['password']);

				$this->coddb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

				$this->coddb->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
                $this->coddb->setAttribute(PDO::MYSQL_ATTR_FOUND_ROWS, true);
			} catch(Exception $e) {
				$this->error = $e->getMessage();
			}
		}

		protected function CNRconnectDB($config) {
			try {
				$dsn = "mysql:host=127.0.0.1;dbname=" . $config['cnr_database'];
				$this->cnrdb = new PDO($dsn, $config['username'], $config['password']);

				$this->cnrdb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

				$this->cnrdb->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
                $this->cnrdb->setAttribute(PDO::MYSQL_ATTR_FOUND_ROWS, true);
			} catch(Exception $e) {
				$this->error = $e->getMessage();
			}
		}

		protected function GWconnectDB($config) {
			try {
				$dsn = "mysql:host=127.0.0.1;dbname=" . $config['gw_database'];
				$this->gwdb = new PDO($dsn, $config['username'], $config['password']);

				$this->gwdb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

				$this->gwdb->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
                $this->gwdb->setAttribute(PDO::MYSQL_ATTR_FOUND_ROWS, true);
			} catch(Exception $e) {
				$this->error = $e->getMessage();
			}
		}
		
		protected function BFconnectDB($config) {
			try {
				$dsn = "mysql:host=127.0.0.1;dbname=" . $config['bf_database'];
				$this->bfdb = new PDO($dsn, $config['username'], $config['password']);

				$this->bfdb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

				$this->bfdb->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
                $this->bfdb->setAttribute(PDO::MYSQL_ATTR_FOUND_ROWS, true);
			} catch(Exception $e) {
				$this->error = $e->getMessage();
			}
		}
	}
?>