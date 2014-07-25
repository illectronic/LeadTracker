<?php

//require_once('../Connections/public2.php');


//---------------------------
// class:	leadtracker
// author:	stephen R Reid
// written:	Tue July 19, 2011
//
// notes:	Used to perform database
//		operations from a mobile devide.
//---------------------------
class leadtracker {

   private $conn;

   public function __construct($conn) {
       //print "In BaseClass constructor ...leadtracker. <br>\n";
       $this->conn = $conn;
   }




   //---------------------------------
   // function:	fetch()
   // author:	Stephen R Reid
   // written:	Wed July 20, 2011
   //
   // notes:	selects records 
   //		from the time database.
   //---------------------------------
   public function fetch(	$priority_filter, 
				$client_filter, 
				$partner_filter, 
				$search_filter, 
				$search_column, 
				$year_filter, 
				$start_offset, 
				$end_offset, 
				$order_by, 
				$asc_desc, 
				&$data, 
				$maxRecordsToProcess,
				$tmp_end_offset, 
				$fetch_more_data,
				$employee_id,
				$max_records_to_fetch ){



//print "tmp_end_offset: $tmp_end_offset, end offset: $end_offset, orig end offset: $orig_end_offset<br>\n";

      if( !isset( $start_offset )  ){
         //return(false);
      }


      $group_id = '';
      $curs = oci_new_cursor($this->conn);


      $orig_end_offset = $end_offset;
      if( $fetch_more_data ){
         $end_offset = $tmp_end_offset;
      }


      $stid = oci_parse($this->conn, 'begin MobileProcs.s_fetch_leads(:priority_filter,:client_filter,:partner_filter,:search_filter,:search_column,:year_filter,:order_by,:start_offset,:end_offset,:asc_desc,:group_id,:cursor); end;');

      oci_bind_by_name($stid, ':priority_filter', 	$priority_filter );
      oci_bind_by_name($stid, ':client_filter', 	$client_filter );
      oci_bind_by_name($stid, ':partner_filter', 	$partner_filter );
      oci_bind_by_name($stid, ':search_filter',  	$search_filter );
      oci_bind_by_name($stid, ':search_column',  	$search_column );
      oci_bind_by_name($stid, ':year_filter',  		$year_filter );
      oci_bind_by_name($stid, ':order_by', 		$order_by );
      oci_bind_by_name($stid, ':start_offset', 		$start_offset );
      oci_bind_by_name($stid, ':end_offset', 		$end_offset );
      oci_bind_by_name($stid, ':asc_desc', 		$asc_desc );
      oci_bind_by_name($stid, ':group_id', 		$group_id );
      oci_bind_by_name($stid, ':cursor', $curs, -1, OCI_B_CURSOR );
      
     if(! oci_execute($stid)){
        $e = oci_error($stid);
        //echo $e['message'];
     }
      oci_execute($curs);



      $dataFound = false;
      $counter = 0;


      $xml = '';
      //$end_offset = 0;


      while ($row = oci_fetch_assoc($curs)) {

         $dataFound = true;


         //---------------------------------------------------//
         // Initialize variables for each call parent record. //
         //---------------------------------------------------//
  
         $callId       = $row["CALL_ID"];
         $groupId      = $row["GROUP_ID"];
         $createDate   = $row["CREATE_DATE"];
         $editDate     = $row["EDIT_DATE"];
         $callDate     = htmlentities( $row["LEAD_DATE"] );
         $employeeCode = $row["EMPLOYEE_CODE"];
         $employeeFName= $row["EMPLOYEE_FIRST_NAME"];
         $employeeLName= $row["EMPLOYEE_LAST_NAME"];
         $employeeId   = $row["EMPLOYEE_ID"];
         $clientName   = htmlentities( $row["CLIENT_NAME"] );
         $contact      = $row["CONTACT"];
         $contactTitle = htmlentities($row["TITLE"] );
         $subject      = htmlentities($row["PRACTICE"] );
         $location     = $row["LOCATION"];
         $locationId   = $row["LOCATION_ID"];
         $priority     = $row["HEAT"];
         $priorityId   = $row["HEAT_ID"];
         $nextSteps    = htmlentities( $row["NEXT_STEPS"] );
         $notes        = htmlentities( $row["NOTES"] );
         $display      = $row["DISPLAY"];
         $jobCode      = $row["JOB_CODE"];



 	//----------------------------------//
	// Determine if a particular record //
	// has associated child records.    //
 	//----------------------------------//
        $ccurs = oci_new_cursor($this->conn);


        $cstid = oci_parse($this->conn, 'begin MobileProcs.s_fetch_replies(:priority_filter,:client_filter,:partner_filter,:search_filter,:search_column,:order_by,:group_id,:call_id,:start_offset,:end_offset,:asc_desc,:cursor); end;');

      	oci_bind_by_name($cstid, ':priority_filter',   	$priority_filter );
      	oci_bind_by_name($cstid, ':client_filter',    	$client_filter );
      	oci_bind_by_name($cstid, ':partner_filter',    	$partner_filter );
      	oci_bind_by_name($cstid, ':search_filter', 	$search_filter );
      	oci_bind_by_name($cstid, ':search_column', 	$search_column );
      	oci_bind_by_name($cstid, ':order_by',      	$order_by );
      	oci_bind_by_name($cstid, ':group_id',  		$groupId );
      	oci_bind_by_name($cstid, ':call_id',    	$callId );
      	oci_bind_by_name($cstid, ':start_offset',    	$start_offset );
      	oci_bind_by_name($cstid, ':end_offset',    	$end_offset );
      	oci_bind_by_name($cstid, ':asc_desc',    	$asc_desc );
      	oci_bind_by_name($cstid, ':cursor', $ccurs, -1, OCI_B_CURSOR );

      	oci_execute($cstid);
      	oci_execute($ccurs);



	  
 	$child_row = oci_fetch_array($ccurs, OCI_BOTH);
        $hasChildren = count(array_keys( $child_row )) > 0 ? 'true':'false';
  



	//----------------------------------------//
	// Prepare record data for an XML string. //
	//----------------------------------------//
         $callTemplate =<<<CALLTEMPLATE
<call id="$callId" group_id="$groupId">
   <create_date>$createDate</create_date>
   <edit_date>$editDate</edit_date>
   <call_date>$callDate</call_date>
   <employee id="$employeeId" name="$employeeFName $employeeLName">$employeeCode</employee>
   <client>$clientName</client>
   <job_code>$jobCode</job_code>
   <contact name="$contact" title="$contactTitle">.</contact>
   <subject>$subject</subject>
   <location id="$locationId">$location</location>
   <priority id="$priorityId">$priority</priority>
   <next_steps>$nextSteps</next_steps>
   <notes>$notes</notes>
   <display>$display</display>
   <has_replies>$hasChildren</has_replies>
</call>

CALLTEMPLATE;

         $xml .= $callTemplate;
         $counter += 1;
         //$end_offset += 1;

         if( $counter >= $maxRecordsToProcess ){
            break;
         }
        
  
      }


      //------------------------------------//
      // reset end_offset in the event that //
      // "Fetch More Contacts" was called.  //
      //------------------------------------//
      $end_offset = $orig_end_offset;



      //--------------------------------------//
      // If less records are found than what  //
      // should be fetched, reset $end_offset //
      // so that 'start_offset' will be set   //
      // to it's previous value. This is      //
      // important to refresh data in a       //
      // mobile device.                       //
      //--------------------------------------//
      if( $counter < $max_records_to_fetch ){
         $end_offset = $start_offset - 1 ; // added 3.28.2012
      }




      $id = '';
      if( $employee_id != ''){
         $id = " employee_id='$employee_id'";
      }

      $xml .= "</calls>\n";
      $xml = "<?xml version=\"1.0\"?>\n\t<calls start_offset='" . ($end_offset+1) ."'$id max_recs_to_fetch='$max_records_to_fetch'>\n" . $xml;

      if( $dataFound ){
         $data = $xml;
      }
      
      oci_free_statement($stid); 
      oci_free_statement($curs); 

      return(true);


   } // end of fetch()






