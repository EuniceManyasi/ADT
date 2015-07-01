<?php
class Patient_Appointment extends Doctrine_Record {

	public function setTableDefinition() {
		$this -> hasColumn('Patient', 'varchar', 25);
		$this -> hasColumn('Appointment', 'varchar', 25);
		$this -> hasColumn('Facility', 'varchar', 25);
		$this -> hasColumn('Machine_Code', 'varchar', 10);
	}

	public function setUp() {
		$this -> setTableName('patient_appointment');
		$this -> hasOne('Patient as Patient_Object', array('local' => 'Patient', 'foreign' => 'id'));
	}

	public function getAllScheduled($timestamp) {
		$query = Doctrine_Query::create() -> select("*") -> from("Patient_Appointment") -> where("Appointment = '$timestamp'");
		$appointments = $query -> execute();
		return $appointments;
	}
	public function getAll() {
		$query = Doctrine_Query::create() -> select("*") -> from("Patient_Appointment");
		$appointments = $query -> execute();
		return $appointments;
	}

	public function getTotalAppointments($facility) {
		$query = Doctrine_Query::create() -> select("count(*) as Total_Appointments") -> from("Patient_Appointment") -> where("Facility= '$facility'");
		$total = $query -> execute();
		return $total[0]['Total_Appointments'];
	}

	public function getPagedPatientAppointments($offset, $items, $machine_code, $patient_ccc, $facility, $appointment) {
		$query = Doctrine_Query::create() -> select("pa.*") -> from("Patient_Appointment pa") -> leftJoin("Patient_Appointment pa2") -> where("pa2.Patient = '$patient_ccc' and pa2.Machine_Code = '$machine_code' and pa2.Appointment = '$appointment' and pa2.Facility='$facility' and pa.Facility='$facility'") -> offset($offset) -> limit($items);
		$patient_appointments = $query -> execute(array(), Doctrine::HYDRATE_ARRAY);
		return $patient_appointments;
	}

	public function getPagedFacilityPatientAppointments($offset, $items, $facility) {
		$query = Doctrine_Query::create() -> select("*") -> from("Patient_Appointment") -> where("Facility='$facility'") -> offset($offset) -> limit($items);
		$patient_appointments = $query -> execute(array(), Doctrine::HYDRATE_ARRAY);
		return $patient_appointments;
	}
	
	public function getAppointmentDate($patient_ccc){
		$query = Doctrine_Query::create() -> select("*") -> from("Patient_Appointment") -> where("patient = '$patient_ccc'")->orderBy("appointment DESC")->limit("2");
		$patient_appointments = $query -> execute(array(), Doctrine::HYDRATE_ARRAY);
		return $patient_appointments;
	}
	public function get_Appointment_Adherence($start_date,$end_date){
		$sql = "SELECT 
                    pa.appointment,
                    pa.patient,
                    IF(UPPER(rst.Name) ='ART','art','non_art') as service,
        		    IF(UPPER(g.name) ='MALE','male','female') as gender,
        		    IF(FLOOR(DATEDIFF(CURDATE(),p.dob)/365)<15,'<15', IF(FLOOR(DATEDIFF(CURDATE(),p.dob)/365) >= 15 AND FLOOR(DATEDIFF(CURDATE(),p.dob)/365) <= 24,'15_24','>24')) as age
                FROM patient_appointment pa
                LEFT JOIN patient p ON p.patient_number_ccc = pa.patient
                LEFT JOIN regimen_service_type rst ON rst.id = p.service
                LEFT JOIN gender g ON g.id = p.gender 
                WHERE pa.appointment 
                BETWEEN '$start_date'
                AND '$end_date'
                GROUP BY pa.patient,pa.appointment
                ORDER BY pa.appointment";
                
        $query = $this ->db ->query($sql);
        $results = $query -> result_array();
        return $results;
	}


}
