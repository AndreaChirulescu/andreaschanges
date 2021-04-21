<?php
// SPDX-FileCopyrightText: 2021 Andrea Chirulescu <andrea.chirulescu@gmail.com>
// SPDX-FileCopyrightText: 2021 Harald Eilertsen <haraldei@anduin.net>
//
// SPDX-License-Identifier: AGPL-3.0-or-later

/*
 * I kinda overloaded this snippet. Added comments for each function. But this
 * is used in the giglog admin page, which should only be available to admin
 * users. After the file is being uploaded into the concertlists folder, its
 * content is written into wpg_files. Then the content is split into lines and
 * each line is transformed intoa  concert
 */

/* this checks th wpg_files table to see if any file is uploaded but hasn't
 * gone through the processing process - aka fetching each line and
 * transforming it into a concert line
 */
function giglogadmin_getunprocessed()
{
    global $wpdb;

    $content = '<br /><h3> UNPROCESSED ROWS</h3><table class="concertstable">';
    $content .= '<tr class="concertsheaderrow"><th>Filerow</th><th>FILENAME</th><th>DATE</TH><th>UploadedContent</th>';
    $query   = 'SELECT rowid,filename,filedate,rowcontent from wpg_files where processed="N"';
    $results = $wpdb->get_results($query);
    foreach ($results AS $row) {
        $content .= '<tr>';
        $content .= '<td>' . $row->rowid . '</td>';
        $content .= '<td>' . $row->filename . '</td>';
        $content .= '<td>' . $row->filedate . '</td>';
        $content .= '<td>' . $row->rowcontent . '</td>';
        $content .= '</tr>';
    }
    $content .= '</table>';
    return $content;
}

/* function that goes through each line of the unprocessed file. Each line is
 * checked against the concerts table. if it exists - concert and date and
 * venue - it does nothing with it. If it doesn't exist, it checks if band or
 * venue exists. If they don't, they get created, if they do, their ID from
 * their table is fetchd and used in concerts table
 */
function giglogadmin_insertconcerts()
{
    global $wpdb;
    $concertlist = '<p>Inserted the following:</p>';
    $newconcert= [];
    $query1   = 'SELECT id,rowid,filename,filedate,rowcontent from wpg_files where processed="N"';
    $cresults = $wpdb->get_results($query1);
    foreach ($cresults AS $row) {
        $rowfileid   = $row->id;
        $resultArray = explode("\t", $row->rowcontent);
        $cname        = $resultArray[0];
        $venue       = $resultArray[1];
        $condate     = date('Y-m-d', strtotime($resultArray[2]));
        $ticketlink  = $resultArray[3];
        $eventlink   = $resultArray[4];
        //first item in the row should be band $resultArray[0]; second should be venue $resultArray[1]; third should be concert date $resultArray[2];
        //fourth item is ticketlink $resultArray[3];  fifth item is eventlink $resultArray[4];



        //processing venue
        if (is_numeric($venue))
            $newconcert[1] = $venue;
        else {
            $venuesql = 'SELECT id FROM wpg_venues WHERE upper(wpgvenue_name)="' . $venue . '"';
            $results  = $wpdb->get_results($venuesql);
            if ($results)
                $newconcert[1] = $results[0]->id;
            else {
                $wpdb->insert('wpg_venues', array(
                    'id' => '',
                    'wpgvenue_name' => $venue
                ));
                echo ($wpdb->last_error);
                $newconcert[1] = $wpdb->insert_id;
            }
        }
        //done processing venue

        //not sure how to check dates, hopefully manual verification of files will take care of it

        //check if concert already exists and return ID if it does.  Not checking by date, to be considered
        $csql = 'SELECT id from wpg_concerts where wpgconcert_name  = ' . $cname . ' and venue = ' . $newconcert[1] . ' and wpgconcert_date ="' . $condate . '"';

        $cresults = $wpdb->get_results($csql);
        if ($cresults) {
            $usql = 'UPDATE wpg_files SET processed="D", wpgc_id = ' . $cresults[0]->id . ' WHERE id = ' . $rowfileid;

            $uresults = $wpdb->get_results($usql);
            $concertlist .= 'DUPLICATE ROW detected Title ' . $cname . ' with band ID ' . $newconcert[0];
            $concertlist .= ', VENUE ' . $venue . ' with venue ID ' . $newconcert[1];
            $concertlist .= ', CONCERTDATE ' . $condate;
            $concertlist .= ' <br />';
        } else {
            $wpdb->insert('wpg_concerts', array(
                'id' => '',
                'wpgconcert_name' => cname,
                'venue' => $newconcert[1],
                'wpgconcert_date' => $condate,
                'wpgconcert_tickets' => $ticketlink,
                'wpgconcert_event' => $eventlink
            ));
            echo ($wpdb->last_error);
            $newconcertid = $wpdb->insert_id;

            $usql = 'UPDATE wpg_files SET processed="Y", wpgc_id = ' . $newconcertid . ' WHERE id = ' . $rowfileid;

            $uresults = $wpdb->get_results($usql);
            $concertlist .= 'name ' . $cname ;
            $concertlist .= ', VENUE ' . $venue . ' with venue ID ' . $newconcert[1];
            $concertlist .= ', CONCERTDATE ' . $condate . ', Ticket LINK ' . $ticketlink . ', event LINK' . $eventlink;
            $concertlist .= ' <br />';

        }

        //end check if concert exists


        //remember to add the concert ID when displaying


    } //end looping through unprocessed rows

    return $concertlist;
}

function giglogadmin_display_unprocessed() {
    $output = giglogadmin_getunprocessed();

    $output .= '<form method="POST" action=""><input type="submit" name="ProcessConcerts" value="ProcessConcerts"/></form>';


    if (isset($_POST['ProcessConcerts'])) {
        $output .= giglogadmin_insertconcerts();

        //$url2 = $_SERVER['REQUEST_URI'];
        //header("Refresh: 5; URL=$url2"); //reload page
    } //end if button for process concerts is pressed

    return $output;
}
