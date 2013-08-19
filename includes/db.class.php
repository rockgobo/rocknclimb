<?PHP
/**
* @desc Database class
* 
* Encapsulates the database connection and is used to build up the query string.
* 
* The following example selects 30 users, whose names start with an "A" from a database. 
* If verboseMode is true all MSQL messages and other debug information is echoed:
* <code>
* $db = new DB();
* $db->verboseMode = true;
* $db->setTable(_DBTABLE_USER);
* $db->setParams(arry('id','name'));
* $db->addfilter('name','A%','LIKE');
* $db->setLimit(30);
* $db->select();
*		
* while(list($id,$name) = $db->fetchFirst()){
* 	echo $id. '.) '. $name;
* }
* </code>
* 
* @author Christian Matyas
*/

class DB {

	/**
	* @var host
	* The host name 
	*/
	var $host;
	
	/**
	* @var username
	* The name of the user used for the connection
	*/
	var $username;
	
	/**
	* @var password
	* The password for the user
	*/
	var $password;
	
	/**
	* @var db
	* The name of the database that should be queried
	*/
	var $db;
	
	/**
	* @var result
	* The actual result set
	*/
	var $result;
	
	/**
	* @var numRows
	* The count of rows that have been queried (selected, updated, deleted, ...)
	*/
	var $numRows;

	/**
	* @var table
	* The actual table name
	*/
	var $table;
	
	/**
	* @var arr_params
	* All params that are needed for the query (fields and values in assoc array)
	*/
	var $arr_params;
	
	/**
	* @var filter
	* Everything behind a WHERE in sql
	*/
	var $filter;
	
	/**
	* @var limit
	* Limit part of the query
	*/
	var $limit;
	
	/**
	* @var order 
	* How the query is ordered 
	*/
	var $order;

	/**
	* @var verboseMode
	* Toggles output of mysql_error Messages
	* and other exceptions thrown from MYSQL
	* verboseMode = false should be used for security issues
	* only use verbosMode for debugging
	*/
	var $verboseMode;
	
	/**
	*
	*
	*/
	var $boolTableInfoFetched;
	var $arrFieldInfos;
	var $arrTableInfos;
	

	/**
	 * @desc Constructor 
	 * 
	 * The optional parameter can be set in a configuration file as global defines.
	 * 
	* @return void
	* @param string[optional] $dbname 	_MYSQL_NAME
	* @param string[optional] $host 	_MYSQL_HOST
	* @param string[optional] $user 	_MYSQL_USERNAME
	* @param string[optional] $password	_MYSQL_PASSWORD
	*/
	function DB ($dbname=_MYSQL_DB_NAME, $host=_MYSQL_HOST,$user=_MYSQL_USERNAME,$password=_MYSQL_PASSWORD){
		$this->host=$host;
		$this->username=$user;
		$this->password=$password;
		$this->connect();
		$this->selectDB($dbname);

		// default init
		$this->result=0;
		$this->arr_params='*';
		$this->filter='';
		$this->limit='';
		$this->order='';
		$this->stdFilterOp='=';
		$this->stdFilterLogic='AND';
		$this->verboseMode=false;
		$this->boolTableInfoFetched = false;
	}

	/**
	* @return void
	* @desc Connects to the MySQL DB with given parameters
	*/
	function connect (){
		if ($this->verboseMode)
			echo 'connect: connecting to host '.$this->host.'...';
		$this->db = mysql_pconnect($this->host,$this->username,$this->password) or die('Keine Verbindung möglich: ' . mysql_error());;
		if ($this->verboseMode)
			echo "succesfully<br>\n";
	}

	/**
	* @return void
	* @desc Selects given Database
	*/
	function selectDB ($dbname){
		if ($this->verboseMode)
			echo 'selectDB: selecting DB...';
		mysql_select_db($dbname,$this->db) or die ('Database '.$dbname.' does not exist');
		if ($this->verboseMode)	
			echo "succesfully<br>\n";
	}

	/**
	* @return void
	* @desc locks given Table
	*/	
	function lockOneTable($tableName)
	{
		$string="LOCK TABLES ".$tableName." "."WRITE;";
		return mysql_query($string,$this->db);
	}

	function lockTwoTables($tableName1, $tableName2)
	{
		$string="LOCK TABLES ".$tableName1." "."WRITE, ".$tableName2." "."WRITE;";
		return mysql_query($string,$this->db);
	}
	
