<?php
class Content {
	private $pageHeader;
	private $panelName;
	private $tableHeader = array();
	private $tableBody = array();
	private $pageAction;

	public function __construct($pageAction, $pageHeader, $panelName, $queryResult) {
		$this->pageAction = $pageAction;
		$this->pageHeader = $pageHeader;
		$this->panelName = $panelName;
		$this->findKeyValue($queryResult);
	}

	private function findKeyValue($queryResult) {
		if (!empty($queryResult) && $this->pageAction != 'br') {
			$isFindKeys = false;
			while($dataRow = $queryResult->fetch(PDO::FETCH_ASSOC)) {
				if (!$isFindKeys) {
					foreach ($dataRow as $key=>$value) {
						$this->tableHeader[] = $key;
					}
					$isFindKeys = true;
				}
				foreach ($dataRow as $value) {
					$this->tableBody[] = $value;
				}
			}
		}
	}

	public function generatePageHeader() {
		echo $this->pageHeader;
	}

	public function generatePanelName() {
		echo $this->panelName;
	}

	public function generateTagTH() {
		foreach ($this->tableHeader as $th) {
			echo "<th>$th</th>";
		}
		// echo ifelse "<th>btn action to something...</th>> fun("th") fun("td");
	}

	public function generateTagTD() {

		$n = count($this->tableHeader);
		$m = count($this->tableBody) / $n;
		for ($i = 0; $i < $m; $i++) {
			echo "<tr>";
			for ($j = 0; $j < $n; $j++) {
				//check Link createLink array 2 dims maps and create case possible get form column
				echo "<td>".$this->tableBody[$n*$i + $j]."</td>";
			}
			// echo <td>btn1 btn2 btn3</td> if else
			echo "</tr>";
		}
		if (count($this->tableBody) == 0) {
			echo "<p>No data available</p>";
		}
	}

	public function getPageHeader() {
		return $this->pageHeader;
	}

	public function getPanelName() {
		return $this->panelName;
	}
}

include 'config.php';
class ContentCreator {
	private $db_connection;
	private $username;
	private $staffID;
	private $status;
	private $sqlCommand;
	private $queryResult;
	private $pageHeader;
	private $panelName;
	private $content;
	private $pageAction;
	private $querySearch;

	public function __construct() {
		session_start();
		$this->userLoggedIn();
		$this->createConnection();
		$this->doAction();
		$this->queryRun();
		//$this->showContent();
		echo $this->sqlCommand;
		echo "<br>";
		var_dump($this->queryResult->fetchAll());
	}

	private function userLoggedIn() {
		if (!isset($_SESSION['username'])) {
			// $this->goToLogin("relogin");
			echo "";
		}
		else {
			$this->username = $_SESSION['username'];
			$this->studentID = $_SESSION['staff_id'];
			$this->status = $_SESSION['status']; //status = staff
		}
	}

	private function createConnection() {
		try {
			$host = DB_HOST;
			$user = DB_USER;
			$pass = DB_PASS;
			$dbname = DB_NAME;
			$port = DB_PORT;
			$this->db_connection = new PDO("mysql:host=$host;dbname=$dbname".";port=$port", $user, $pass);
		} catch (PDOException $error) {
			die("Error : " . $error->getMessage());
		}
	}

	// can move to define
	private function convertSearchStr() {
		$search = $_GET["search"];
		if (!empty($search)) {
			if ($search == "ready") {
				$search = "Ready";
			}
			else if ($search == "borrow") {
				$search = "Borrow";
			}
			else if ($search == "return") {
				$search = "Return";
			}
			else if ($search == "lost") {
				$search = "Lost";
			}
			else if ($search == "repair") {
				$search = "Repair";
			}
			else if ($search == "requestborrow") {
				$search = "RequestBorrow";
			}
			else if ($search == "requestreturn") {
				$search = "RequestReturn";
			}
			else if ($search == "requestloss") {
				$search = "RequestLoss";
			}
			else if ($search == "loss") {
				$search = "Loss";
			}
			else if ($search == "repaired") {
				$search = "Repaired";
			}
			else if ($search == "torepair") {
				$search = "ToRepair";
			}
			else if ($search == "payed") {
				$search = "Payed";
			}
			else if ($search == "notpayed") {
				$search = "NotPayed";
			}
			else if ($search == "all") {
				$search = "";
			}
		}
		//$this->querySearch = $search;
		return $search;
	}
	// can move to define

