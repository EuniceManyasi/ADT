<?php
class Patient_Visit extends Doctrine_Record {

	public function setTableDefinition() {
		/*
		 * pill_count is expected pill count
		 * mos is actual pill count
		 */
		$this -> hasColumn('Patient_Id', 'varchar', 10);
		$this -> hasColumn('Visit_Purpose', 'varchar', 10);
		$this -> hasColumn('Current_Height', 'varchar', 10);
		$this -> hasColumn('Current_Weight', 'varchar', 10);
		$this -> hasColumn('Regimen', 'varchar', 100);
		$this -> hasColumn('Last_Regimen', 'varchar', 100);
		$this -> hasColumn('Regimen_Change_Reason', 'varchar', 10);
		$this -> hasColumn('Drug_Id', 'varchar', 10);
		$this -> hasColumn('Batch_Number', 'varchar', 10);
		$this -> hasColumn('Brand', 'varchar', 10);
		$this -> hasColumn('Indication', 'varchar', 10);
		$this -> hasColumn('Pill_Count', 'varchar', 10);
		$this -> hasColumn('Comment', 'text');
		$this -> hasColumn('Timestamp', 'varchar', 32);
		$this -> hasColumn('User', 'varchar', 10);
		$this -> hasColumn('Facility', 'varchar', 10);
		$this -> hasColumn('Dose', 'varchar', 20);
		$this -> hasColumn('Dispensing_Date', 'varchar', 20);
		$this -> hasColumn('Dispensing_Date_Timestamp', 'varchar', 32);
		$this -> hasColumn('Quantity', 'varchar', 100);
		$this -> hasColumn('Machine_Code', 'varchar', 100);
		$this -> hasColumn('Duration', 'varchar', 10);
		$this -> hasColumn('Months_Of_Stock', 'varchar', 10);
		$this -> hasColumn('Adherence', 'varchar', 10);
		$this -> hasColumn('Missed_Pills', 'varchar', 10);
		$this -> hasColumn('Non_Adherence_Reason', 'varchar', 255);
		$this -> hasColumn('Merged_From', 'varchar', 50);
		$this -> hasColumn('Regimen_Merged_From', 'varchar', 20);
		$this -> hasColumn('Last_Regimen_Merged_From', 'varchar', 20);
		$this -> hasColumn('Active', 'int', 5);
		
	}

	public function setUp() {
		$this -> setTableName('patient_visit');
	}

	public function getAllScheduled($timestamp) {
		$query = Doctrine_Query::create() -> select("*") -> from("Patient_Visit") -> where("Dispensing_Date_Timestamp >= '$timestamp'");
		$visits = $query -> execute();
		return $visits;
	}

	public function getAll() {
		$query = Doctrine_Query::create() -> select("Dispensing_Date,Dispensing_Date_Timestamp") -> from("Patient_Visit");
		$visits = $query -> execute();
		return $visits;
	}

	public function getTotalVisits($facility) {
		$query = Doctrine_Query::create() -> select("count(*) as Total_Visits") -> from("Patient_Visit") -> where("Facility='$facility'");
		$total = $query -> execute();
		return $total[0]['Total_Visits'];
	}

	public function getPagedPatientVisits($offset, $items, $machine_code, $patient_ccc, $facility, $date,$drug) {
		$query = Doctrine_Query::create() -> select("pv.*") -> from("Patient_Visit pv") -> leftJoin("Patient_Visit pv2") -> where("pv2.Patient_Id = '$patient_ccc' and pv2.Machine_Code ='$machine_code' and pv2.Dispensing_Date ='$date' and pv2.Facility='$facility' and pv2.Drug_Id ='$drug' and pv.Facility='$facility'") -> offset($offset) -> limit($items);
		//echo $query->getSQL();
		$patient_visits = $query -> execute(array(), Doctrine::HYDRATE_ARRAY);
		return $patient_visits;
	}

	public function getPagedFacilityPatientVisits($offset, $items, $facility) {
		$query = Doctrine_Query::create() -> select("*") -> from("Patient_Visit") -> where("Facility='$facility'") -> offset($offset) -> limit($items);
		//echo $query->getSQL();
		$patient_visits = $query -> execute(array(), Doctrine::HYDRATE_ARRAY);
		return $patient_visits;
	}
	public function getNon_adherence_reason(){
        $sql=("");
	    $query = $this -> db -> query($sql);
		$patient_visit = $query -> result_array();
		return $patient_visit;
	}
	public function getPill_count_Adherence($start_date, $end_date){
		$sql="SELECT pv.dispensing_date, pv.patient_id,
IF(UPPER(rst.Name) ='ART','art','non_art') as service,
        		    IF(UPPER(g.name) ='MALE','male','female') as gender,
        		    IF(FLOOR(DATEDIFF(CURDATE(),p.dob)/365)<15,'<15', IF(FLOOR(DATEDIFF(CURDATE(),p.dob)/365) >= 15 AND FLOOR(DATEDIFF(CURDATE(),p.dob)/365) <= 24,'15_24','>24')) as age,
                    (AVG(((pv.quantity-(pv.pill_count-pv.months_of_stock))/pv.quantity)*100)) as avg_pill_adh
                FROM patient_visit pv
                LEFT JOIN patient p ON p.patient_number_ccc = pv.patient_id
                LEFT JOIN regimen_service_type rst ON rst.id = p.service
                LEFT JOIN gender g ON g.id = p.gender 
                WHERE pv.dispensing_date BETWEEN '$start_date'
                            AND '$end_date'
                            GROUP BY patient_id";
        $query = $this ->db ->query($sql);
        $results = $query -> result_array();
        return $results;

	}
	public function getSelf_Report_Adherence($start_date, $end_date){
		$sql="SELECT pv.dispensing_date, pv.patient_id,
AVG((((DATEDIFF(CURDATE(),dispensing_date)) * frequency)- missed_pills)/(((DATEDIFF(CURDATE(),dispensing_date)) * frequency))*100) as self_report,
                    IF(UPPER(rst.Name) ='ART','art','non_art') as service,
        		    IF(UPPER(g.name) ='MALE','male','female') as gender,
        		    IF(FLOOR(DATEDIFF(CURDATE(),p.dob)/365)<15,'<15', IF(FLOOR(DATEDIFF(CURDATE(),p.dob)/365) >= 15 AND FLOOR(DATEDIFF(CURDATE(),p.dob)/365) <= 24,'15_24','>24')) as age

                FROM patient_visit pv 
                LEFT JOIN dose d ON d.Name=pv.dose
                LEFT JOIN patient p ON p.patient_number_ccc = pv.patient_id
                LEFT JOIN regimen_service_type rst ON rst.id = p.service
                LEFT JOIN gender g ON g.id = p.gender
                WHERE pv.dispensing_date BETWEEN '$start_date'
                            AND '$end_date'";
		$query=$this->db->query($sql);
		$results=$query->result_array();
		return $results;
	}
	


}