	/**
	* @return void
	* @desc unlocks given Table
	*/	
	function unlock()
	{
		$string="UNLOCK TABLES;";
		return mysql_query($string,$this->db);
	}	
	/**
	* @return void
	* @desc Closes the connection to the database
	*/
	function close () {
		// don´t do a close on a persistent connection !!!
		//mysql_close($this->db);
		if ($this->verboseMode) {
			echo "close: disconnected<br>\n";
		}
	}

	/**
	* @return void
	* @param String
	* @desc Submits a Query to the database
	* @desc This function is called by all other functions like select() oder save() in this class to submit the final query
	* @desc Can also be used for manually submitting complex queries
	* @desc It automatically sets numRows variable after a select()
	*/
	function query ($queryString) {
		$this->result = mysql_query($queryString,$this->db);
		if ($this->result && preg_match('/SELECT/i',$queryString)) {
			$this->numRows = mysql_num_rows($this->result);
		}
		else if (preg_match('/DELETE/i',$queryString)) $this->numRows = mysql_affected_rows($this->db);
		else {
			$this->numRows = 0;
		}
		if ($this->verboseMode) {
			echo 'query: submitted query <b>'.$queryString.'</b>';
			if(preg_match('/SELECT/i',$queryString)) {
				echo ' (numRows: <b>'.$this->numRows.')</b>';
			}
			else {
				echo ' (affectedRows: <b>'.mysql_affected_rows($this->db).')</b>';
			}
			if($this->getError()) {
				echo '<br>MySQL said: '.$this->getError();
			}
			echo "<br>\n";
		}
	}

	/**
	* @return void
	* @desc Submits a insert or update Query based on given params
	* @desc if no id is given in the arr_params variable insert() is called
	* @desc if an id is given in the arr_params variable update() with the id as filter is called
	*/
	function save()
	{
		if(is_array($this->arr_params)) {
			if($this->arr_params['id']=='' || $this->arr_params['id']==0) {
				if ($this->verboseMode)
					echo "save: initiated insert<br>\n";
				$this->insert();
			}
			else
			{
				$this->clearQuery(false,false,true);
				$this->addFilter('id',$this->arr_params['id']);
				if ($this->verboseMode)
					echo "save: initiated update<br>\n";
				$this->update();
			}
			$this->numRows = mysql_affected_rows($this->db);
		}
	}

	/**
	* @return void
	* @desc Submits a select Query
	* @desc This function submits a select query based on all parameters defined
	*/
	function select()
	{
		$query='SELECT ';
		if(is_array($this->arr_params)) {
			foreach($this->arr_params as $key=>$value) {
				$query.=$value.', ';
			}
			$query = substr($query, 0, strlen($query)-2); // trim last comma
		}
		else { // params is a *
			$query.=$this->arr_params;
		}
		$query.=' FROM '.$this->table;
		if(!empty($this->filter)) {
			$query.=' WHERE '.$this->filter;
		}
		if(!empty($this->order)) {
			$query.=$this->order;
		}
		if(!empty($this->limit)) {
			$query.=$this->limit;
		}
		if ($this->verboseMode)
			echo "select: ok<br>\n";
		$this->query($query);
	}

	/**
	* @return void
	* @desc Submits an insert Query
	* @desc This function submits an insert query based on all parameters defined
	*/
	function insert()
	{
		//unset($this->arr_params['id']);
		$query='INSERT INTO '.$this->table.' (';
		if(is_array($this->arr_params)) {
			foreach($this->arr_params as $key=>$value) {
				$query.=$key.', ';
			}
			$query = substr($query, 0, strlen($query)-2); // trim last comma
		}
		$query.=') VALUES (';
		if(is_array($this->arr_params)) {
			foreach($this->arr_params as $key=>$value) {
				if(strpos($value,"GeomFromText") === 0){
					//TODO geometry wieder eintragen....
					$query.=$value.', ';	
				}
				elseif(is_numeric($value)){
					$query.=addslashes($value).', ';	
				}
				else{
					$query.='\''.addslashes($value).'\', ';
				}
			}
			$query = substr($query, 0, strlen($query)-2); // trim last comma
		}
		$query.=')';
		if ($this->verboseMode)
			echo "insert: ok<br>\n";
		$this->query($query);
	}

