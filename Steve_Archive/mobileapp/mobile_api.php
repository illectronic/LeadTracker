<?php

include_once('classes/Class.leadtracker.php');


//-------------------------------------------
//
//  Program:	mobile_api.php
//  Author:	stephen r reid
//  Written:	Tue July 19, 2011
//
//  Notes:	This app is part of a web service 
//		for mobile applications. It can be
//		used to fetch data, update data
// 		and add data to a database.
//
//		Each case statement below is to
//		be used for a specific mobile
//		application. A class should also
//		be written to perform database
//		operations for a specific
//		mobile application.
//
//  Usage:	mobile_api.php?appName=leadtracker&action=fetch&partner_filter=34&calls_start_offset=1
//
//-------------------------------------------


//$maxRecordsToProcess = 45;
$maxRecordsToProcess = $max_records_to_fetch = 10;
$start_offset        = $_REQUEST['calls_start_offset'];
$end_offset          = $start_offset + $maxRecordsToProcess - 1;
$tmp_end_offset      = $end_offset;
$fetch_more_data     = false;


$bb_maxRecordsToProcess = $_REQUEST['max_records_to_process'];


if( isset( $bb_maxRecordsToProcess ) && (trim($bb_maxRecordsToProcess) != '') ){
   $maxRecordsToProcess = $bb_maxRecordsToProcess;
   $tmp_end_offset      = $start_offset + $maxRecordsToProcess - 1;
   $fetch_more_data     = true;
}





//--------------------------//
// Get Required parameters. //
//--------------------------//
$appName   = strtolower($_REQUEST['appName']);
$action    = strtolower($_REQUEST['action']);
$login     = strtolower($_REQUEST['login']);

$_REQUEST['md5'] = md5($_REQUEST['password']);

$data = print_r($_REQUEST, true);

$Handle = fopen('amobile_test.txt', 'w');
//fwrite($Handle, $_POST['action']);
fwrite($Handle, $data);
fclose($Handle);


switch ($appName) {

   case 'leadtracker':

      //require_once('Connections/public2.php'); // prod
      require_once('Connections/public3.php'); // dev

      $lt = new leadtracker($public_conn);



      //--------------------------------------------//
      // Attempt to login, if the parameter exists. //
      //--------------------------------------------//
      if( $login == 'login' ){
         $employee_id = '';
         if( !$lt->login( $_REQUEST['username'], $_REQUEST['password'], $employee_id )){
            print 'failure';
            exit(0);
         }
      }



      if( $action == 'fetch' ){

         //--------------------------------//
         // Perform a database fetch here. //
         //--------------------------------//

         $priority_filter = trim( $_REQUEST['priority_filter'] 	);
         $partner_filter  = trim( $_REQUEST['partner_filter']  	);
         $client_filter	  = trim( $_REQUEST['client_filter']   	);
         $year_filter	  = trim( $_REQUEST['year_filter']   	);
         $asc_desc        = trim( $_REQUEST['asc_desc']		);
         $order_by	  = trim( $_REQUEST['order_by']   	);
         $search_filter	  = trim( $_REQUEST['search_filter']   	);
         $search_column	  = trim( $_REQUEST['search_column']   	);


         //--------------------------------//
	 // Set the default year filter to // 
 	 // the last 30 days worth of data //
         //--------------------------------//
         $year_filter = ($year_filter == '')?'1':$year_filter;



         if( $lt->fetch(	$priority_filter, 
				$client_filter, 
				$partner_filter, 
				$search_filter, 
				$search_column, 
				$year_filter, 
				$start_offset, 
				$end_offset, 
				$order_by, 
				$asc_desc, 
				$xml_string, 
				$maxRecordsToProcess,
				$tmp_end_offset,
                                $fetch_more_data,
				$employee_id,
				$max_records_to_fetch )){


            if( trim( $xml_string) == '' ){
               print 'No Data Found';
               exit(0);
            }

            //-----------------------------------------------------------//
            // Disable caching before outputting the xml data to stdout. //
            //-----------------------------------------------------------//
//          header("Cache-Control: no-cache, must-revalidate");	// HTTP/1.1
//          header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");	// Date in the past
            header('Content-type: text/xml');
            echo $xml_string;
            exit(0);
         }


         // failure
         exit(1);



        }elseif( $action == 'update'){

           //---------------------------------//
           // Perform a database update here. //
           //---------------------------------//
           $callId = $_REQUEST['call_id'];
           if( $lt->update($callId, $_REQUEST)){
              // success
              print 'success';
              exit(0);
           }
           // failure
           print 'failure';
           exit(1);



        }elseif( $action == 'add'){

           //------------------------------//
           // Perform a database add here. //
           //------------------------------//
           if( $lt->add($_REQUEST)){
              // success
              print 'success';
              exit(0);
           }

           // failure
           print 'failure';
           exit(1);



        }elseif( $action == 'login'){

           $employee_id = '';
           if( $lt->login($_REQUEST['username'], $_REQUEST['password'], $employee_id )){
              // success
              print 'success;' . $employee_id;
              exit(0);
 	   }
           print 'failure';
           exit(0);


        }elseif( $action == 'fetch_children'){

	   $priority_filter = trim( $_REQUEST['priority_filter']  );
           $partner_filter  = trim( $_REQUEST['partner_filter']   );
           $client_filter   = trim( $_REQUEST['client_filter']    );
           $search_filter   = trim( $_REQUEST['search_filter']   	);
           $search_column   = trim( $_REQUEST['search_column']   	);
           $asc_desc        = trim( $_REQUEST['asc_desc']         );
           $order_by	    = trim( $_REQUEST['order_by']   );
           $group_id 	    = trim( $_REQUEST['call_group_id']);
           $call_id         = trim( $_REQUEST['call_id']);



           if( $lt->fetchChildren(	$priority_filter, 
					$client_filter, 
					$partner_filter, 
					$search_filter, 
					$search_column, 
					$start_offset, 
					$end_offset, 
					$order_by, 
					$asc_desc, 
					$xml_string, 
					$group_id, 
					$call_id, 
					$maxRecordsToProcess)){

              // success
              if( trim( $xml_string) == '' ){
                 print 'no data found';
                 exit(0);
              }

              header('Content-type: text/xml');
              echo $xml_string;
              exit(0);
           }
           exit(1);
	}


        echo "<br>appName is $appName<br>";
        break;


    case '':
        echo "";
        break;


    case '':
        echo "";
        break;
}




exit(0);


?>