	private function doAction() {
		if (isset($_GET["action"])) {
			$action = $_GET["action"];
			$this->pageAction = $action;
			$search = $this->convertSearchStr();
			if ($action == "bike") {
				$this->setContentBike($search);
			}
			else if ($action == "request") {
				$this->setContentRequest($search);
			}
			else if ($action == "history") {
				$this->setContentHistory($search);
			}
			else if ($action == "repair") {
				$this->setContentRepair($search);
			}
			else if ($action == "blacklist") {
				$this->setContentBlackList($search);
			}
			else if ($action == "student") {
				$this->setContentUserStudent();
			}
			else if ($action == "staff") {
				$this->setContentUserStaff();
			}
			else if ($action == "logout") {
				$this->goToLogin("logout");
			}
			else {
				$this->setContentHome();
			}
		}
	}

	private function queryRun() {
		$this->queryResult = $this->db_connection->query($this->sqlCommand); // Can Reuse
	}

	private function showContent() {
		// edithere add Value something to class Content to create button coloumn javascript to do it work
		$this->content = new Content($this->pageAction, $this->pageHeader, $this->panelName, $this->queryResult);
		//editHere
	}

	private function goToLogin($action) {
		header("refresh: 0; url=logout.php?action=$action");
	}

	public function getStatus() {
		return $this->status;
	}

	public function getContent() {
		return $this->content;
	}

	public function getPageAction() {
		return $this->pageAction;
	}

	public function getStaffID() {
		return $this->staffID;
	}

	public function getQueryResult() {
		return $this->queryResult;
	}

	public function getUsername() {
		return $this->username;
	}

	public function getQuerySearch() {
		return $this->querySearch;
	}

	private function setContentHome() {
		// Edit Here
		echo "Home";
	}

	private function setContentBike($search) {
		if (!empty($search)) {
			$this->sqlCommand = "SELECT * FROM Bike WHERE Status = '$search'";
		}
		else {
			$this->sqlCommand = "SELECT * FROM Bike";
			$search = "All";
		}
		$this->pageHeader = "Bike ($search)";
		$this->panelName = "Bike ($search)";
	}

	private function setContentRequest($search) {
		if ($search != "All") {
			$this->sqlCommand = "SELECT * FROM Request WHERE Type = '$search'";
		}
		else {
			$this->sqlCommand = "SELECT * FROM Request";
			$search = "All";
		}
		$this->pageHeader = "Request ($search)";
		$this->panelName = "Request ($search)";
	}

	private function setContentHistory($search) {
		if (!empty($search)) {
			$this->sqlCommand = "SELECT * FROM History WHERE Operation = '$search'";
		}
		else {
			$this->sqlCommand = "SELECT * FROM History";
			$search = "All";
		}
		$this->pageHeader = "History ($search)";
		$this->panelName = "History ($search)";
	}

	private function setContentRepair($search) {
		if ($search == "Repaired") {
			//$this->sqlCommand = "SELECT * FROM History";
			$this->sqlCommand = "SELECT _Order, BikeID, Description, Cost FROM Repairing INNER JOIN Bike ON Repairing.BikeID = Bike.BikeID WHERE Status != 'Repair'";
		} 
		else if ($search == "ToRepair") {
			$this->sqlCommand = "SELECT _Order, BikeID, Description, Cost FROM Repairing INNER JOIN Bike ON Repairing.BikeID = Bike.BikeID WHERE Status = 'Repair'";
		}
		else {
			$this->sqlCommand = "SELECT * FROM Repairing";
			$search = "All";
		}
		$this->pageHeader = "History ($search)";
		$this->panelName = "History ($search)";

	}

	// editHere
	private function setContentBlackList($search) {
		if ($search == "Payed") {
			$this->sqlCommand = "SELECT _Order, StdID, BikeID, Cost, StaffID FROM BlackList INNER JOIN Payed ON BlackList.Order = Payed.Order";
		}
		else {
			$this->sqlCommand = "SELECT _Order, StdID, BikeID, Cost, StaffID FROM BlackList INNER JOIN NotPayed ON BlackList.Order = NotPayed.Order";
		}
	}
	//edit Here

	//left join ?
	private function setContentUserStudent() {
		$this->sqlCommand = "SELECT StdID,User,Status,FirstName,LastName,MajorCode,Telephone FROM Student LEFT JOIN StdAccount ON Student.StdID = StdAccount.StdID";
	}

	//left join ?
	private function setContentUserStaff() {
		$this->sqlCommand = "SELECT StaffID,User,FirstName,LastName,Telephone FROM Staff LEFT JOIN StaffAccount ON Staff.StaffID = StaffAccount.StaffID";
	}
}