	/**
	* @return void
	* @desc Submits an update Query
	* @desc This function submits an update query based on all parameters defined
	*/
	function update()
	{
		$query='UPDATE '.$this->table.' SET ';
		if(is_array($this->arr_params)) {
			foreach($this->arr_params as $key=>$value) {
				if($key=='_SQL_') { // value is sql code
					$query.=$value.', ';
				} else {
					$query.=$key.'=\''.addslashes($value).'\', ';
				}
			}
			$query = substr($query, 0, strlen($query)-2); // trim last comma
		}
		if(!empty($this->filter)) {
			$query.=' WHERE '.$this->filter;
		}
		if ($this->verboseMode)
			echo "update: ok<br>\n";
		$this->query($query);
		$this->numRows = mysql_affected_rows($this->db);
	}

	/**
	* @return void
	* @desc Submits a delete Query
	* @desc This function submits a delete query based on all parameters defined
	*/
	function delete()
	{
		$query='DELETE FROM '.$this->table;
		if(!empty($this->filter)) {
			$query.=' WHERE '.$this->filter;
		}
		if ($this->verboseMode)
			echo "delete: ok<br>\n";
		$this->query($query);
	}
	
	/**
	 * @return void
	 * @desc Submits a truncate Query
	 * @desc This function submits a truncate query on the table defined
	 *
	 */
	function truncate()
	{
		$query = 'TRUNCATE TABLE '.$this->table;
		$this->query($query);
	}
	

	/**
	* @return void
	* @desc Table name for the Query is set here
	*/
	function setTable($tableName)
	{
		$this->table=$tableName;
		if ($this->verboseMode)
			echo "setTable: set table name to <b>".$tableName."</b><br>\n";
	}

	/**
	* @return void
	* @param Mixed
	* @desc sets params for the query
	* @desc This function is called in 3 different ways:
	* 1. with a string like '*' or 'max(id)'
	* 2. with a normal array like ('id','name')
	* 	 for select()
	* 3. with an associative array like ('id'=>$id,'name'=>$name)
	* 	 for save() or update() or insert()
	* all params will automatically be "addslashed" for security reasons
	*/
	function setParams($params='*')
	{
		$this->arr_params=$params;
	
		if ($this->verboseMode)
			echo "setParams: set <b>".count($params)."</b> params<br>\n";
	}

	/**
	* @return void
	* @param String
	* @desc Manually sets a filter that is used for the WHERE part of the query. Also see addfilter
	* @desc (this overwrites all previously filters set by addFilter)
	*/
	function setFilter($filter)
	{
		$this->filter = $filter;
		if ($this->verboseMode)
			echo "setFilter: New Filter is <b>".$this->filter."</b><br>\n";
	}

	/**
	* @return void
	* @param String
	* @param String
	* @param String
	* @param String
	* @desc Adds a filter to the query
	* @desc A filter can be added to the WHERE condition of the query like this:
	* @desc fieldName is the name of the field the filter is applied to
	* @desc value is the value of the field that should be filtered
	* @desc op is the operand that is used for the filter, by default it is =. It can also be % or ? for LIKE or any other valid operator
	* @desc logic can be (AND|OR), AND by default. With the logic you can define how the filter is added to the previously set filters
	*/
	function addFilter($fieldName, $value, $op='=', $logic='AND')
	{
		if($op=='%')
		{
			$value='%'.addslashes($value).'%';
			$op=' LIKE ';
		}
		elseif($op=='?')
		{
			$op=' LIKE ';
		}
		if(!empty($this->filter))
		{
			$this->filter.=' '.$logic.' ';
		}
		$this->filter.= $fieldName.$op.'\''.addslashes($value).'\'';
		if ($this->verboseMode)
			echo "addFilter: New Filter is <b>".$this->filter."</b><br>\n";
	}
	
	/**
	* @return void
	* @param Int
	* @param Int
	* @desc Adds a limit statement to the query
	*/
	function setLimit($numberOfRows,$offset=0)
	{
		$this->limit = " LIMIT ".$numberOfRows;
		if($offset!=0)
		{
			$this->limit = " LIMIT ".$offset.", ".$numberOfRows;
		}
		if ($this->verboseMode)
			echo "setLimit: Limit is: <b>".$this->limit."</b><br>\n";
	}