   //---------------------------------
   // function:	fetchChildren()
   // author:	Stephen R Reid
   // written:	Sat Aug 26, 2011
   //
   // notes:	selects records 
   //		from the officedb database.
   //---------------------------------
   public function fetchChildren(	$priority_filter, 
					$client_filter, 
					$partner_filter, 
					$search_filter, 
					$search_column, 
					$start_offset, 
					$end_offset, 
					$order_by, 
					$asc_desc , 
					&$data, 
					$group_id, 
					$call_id, 
					$maxRecordsToProcess){


      if( !isset( $start_offset )  ){
         return;
      }



      $ccurs = oci_new_cursor($this->conn);

      $cstid = oci_parse($this->conn, 'begin MobileProcs.s_fetch_replies(:priority_filter,:client_filter,:partner_filter,:search_filter,:search_column,:order_by,:group_id,:call_id,:start_offset,:end_offset,:asc_desc,:cursor); end;');

      oci_bind_by_name($cstid, ':priority_filter',	$priority_filter );
      oci_bind_by_name($cstid, ':client_filter',	$client_filter );
      oci_bind_by_name($cstid, ':partner_filter',	$partner_filter );
      oci_bind_by_name($cstid, ':search_filter',	$search_filter );
      oci_bind_by_name($cstid, ':search_column',	$search_column );
      oci_bind_by_name($cstid, ':order_by',      	$order_by );
      oci_bind_by_name($cstid, ':group_id',  	 	$group_id );
      oci_bind_by_name($cstid, ':call_id',       	$call_id );
      oci_bind_by_name($cstid, ':start_offset',  	$start_offset );
      oci_bind_by_name($cstid, ':end_offset',    	$end_offset );
      oci_bind_by_name($cstid, ':asc_desc',    	 	$asc_desc );
      oci_bind_by_name($cstid, ':cursor', $ccurs, -1, OCI_B_CURSOR );

      oci_execute($cstid);
      oci_execute($ccurs);




      $dataFound = false;
      $counter = 0;

      $xml = '';
      while ($row = oci_fetch_assoc($ccurs)) {

         $dataFound = true;

         //---------------------------------------------------//
         // Initialize variables for each call parent record. //
         //---------------------------------------------------//
  
         $callId       = $row["CALL_ID"];
         $groupId      = $row["GROUP_ID"];
         $createDate   = $row["CREATE_DATE"];
         $editDate     = $row["EDIT_DATE"];
         $callDate     = htmlentities( $row["LEAD_DATE"] );
         $employeeCode = $row["EMPLOYEE_CODE"];
         $employeeFName= $row["EMPLOYEE_FIRST_NAME"];
         $employeeLName= $row["EMPLOYEE_LAST_NAME"];
         $employeeId   = $row["EMPLOYEE_ID"];
         $clientName   = htmlentities( $row["CLIENT_NAME"] );
         $contact      = $row["CONTACT"];
         $contactTitle = htmlentities( $row["TITLE"] );
         $subject      = htmlentities( $row["PRACTICE"] );
         $location     = $row["LOCATION"];
         $locationId   = $row["LOCATION_ID"];
         $priority     = $row["HEAT"];
         $priorityId   = $row["HEAT_ID"];
         $nextSteps    = htmlentities( $row["NEXT_STEPS"] );
         $notes        = htmlentities( $row["NOTES"] );
         $display      = $row["DISPLAY"];
         $jobCode      = $row["JOB_CODE"];


         $callTemplate =<<<CALLTEMPLATE
<call id="$callId" group_id="$groupId">
   <create_date>$createDate</create_date>
   <edit_date>$editDate</edit_date>
   <call_date>$callDate</call_date>
   <employee id="$employeeId" name="$employeeFName $employeeLName">$employeeCode</employee>
   <client>$clientName</client>
   <job_code>$jobCode</job_code>
   <contact name="$contact" title="$contactTitle">.</contact>
   <subject>$subject</subject>
   <location id="$locationId">$location</location>
   <priority id="$priorityId">$priority</priority>
   <next_steps>$nextSteps</next_steps>
   <notes>$notes</notes>
   <display>$display</display>
   <has_replies>false</has_replies>
</call>

CALLTEMPLATE;

         $xml .= $callTemplate;

         $counter += 1;

         if( $counter >= $maxRecordsToProcess ){
            break;
         }
  
      }

      if( $counter < $maxRecordsToProcess ){
         //$end_offset = $counter;
      }

      $xml .= "</calls>\n";
      $xml = "<?xml version=\"1.0\"?>\n\t<calls start_offset='" . ($end_offset+1) ."'>\n" . $xml;

      if( $dataFound ){
         $data = $xml;
      }
      
      oci_free_statement($cstid); 
      oci_free_statement($ccurs); 


      return(true);
   }





