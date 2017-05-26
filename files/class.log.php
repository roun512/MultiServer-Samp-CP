<?php

/**
* @author roun512
*/

class log {

	private $app;

	public $limit;

	function __construct($app) {
		$this->app = $app;
		$this->limit = $this->app->config['logs_limit'];
	}

	public function ConnectDB() {
		try {
			$config = $this->app->config['db'];
			$dsn = "mysql:host=127.0.0.1;dbname=" . $config['web_database'];
			$this->db = new PDO($dsn, $config['username'], $config['password']);

			$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			$this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
	        $this->db->setAttribute(PDO::MYSQL_ATTR_FOUND_ROWS, true);
		} catch(Exception $e) {
			$this->app->error = $e->getMessage();
		}
	}

	public function GetAdminLogs($page = 1, $offset)
	{
		try {
			$this->ConnectDB();

			if($this->app->COD)
				$st = $this->db->prepare("SELECT * FROM acp_logs WHERE server = 'cod' OR server = 'all' ORDER BY id DESC LIMIT :limit OFFSET :offset");
			elseif($this->app->CNR)
				$st = $this->db->prepare("SELECT * FROM acp_logs WHERE server = 'cnr' OR server = 'all' ORDER BY id DESC LIMIT :limit OFFSET :offset");
			elseif($this->app->GW)
				$st = $this->db->prepare("SELECT * FROM acp_logs WHERE server = 'gw' OR server = 'all' ORDER BY id DESC LIMIT :limit OFFSET :offset");
			elseif($this->app->BF)
				$st = $this->db->prepare("SELECT * FROM acp_logs WHERE server = 'bf' OR server = 'all' ORDER BY id DESC LIMIT :limit OFFSET :offset");
			$st->bindParam(':limit', $this->limit, PDO::PARAM_INT);
			$st->bindParam(':offset', $offset, PDO::PARAM_INT);
			$st->execute();
			return $st->fetchAll();
		} catch(Exception $e) {
			$this->app->error = $e->getMessage();
		}
	}

	public function GetNameChanges($id, $page = 1, $offset)
	{
		try
		{
			$this->ConnectDB();

			if($this->app->COD)
				$st = $this->db->prepare("SELECT * FROM logs WHERE server = 'cod' AND uid = :uid ORDER BY id DESC LIMIT :limit OFFSET :offset");
			elseif($this->app->CNR)
				$st = $this->db->prepare("SELECT * FROM logs WHERE server = 'cnr' AND uid = :uid ORDER BY id DESC LIMIT :limit OFFSET :offset");
			elseif($this->app->GW)
				$st = $this->db->prepare("SELECT * FROM logs WHERE server = 'gw' AND uid = :uid ORDER BY id DESC LIMIT :limit OFFSET :offset");
			elseif($this->app->BF)
				$st = $this->db->prepare("SELECT * FROM logs WHERE server = 'bf' AND uid = :uid ORDER BY id DESC LIMIT :limit OFFSET :offset");

			$st->bindParam(':uid', $id, PDO::PARAM_INT);
			$st->bindParam(':limit', $this->limit, PDO::PARAM_INT);
			$st->bindParam(':offset', $offset, PDO::PARAM_INT);
			$st->execute();
			return $st->fetchAll();
		} catch(Exception $e) {
			$this->app->error = $e->getMessage();
		}
	}

	public function GetAdminLogsCount() {
		try {
			$this->ConnectDB();
			if($this->app->COD)
				$st = $this->db->prepare("SELECT COUNT(*) as total FROM acp_logs WHERE server = 'cod'");
			elseif($this->app->CNR)
				$st = $this->db->prepare("SELECT COUNT(*) as total FROM acp_logs WHERE server = 'cnr'");
			elseif($this->app->GW)
				$st = $this->db->prepare("SELECT COUNT(*) as total FROM acp_logs WHERE server = 'gw'");
			elseif($this->app->BF)
				$st = $this->db->prepare("SELECT COUNT(*) as total FROM acp_logs WHERE server = 'bf'");

			$st->execute();
			return $st->fetch()->total;
		} catch(Exception $e) {
			$this->app->error = $e->getMessage();
		}
	}