	/**
	* @return void
	* @param String
	* @desc Adds an order statement to the query
	*/
	function setOrderBy($orderByStatement)
	{
		$this->order = " ORDER BY ".$orderByStatement;
		if ($this->verboseMode)
			echo "setOrderBy: OrderBy is: <b>".$this->order."</b><br>\n";
	}

	/**
	* @return void
	* @param Boolean
	* @param Boolean
	* @param Boolean
	* @desc Clears all parameters of the last query
	* @desc by default all parameters are reset, but you can also reset only a certain part of the query by setting the flags for each parameter
	*/
	function clearQuery($clearFields=true,$clearTable=true,$clearFilter=true)
	{
		if($clearFields) {
			$this->arr_params='*';
		}
		if($clearTable) {
			$this->table='';
		}
		if($clearFilter) {
			$this->filter = '';
			$this->limit = '';
			$this->order = '';
		}
	}

	/**
	* @return Array
	* @desc Fetches a single result row in assoc array
	*/
	function fetchArray($result_type=MYSQL_BOTH) {
		if ($this->result) {
			$result = mysql_fetch_array($this->result,$result_type);
			if($result) {
				array_walk($result, array('DB','walkStripslashes'));#
			}
			return $result;
		} else {
			if ($this->verboseMode)
				echo "fetchArray: No valid result given<br>\n";
		}
	}

	/**
	* @return Array
	* @desc Fetches a single result row in array
	*/
	function fetchRow() {
		if ($this->result) {
			$result = mysql_fetch_row($this->result);
			if($result) {
				array_walk($result, array('DB','walkStripslashes'));#
			}
			return $result;
		} else {
			if ($this->verboseMode)
				echo "fetchRow: No valid result given<br>\n";
		}
	}

	
	function walkStripslashes(&$item, $key='') {
		stripslashes($item);
	}
		
	/**
	* @return Array
	* @desc Fetches all result rows in assoc array
	*/
	function fetchArrayAll($result_type=MYSQL_BOTH) {
		if ($this->result) {
			while($row = $this->fetchArray($result_type)) {
				$rows[]=$row;
			}
			return $rows;
		}
		else {
			if ($this->verboseMode)
				echo "fetchArrayAll: No valid result given<br>\n";
		}
	}

	/**
	* @return Mixed
	* @desc Fetches the first field in the first result row (good for count(*), max(), min())
	*/
	function fetchFirst() {
		if ($this->result) {
			$result=mysql_fetch_row($this->result);
			return stripslashes($result[0]);
		}
		else {
			if ($this->verboseMode)
				echo "fetchFirst: No valid result given<br>\n";
		}
	}

	/**
	* @return Int
	* @desc Returns the last generated Id
	*/
	function getInsertId() {
		return mysql_insert_id($this->db);
	}

	/**
	* @return String
	* @desc Returns MySQL Error Description
	*/
	function getError() {
		return mysql_error($this->db);
	}
	
	
	/**
	* @return void
	* @desc fetches all table infos (e.g. field infos) from table
	*/
	function getTableInfo () {
		if ($this->verboseMode) echo "table info fetched...";
		$this->boolTableInfoFetched = false;
		
		$this->arrFieldInfos = array ();
		$result = mysql_query('select * from '.$this->table);
		$fieldNum = mysql_num_fields($result);
		for ($i=0; $i < $fieldNum; $i++) {
			if ($this->verboseMode) echo 'fetching fieldName: '.mysql_field_name($result, $i).'<br>';
			$this->arrFieldInfos[mysql_field_name($result,$i)] = array ('type' => mysql_field_type($result, $i),'length' => mysql_field_len($result, $i),'flags' => mysql_field_flags($result, $i));
		}
		$this->boolTableInfoFetched = true;
		if ($this->verboseMode) {
			echo "OK\n (numFields: $fieldNum)<br>";
			print_r ($this->arrFieldInfos);
		}
	}
	
	/**
	* @return assoc_array ( 'type','length', 'flags');
	* @param String $strFieldName
	* @desc returns info about given fieldname in table
	*/
	function getFieldInfo ($strFieldName) {
		if (!$this->boolTableInfoFetched) $this->getTableInfo();
		
		if ($this->verboseMode)	echo 'Looking for: '.$strFieldName.'...';
		
		if (!key_exists($strFieldName, $this->arrFieldInfos)) { 
			if ($this->verboseMode) echo "$strFieldName field does not exist"; 
			return false; 
		}
		
		return $this->arrFieldInfos[$strFieldName];
	}
}
?>