   //---------------------------------
   // function:	update()
   // author:	Stephen R Reid
   // written:	Wed July 20, 2011
   //
   // notes:	updates a record 
   //		in the time database.
   //---------------------------------
   public function update($callId, $formData){


      $call_id       = $formData['call_id'];
      $call_date     = $formData['call_date'];
      $employee_id   = $formData['employee_id'];
      $client        = $formData['client'];
      $contact       = $formData['contact_name'];
      $title         = $formData['contact_title'];
      $subject       = $formData['subject'];
      $notes         = $formData['notes'];
      $location_id   = $formData['location_id'];
      $priority_id   = $formData['priority_id'];
      $next_steps    = $formData['next_steps'];
      $display       = $formData['display'];
      $job_code      = $formData['job_code'];


      //----------------------------------//
      // If call date has a time appended //
      // to it, remove and user the       //
      // servers time.                    //
      //----------------------------------//
      if( strlen( $call_date ) > 10 ){
         $call_date = substr($call_date, 0, 10 );
      }


      $currentTime = date('H:i:s');
      $call_date .= " $currentTime";



      $query = <<<QUERY
                UPDATE lead_tracker SET CALL_DATE=to_date(:call_date,'MM/DD/YYYY HH24:MI:SS'), CLIENT_NAME=:client, CONTACT=:contact,
                                        TITLE=:title, PRACTICE=:subject, LOCATION_ID=:location_id,
                                        LEVEL_ID=:priority_id, NEXT_STEPS=:next_steps, NOTES=:notes, DISPLAY=:display,
                                        JOB_CODE=:job_code, EDIT_DATE=SYSDATE
                WHERE call_id=:call_id
QUERY;


      $stid = oci_parse($this->conn, $query );

      oci_bind_by_name($stid, ":call_date",	$call_date);
      oci_bind_by_name($stid, ":client",	$client);
      oci_bind_by_name($stid, ":contact",	$contact);
      oci_bind_by_name($stid, ":title",		$title);
      oci_bind_by_name($stid, ":subject",	$subject);
      oci_bind_by_name($stid, ":location_id",	$location_id);
      oci_bind_by_name($stid, ":priority_id",	$priority_id);
      oci_bind_by_name($stid, ":next_steps",	$next_steps);
      oci_bind_by_name($stid, ":notes",		$notes);
      oci_bind_by_name($stid, ":display",	$display);
      oci_bind_by_name($stid, ":job_code",	$job_code);
      oci_bind_by_name($stid, ":call_id",	$call_id);



      //---------------------------------------//
      // Execute query without auto-commit on. //
      //---------------------------------------//
      $r = oci_execute($stid, OCI_NO_AUTO_COMMIT);
      if (!$r) {    
         $e = oci_error($stid);
         oci_rollback($this->conn);  // rollback changes 
         //trigger_error(htmlentities($e['message']), E_USER_ERROR);
         return(false);
      }



      //---------------------//
      // Commit the changes. //
      //---------------------//
      $r = oci_commit($this->conn);
      if (!$r) {
         //$e = oci_error($conn);
         //trigger_error(htmlentities($e['message']), E_USER_ERROR);
         return(false);
      }



      return(true); 

   } // end of update()







