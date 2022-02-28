<?php

/*******************************************************************************
  GS Lineups
*******************************************************************************/

  $src = new scraper();
  $src->set_params('GOV','Gas','http://theprem.ws/db/climate.json');

  if ($src->check_counter()) {
          
    // check GW deadline
    $src->updated = true;

    // if updated, load data array
    if ($src->updated) {    
      $src->add_data('REPORT_DATE',date("Y-m-d"));
      $src->add_data('CODE',$src->state);

      // postcode
      $src->scan();         
      $json = json_decode($src->page);
      $table = $json->results[0]->result->data->dsr->DS[0]->PH[0]->DM0;      
      $csv = array();

      foreach($table as $row) {   

        $array = array(
          "Quarter" => date("Y-m-d", $row->G0 / 1000),
          "1a. Electricity" => $row->X[0]->M0,
          "1b. Stationary Energy" => $row->X[1]->M0,
          "1c. Transport" => $row->X[2]->M0,
          "1d. Fugitive Emissions" => $row->X[3]->M0,
          "2. Industrial Processes" => $row->X[4]->M0,
          "3. Agriculture" => $row->X[5]->M0,
          "4. LULUCF" => $row->X[6]->M0,
          "5. Waste" => $row->X[7]->M0
        );
        array_push($csv,$array);

      }

      $src->page = json_encode($csv);
      $src->json_table('wrk_gov_gas');

    } else {
      // pending message
      $src->pending_notification();
    }
    
  }

?>  