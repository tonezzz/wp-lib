<?php //die(__FILE__); 
/**
 * gz_poi_water:Manage data point for water quality
 * v0.00:20190117:Tony:Return test data
 * 
 * 1DIDB71Y9tSAj6r88Hf2kuUvjeM8Lu8RAAJgqGTYQyu4
 * https://sheets.googleapis.com/v4/spreadsheets/1DIDB71Y9tSAj6r88Hf2kuUvjeM8Lu8RAAJgqGTYQyu4/values/Sheet1!A1:D5
 */
class gz_gsheet extends gz_tpl{
	public $client,$service,$sheet_id,$sheets;
	public $spreadsheets;

	/**
	 * Undocumented function
	 *
	 * @param array $prm
	 * 	auth_type	=> How to authenticate (file)
	 *	auth_file	=> Path to the authentication file
	 *	app_name	=> Name of the app
	 *  sheet_id	=> ID of the sheet to be read
	 */
	public function __construct($prm=[]){//ob_clean(); echo '<pre>'; print_r($prm); die();
		extract($prm,EXTR_PREFIX_ALL,'prm');

		$this->sheet_id = $prm_sheet_id;

		$arg = [];
		parent::__construct($arg); //init_shortcodes
		//ob_clean(); echo '<pre>'; print_r($this); die();
		//$sheet_id = '1DIDB71Y9tSAj6r88Hf2kuUvjeM8Lu8RAAJgqGTYQyu4';

		//https://www.fillup.io/post/read-and-write-google-sheets-from-php/
		$this->client = new \Google_Client();
		$this->client->setApplicationName($prm_app_name);
		$this->client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);
		$this->client->setAccessType('offline'); //ob_clean(); echo '<pre>'; print_r($client); die();

		//$auth_file = $this->dir.'credentials.json';
		//$auth_file = $this->dir.'Water Quality-5d3eed252673.json';
		//$auth_file = $this->dir.'Water Quality-1ba1f5ffe528.json';
		$jsonAuth = file_get_contents($prm_auth_file); //ob_clean(); echo '<pre>'; print_r($jsonAuth); die();
		$jsonAuth = json_decode($jsonAuth,true); //ob_clean(); echo '<pre>'; print_r($jsonAuth); die();
		$this->client->setAuthConfig($jsonAuth); //ob_clean(); echo '<pre>'; print_r($client); die();

		$this->service = new Google_Service_Sheets($this->client);
		$this->sheets = $this->service->spreadsheets->get($this->sheet_id); //ob_clean(); echo '<pre>'; print_r($this->sheet->getSheets()); die();
		//$this->spreadsheet = $this->sheets;
		$this->spreadsheets = $this->service->spreadsheets;
		//$this->sheet = $this->service->spreadsheets->get($prm_sheet_id); ob_clean(); echo '<pre>'; print_r($this->sheet); die();

		//$sheets = new \Google_Service_Sheets($client); //ob_clean(); echo '<pre>'; print_r($sheets); die();
		//$range = 'A:H';
		//$rows = $sheets->spreadsheets_values->get($sheet_id, $range, ['majorDimension' => 'ROWS']); //ob_clean(); echo '<pre>'; print_r($rows); die();
	}

	/**
	 * Undocumented function
	 *
	 * @param array $prm
	 * 	data_dim	= ROWS/COLS
	 * 	data_point	= Range of data (Areas, Areas!A1:1, Areas!A2:All)
	 * 	data_value	= Sheet to lookup for data
	 * @return void
	 */
	public function get_data_lookup($prm){
		extract($prm,EXTR_PREFIX_ALL,'prm');
		$rs = $this->service->spreadsheets_values->get($this->sheet_id, $prm_data_point, ['majorDimension' => $prm_data_dim]); //ob_clean(); echo '<pre>'; print_r($rs); die();
		$points = $rs['values']; //ob_clean(); echo '<pre>'; print_r($points); die();
		$pois = []; $poi =[];
		for($i=1;$i<count($points);$i++){
			$poi_name = $points[$i][1];
			$pois[$poi_name]['area'] = $points[$i][0];
			$pois[$poi_name]['desc'] = $points[$i][2];
			$pois[$poi_name]['E'] = $points[$i][3];
			$pois[$poi_name]['N'] = $points[$i][4];
		} //ob_clean(); echo '<pre>'; print_r($pois); die();

		$rs = $this->service->spreadsheets_values->get($this->sheet_id, $prm_data_value, ['majorDimension' => $prm_data_dim]); //ob_clean(); echo '<pre>'; print_r($rs); die();
		$values = $rs['values'];
		$labels = $values[0]; //ob_clean(); echo '<pre>'; print_r(compact('labels','values')); die();
		for($i=1;$i<count($values);$i++){
			$poi_name = $values[$i][0]; 
			if(isset($pois[$poi_name])){ //ob_clean(); echo '<pre>'; print_r($pois[$poi_name]); die();
				for($j=1;$j<count($labels);$j++)if(isset($labels[$j]) && isset($values[$i][$j])){
					$k2 = $labels[$j]; //ob_clean(); echo '<pre>'; print_r([$poi_name,$k2,$values[$i][$j]]); die();
					$pois[$poi_name][$k2] = $values[$i][$j]; 
				} //ob_clean(); echo '<pre>'; print_r($pois[$poi_name]); die();
			}
		} //ob_clean(); echo '<pre>'; print_r($pois); die();
		//Clean up empty pois
		foreach($pois as $key=>$val) {if(count($pois[$key])==4) unset($pois[$key]);} //ob_clean(); echo '<pre>'; print_r($pois); die();
		return $pois;
	}

	/**
	 * function get_data()
	 *
	 * @param array $prm
	 * 	data_dim	= ROWS/COLS
	 * 	data_range	= Range of data (Areas, Areas!A1:1, Areas!A2:All)
	 * @return void
	 */
	public function get_data($prm){//if(!empty($prm))die('<pre>'.print_r($prm,true));
		extract($prm,EXTR_PREFIX_ALL,'prm');
		$rs = $this->service->spreadsheets_values->get($this->sheet_id, $prm_data_range, ['majorDimension' => $prm_data_dim]); //ob_clean(); echo '<pre>'; print_r($rs); die();
		if(!empty($prm_data_label_col)){
			$data = $rs['values']; //if($prm__debug){ob_clean(); echo '<pre>'; print_r($data); die();}
			//extract($data[0],)
			for($i=1;$i<count($data);$i++);

		}else $values = $rs['values'];
		
		return $rs['values'];
	}
}