	public function GetNameChangesCount($id) {
		try {
			$this->ConnectDB();
			if($this->app->COD)
				$st = $this->db->prepare("SELECT COUNT(*) as total FROM logs WHERE server = 'cod' AND uid = ?");
			elseif($this->app->CNR)
				$st = $this->db->prepare("SELECT COUNT(*) as total FROM logs WHERE server = 'cnr' AND uid = ?");
			elseif($this->app->GW)
				$st = $this->db->prepare("SELECT COUNT(*) as total FROM logs WHERE server = 'gw' AND uid = ?");
			elseif($this->app->BF)
				$st = $this->db->prepare("SELECT COUNT(*) as total FROM logs WHERE server = 'bf' AND uid = ?");
			$st->bindParam(1, $id, PDO::PARAM_INT);
			$st->execute();
			return $st->fetch()->total;
		} catch(Exception $e) {
			$this->app->error = $e->getMessage();
		}
	}

	public function AddACPLog($admin, $target, $type, $log) {
		try {
			$this->ConnectDB();
			$svr = "";
			if($this->app->COD)
				$svr = 'cod';
			elseif($this->app->CNR)
				$svr = 'cnr';
			elseif($this->app->GW)
				$svr = 'gw';
			elseif($this->app->BF)
				$svr = 'bf';

			$date = date('d.m.Y - H:i:s');

			$st = $this->db->prepare("INSERT INTO acp_logs (adm_id, target_id, type, log, server, date) VALUES (:admin, :target, :type, :log, :server, :date)");
			$st->bindParam(':admin', $admin, PDO::PARAM_INT);
			$st->bindParam(':target', $target, PDO::PARAM_INT);
			$st->bindParam(':type', $type, PDO::PARAM_STR);
			$st->bindParam(':log', $log, PDO::PARAM_STR);
			$st->bindParam(':server', $svr, PDO::PARAM_STR);
			$st->bindParam(':date', $date, PDO::PARAM_STR);
			$st->execute();
		} catch(Exception $e) {
			$this->app->error = $e->getMessage();
		}
	}
	public function AddNameChangeLog($id, $ip, $oldname, $newname) {
		try {
			$this->ConnectDB();
			$svr = "";
			if($this->app->COD)
				$svr = 'cod';
			elseif($this->app->CNR)
				$svr = 'cnr';
			elseif($this->app->GW)
				$svr = 'gw';
			elseif($this->app->BF)
				$svr = 'bf';

			$st = $this->db->prepare("INSERT INTO logs (uid, ip, old_name, new_name, server, date, time) VALUES (:uid, :ip, :oname, :nname, :server, CURDATE(), CURTIME())");
			$st->bindParam(':uid', $id, PDO::PARAM_INT);
			$st->bindParam(':ip', $ip, PDO::PARAM_STR);
			$st->bindParam(':oname', $oldname, PDO::PARAM_STR);
			$st->bindParam(':nname', $newname, PDO::PARAM_STR);
			$st->bindParam(':server', $svr, PDO::PARAM_STR);

			$st->execute();
		} catch(Exception $e) {
			$this->app->error = $e->getMessage();
		}
	}

	public function GetLogType($type) {
		$r = "";
		switch ($type) {
			case 'osetstats':
				$r = "Set Stats";
				break;
			case 'unban':
				$r = "Unban";
				break;
			case 'setpass':
				$r = "Set Pass";
				break;
			case 'setname':
				$r = "Set Name";
				break;
			case 'oban':
				$r = "Offline Ban";
				break;
			case 'otban':
				$r = "Offline Temp. Ban";
				break;
			case 'osetlevel':
				$r = "Set Level";
				break;
			case 'osetvip':
				$r = "Set VIP";
				break;
			case 'snapshot':
				$r = "Snapshot";
				break;

			default:
				$r = "";
				break;
		}
		return $r;
	}