   //---------------------------------
   // function:	add()
   // author:	Stephen R Reid
   // written:	Wed July 20, 2011
   //
   // notes:	adds a record to 
   //		the time database.
   //---------------------------------
   public function add($formData){
      //print "<br>add to database...<br>\n";


      $call_date     = $formData['call_date'];
      $employee_id   = $formData['employee_id'];
      $client        = $formData['client'];
      $contact       = $formData['contact_name'];
      $title         = $formData['contact_title'];
      $subject       = $formData['subject'];
      $notes         = $formData['notes'];
      $location_id   = $formData['location_id'];
      $priority_id   = $formData['priority_id'];
      $next_steps    = $formData['next_steps'];
      $display       = $formData['display'];
      $job_code      = $formData['job_code'];
      $group_id      = $formData['group_id'];
   
      $call_id       = $this->get_callId();
      if( $group_id == "" ){
         $group_id = $call_id;
      } 


      //----------------------------------//
      // If call date has a time appended //
      // to it, remove and user the       //
      // servers time.                    //
      //----------------------------------//
      if( strlen( $call_date ) > 10 ){
         $call_date = substr($call_date, 0, 10 );
      }


      $currentTime = date('H:i:s');
      $call_date .= " $currentTime";

      $query = <<<QUERY
            INSERT INTO lead_tracker (CALL_ID, CALL_DATE, EMPLOYEE_ID, CLIENT_NAME, CONTACT, TITLE, PRACTICE,
                                      LOCATION_ID, LEVEL_ID, NEXT_STEPS, NOTES, CREATE_DATE, EDIT_DATE, DISPLAY, GROUP_ID, JOB_CODE )
                             VALUES (:call_id, to_date(:call_date,'MM/DD/YYYY HH24:MI:SS'), :employee_id, :client, :contact, :title, :subject, :location_id,
                                     :priority_id, :next_steps, :notes, SYSDATE, SYSDATE,  :display, :group_id, :job_code )
QUERY;


      $stid = oci_parse($this->conn, $query );

      oci_bind_by_name($stid, ":call_id", $call_id);
      oci_bind_by_name($stid, ":call_date", $call_date);
      oci_bind_by_name($stid, ":employee_id", $employee_id);
      oci_bind_by_name($stid, ":client", $client);
      oci_bind_by_name($stid, ":contact", $contact);
      oci_bind_by_name($stid, ":title", $title);
      oci_bind_by_name($stid, ":subject", $subject);
      oci_bind_by_name($stid, ":location_id", $location_id);
      oci_bind_by_name($stid, ":priority_id", $priority_id);
      oci_bind_by_name($stid, ":next_steps", $next_steps);
      oci_bind_by_name($stid, ":notes", $notes);
      oci_bind_by_name($stid, ":display", $display);
      oci_bind_by_name($stid, ":group_id", $group_id);
      oci_bind_by_name($stid, ":job_code", $job_code);



      //---------------------------------------//
      // Execute query without auto-commit on. //
      //---------------------------------------//
      $r = oci_execute($stid, OCI_NO_AUTO_COMMIT);
      if (!$r) {    
         $e = oci_error($stid);
         oci_rollback($this->conn);  // rollback changes 
         //trigger_error(htmlentities($e['message']), E_USER_ERROR);
//$Handle = fopen("add_test.txt", 'w');
//$data = "call id: $call_id, call date: $call_date, employee id: $employee_id, client: $client, contact: $contact, title: $title, subject: $subject, location id: $location_id, priority id: $priority_id, next steps: $next_steps, notes: $notes, display: $display, group id: $group_id, job code: $job_code";   

//fwrite($Handle, $data);
//fclose($Handle);

         return(false);
      }



      //---------------------//
      // Commit the changes. //
      //---------------------//
      $r = oci_commit($this->conn);
      if (!$r) {
         //$e = oci_error($conn);
         //trigger_error(htmlentities($e['message']), E_USER_ERROR);
         return(false);
      }

      return(true);

   } // end of add()





