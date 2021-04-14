<?php
// SPDX-FileCopyrightText: 2021 Andrea Chirulescu <andrea.chirulescu@gmail.com>
// SPDX-FileCopyrightText: 2021 Harald Eilertsen <haraldei@anduin.net>
//
// SPDX-License-Identifier: AGPL-3.0-or-later

/*
 * code used for giglogadmin for the open page where everyone sees the list of
 * concerts. First function displays filters by city, venue and the second one
 * builds the table with concerts
 */

function giglogadmin_getfilters()
{
    global $wpdb;

    //echo (var_dump($_POST["selectvenue"]));

    $results = $wpdb->get_results('select distinct wpgvenue_city from wpg_venues');
    $select  = '<form method="POST" action=""><select name="selectcity">';
    $select .= '<option value="ALL" ';
    if (isset($_POST["selectcity"]) && $_POST["selectcity"] == "ALL") {
        $select .= ' selected = "selected"';
    }
    $select .= '> All cities</option>';
    foreach ($results AS $row) {
        $select .= '<option value="' . $row->wpgvenue_city . '"';
        if (isset($_POST["selectcity"]) && $_POST["selectcity"] == $row->wpgvenue_city) {
            $select .= ' selected = "selected"';
        }
        $select .= ' >' . $row->wpgvenue_city . '</option>';
    }

    if (isset($_POST["selectcity"]) && $_POST["selectcity"] != "ALL") {
        $select .= '</select>';
        //second drop down for venue

        $vquery = "select id, wpgvenue_name from wpg_venues";
        $vquery .= " where wpgvenue_city='" . $_POST["selectcity"] . "'";
        $resultsv = $wpdb->get_results($vquery);
        $select .= '<select name="selectvenue">';
        $select .= '<option value="0" ';
        if (isset($_POST["selectvenue"]) && $_POST["selectvenue"] == "0") {
            $select .= ' selected = "selected"';
        }
        $select .= '> All venues</option>';

        foreach ($resultsv AS $rowv) {
            $select .= '<option value="' . $rowv->id . '"';
            if (isset($_POST["selectvenue"]) && $_POST["selectvenue"] == $rowv->id) {
                $select .= ' selected = "selected"';
            }
            $select .= ' >' . $rowv->wpgvenue_name . '</option>';
        }
        //end IF that checks if city was selected
    }
    $select .= '</select><input type="submit" value="Filter"></form>';
    return $select;
}


function giglogadmin_getconcerts()
{
    global $wpdb;
    // Shortcodes RETURN content, so store in a variable to return
    $content = '<table class="concertstb">';
    //    $content .= '</tr><th>CITY</th><th>ID</th><th>BAND</th><th>VENUE</th><th>DATE</th><th>TICKETS</th><th>EVENT</th></tr>';
    $content .= '<tr class="concertshrow"><th>CITY</th><th>BAND</th><th>VENUE</th><th>DATE</th><th>TICKETS</th><th>EVENT</th></tr>';
    // Use the submitted "city" if any. Otherwise, use the default/static value.
    $cty = filter_input(INPUT_POST, 'selectcity');
    $cty = $cty ? $cty : 'ALL';

    $venue = filter_input(INPUT_POST, 'selectvenue');
    //echo($_POST['selectvenue']);
    $venue = $venue ? $venue : '0';


    $query = "SELECT wpgc.id, wpgb.wpgband_name as band ,wpgv.wpgvenue_name as venue ,wpgc.wpgconcert_date, wpgc.wpgconcert_tickets, wpgc.wpgconcert_event, wpgv.wpgvenue_city, wpgv.wpgvenue_webpage
 FROM wpg_concerts wpgc, wpg_bands wpgb, wpg_venues wpgv
where wpgc.band=wpgb.id
and wpgc.venue = wpgv.id
and wpgconcert_date >= CURDATE()";
    $query .= ($cty == "ALL") ? "" : "  and wpgv.wpgvenue_city='" . $cty . "'";
    $query .= ($venue == "0") ? "" : "  and wpgv.id='" . $venue . "'";
    $query .= " order by wpgv.wpgvenue_city, wpgconcert_date, wpgc.id";
    //echo($query);
    $results = $wpdb->get_results($query);


    $lastType = '';
    foreach ($results AS $row) {
        $content .= '<tr class="concertsrow">';

        if ($lastType != '' && $lastType != $row->wpgvenue_city) {
            $content .= '<td class="concertstd">' . $row->wpgvenue_city . '</td></tr><tr>';
        }

        if ($lastType == '') {
            $content .= '<td>' . $row->wpgvenue_city . '</td></tr><tr>';
        }
        // Modify these to match the database structure
        //     $content .= '<td>' . $row->id. '</td>';
        $content .= '<td></td>';
        $content .= '<td>' . $row->band . '</td>';
        $content .= '<td>' . $row->venue . '</td>';
        $fdate     = strtotime($row->wpgconcert_date);
        $newformat = date('d.M.Y', $fdate);

        //$content .= DATE_FORMAT($fdate,'%d.%b.%Y');
        $content .= '<td>' . $newformat . '</td>';
        $content .= '<td><a href="' . $row->wpgconcert_tickets . '" target="_blank">Tickets</a></td>';
        $content .= '<td><a href="' . $row->wpgconcert_event . '" target="_blank">Event link</a></td>';
        $content .= '</tr>';
        $lastType = $row->wpgvenue_city;
    }
    $content .= '</table>';
    // return the table
    return $content;
}