	public function ShowPagination($page, $total, $url)
	{
		if(strpos($url, "?") === false) $url .= "?";
		else $url .= "&";

		if($total <= $this->limit) return "";
		$pages = ceil($total / $this->limit);
		$max = 5;
		$pagination = "<ul class='pagination'>";

		if($page > 1) {
			$prev = $page-1;
			$pagination .= "<li><a href='{$url}page={$prev}'>&laquo; Previous</a></li>";
		}

		$from = $page-floor($max/2);
		$to = $page+floor($max/2);

		if($from <= 0) {
			$from = 1;
			$to = $from+$max-1;
		}

		if($to > $pages)
		{
			$to = $pages;
			$from = $pages-$max+1;
			if($from <= 0)
			{
				$from = 1;
			}
		}

		if($to == 0)
		{
			$to = $pages;
		}

		if($from > 2)
		{
			$pagination .= "<li><a href='{$url}' title=\"Page 1\">1</a></li>";
		}

		for($i = $from; $i <= $to; ++$i)
		{
			$page_url = "{$url}page=".$i;
			if($page == $i)
			{
				$pagination .= "<li class='active'><a>{$i}</a></li> \n";
			}
			else
			{
				$pagination .= "<li><a href=\"{$page_url}\" title=\"Page {$i}\">{$i}</a></li> \n";
			}
		}

		if($to < $pages)
		{
			$last = "{$url}page=".$pages;
			$pagination .= "<li><a href=\"{$last}\" title=\"Page {$pages}\">{$pages}</a></li>";
		}

		if($page < $pages)
		{
			$next = $page+1;
			$next_page = "{$url}page=".$next;
			$pagination .= " <li><a href=\"{$next_page}\">Next &raquo;</a></li>\n";
		}
		$pagination .= "</ul>\n";
		return $pagination;

	}

	public function GetAGTagInfo()
	{
		$this->ConnectDB();
		$st = $this->db->prepare("SELECT * FROM agtags ORDER BY id DESC;");
		$st->execute();
		return $st->fetchAll();
	}

	public function RemoveAGTagUser($id)
	{
		$this->ConnectDB();
		$st = $this->db->prepare("DELETE FROM agtags WHERE id = ?");
		$st->bindParam(1, $id, PDO::PARAM_INT);
		if($st->execute())
			return 1;
		else
			return 0;
	}

	public function AddAGTagUser($name, $codid = 0, $cnrid = 0, $lsgwid = 0, $forumid, $link, $notes)
	{
		$this->ConnectDB();
		$st = $this->db->prepare("INSERT INTO agtags (name, codid, cnrid, lsgwid, forumid, link, notes) VALUES (?, ?, ?, ?, ?, ?, ?)");
		$st->bindParam(1, $name, PDO::PARAM_STR);
		$st->bindParam(2, $codid, PDO::PARAM_INT);
		$st->bindParam(3, $cnrid, PDO::PARAM_INT);
		$st->bindParam(4, $lsgwid, PDO::PARAM_INT);
		$st->bindParam(5, $forumid, PDO::PARAM_INT);
		$st->bindParam(6, $link, PDO::PARAM_STR);
		$st->bindParam(7, $notes, PDO::PARAM_STR);
		if($st->execute())
			return 1;
		else
			return 0;
	}

	public function SearchNameChanges($term)
	{
		$this->ConnectDB();
		if($this->app->COD)
			$st = $this->db->prepare("SELECT * FROM logs WHERE (oldname = ? OR newname = ? OR uid = ?) AND server = 'cod' ORDER BY id DESC");
		elseif($this->app->CNR)
			$st = $this->db->prepare("SELECT * FROM logs WHERE (oldname = ? OR newname = ? OR uid = ?) AND server = 'cnr' ORDER BY id DESC");
		elseif($this->app->GW)
			$st = $this->db->prepare("SELECT * FROM logs WHERE (oldname = ? OR newname = ? OR uid = ?) AND server = 'gw' ORDER BY id DESC");
		elseif($this->app->BF)
			$st = $this->db->prepare("SELECT * FROM logs WHERE (oldname = ? OR newname = ? OR uid = ?) AND server = 'bf' ORDER BY id DESC");
		$st->bindParam(1, $term, PDO::PARAM_STR);
		$st->bindParam(2, $term, PDO::PARAM_STR);
		$st->bindParam(3, $term, PDO::PARAM_INT);
		$st->execute();
		return $st->fetchAll();
	}
}

?>