   //---------------------------------------------
   // function: login()
   // author:   Stephen Reid
   // written:  August 11, 2011
   //
   // notes:    Checks for a valid user/pswd
   //---------------------------------------------
   public function login($username, $password, &$employee_id){


	$ccurs = oci_new_cursor($this->conn);

      	$cstid = oci_parse($this->conn, 'begin MobileProcs.s_login(:username,:password,:cursor); end;');

      	oci_bind_by_name($cstid, ':username',	$username );
      	oci_bind_by_name($cstid, ':password',	md5($password) );
      	oci_bind_by_name($cstid, ':cursor',	$ccurs, -1, OCI_B_CURSOR );

      	oci_execute($cstid);
   	oci_execute($ccurs);




	//-----------------------------------------//
 	// If a match was not found, return false. //
	//-----------------------------------------//
        $row = oci_fetch_assoc($ccurs);
      	if ( ($row === FALSE) || (trim($row["EMPLOYEE_ID"]) == '') ) {
           return(false);
      	}

        $employee_id = $row["EMPLOYEE_ID"];

      	return(true);
   }






   //---------------------------------------------
   // function: get_callId()
   // author:   Stephen Reid
   // written:  March 3rd, 2011
   //
   // notes:    fetch the next available call_id
   //           from the 'lead_tracker' table.
   //---------------------------------------------
   private function get_callId(){

      $query   = 'SELECT lead_tracker_seq.nextval CALL_ID FROM dual';

      $m_auth = ociparse($this->conn, $query);


      //----------------------------//
      // Check if an error occured. //
      //----------------------------//
      if( !ociexecute($m_auth) ){
         return(-1);
      }

      ocifetch($m_auth);

      $call_id = ociresult($m_auth, "CALL_ID");

      return($call_id);

   } // end of get_callId()



} // end of leadtracker class


?>
