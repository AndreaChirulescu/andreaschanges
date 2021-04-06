<?php
/*
 * code used  for giglogadmin for the page where users such as photographers/concert reviewers
 * check what gigs are available and show interest for them
 * Admin users also control concert statuses here
 */

function giglogadmin_assignconcert($p1, $c)
{
    global $wpdb;

    $hf_user = wp_get_current_user();
    $hf_username = $hf_user->user_login;
    $to = 'live@eternal-terror.com';
    $subject = $hf_username.' has taken '.$p1. 'for a concert with id '.$c;
    $body = 'The email body content';
    $headers = array('Content-Type: text/html; charset=UTF-8');



    if ($p1 == 'photo1') $usql = "UPDATE wpg_concertlogs  SET wpgcl_photo1='".$hf_username."'  WHERE wpgcl_concertid=".$c;
    if ($p1 == 'photo2') $usql = "UPDATE wpg_concertlogs  SET wpgcl_photo2='".$hf_username."'  WHERE wpgcl_concertid=".$c;
    if ($p1 == 'rev1') $usql = "UPDATE wpg_concertlogs  SET wpgcl_rev1='".$hf_username."'  WHERE wpgcl_concertid=".$c;
    if ($p1 == 'rev2') $usql = "UPDATE wpg_concertlogs  SET wpgcl_rev2='".$hf_username."'  WHERE wpgcl_concertid=".$c;

    $uresults = $wpdb->get_results($usql);
    $wpdb->insert( 'wpg_logchanges', array (
        'id' => '',
        'userid' => $hf_username,
        'action' => 'assigned '.$p1,
        'concertid' => $c));
    echo ($wpdb->last_error );
    wp_mail( $to, $subject, $body, $headers );


}

function giglogadmin_unassignconcert($p1, $c)
{
    global $wpdb;

    $hf_user = wp_get_current_user();
    $hf_username = $hf_user->user_login;
    $to = 'live@eternal-terror.com';
    $subject = $hf_username.' has UNASSINED  '.$p1. 'for a concert with id '.$c;
    $body = 'The email body content';
    $headers = array('Content-Type: text/html; charset=UTF-8');



    if ($p1 == 'photo1') $usql = "UPDATE wpg_concertlogs  SET wpgcl_photo1=''  WHERE wpgcl_concertid=".$c;
    if ($p1 == 'photo2') $usql = "UPDATE wpg_concertlogs  SET wpgcl_photo2=''  WHERE wpgcl_concertid=".$c;
    if ($p1 == 'rev1') $usql = "UPDATE wpg_concertlogs  SET wpgcl_rev1='' WHERE wpgcl_concertid=".$c;
    if ($p1 == 'rev2') $usql = "UPDATE wpg_concertlogs  SET wpgcl_rev2=''  WHERE wpgcl_concertid=".$c;


    $uresults = $wpdb->get_results($usql);
    $wpdb->insert( 'wpg_logchanges', array (
        'id' => '',
        'userid' => $hf_username,
        'action' => 'unassigned '.$p1,
        'concertid' => $c));
    echo ($wpdb->last_error );
    wp_mail( $to, $subject, $body, $headers );


}

function giglogadmin_getpublishstatus ($c)
{
    global $wpdb;
    $date1 = new DateTime("now");
    $dsql = "select filedate from wpg_files where wpgc_id=".$c;
    $results = $wpdb->get_results($dsql);
    foreach ( $results AS $row )
    { //$x = strtotime($row -> filedate);
    $x= date('Y-m-d H:i:s', strtotime($row -> filedate));
    $date2 = new DateTime($x, new DateTimeZone('Europe/London'));
    $dd = date_diff ($date1, $date2);
    $datediff = $dd ->format('%d');
    }
    if ($datediff <= 10) return ('<span style="color:green">NEW</span>');
}


function giglogadmin_returnuser($p1, $c)
{
    global $wpdb;
    $hf_user = wp_get_current_user();
    $hf_username = $hf_user->user_login;

    //PHOTO1
    if ($p1 == 'photo1')
    {
        //checking if taken
        $vquery0 = "select wpgcl_photo1 from wpg_concertlogs where wpgcl_concertid=".$c ;
        $results = $wpdb->get_results($vquery0);
        foreach ( $results AS $row )   $x= $row -> wpgcl_photo1;
        if ($x !='' and $x!=$hf_username)  { return ('<span class="takenby">Taken</span><div class="takenby">Taken by '.$x.'</div>'); }
        else
            if  ($x==$hf_username)  //if current user
                return ('<form class="unassignit" method="POST" action=""> <input type="hidden" name="cid" value="' . $c. '" /><input type="hidden" name="pid" value="' . $p1. '" /><input type="submit" name="unassignitem" value=""/>
                </form>');
            else  //not taken by anyone
                return ('<form method="POST" action=""> <input type="hidden" name="cid" value="' . $c. '" /><input type="hidden" name="pid" value="' . $p1. '" /><input  type="submit" name="assignitem" value=""/>
                </form>');
    }
    //PHOTO2
    if ($p1 == 'photo2')
    {
        $vquery0 = "select wpgcl_photo2 from wpg_concertlogs where wpgcl_concertid=".$c ;
        $results = $wpdb->get_results($vquery0);
        foreach ( $results AS $row )   $x= $row -> wpgcl_photo2;
        if ($x !='' and $x!=$hf_username)  { return ('<span class="takenby">Taken</span><div class="takenby">Taken by '.$x.'</div>'); }
        else
            if  ($x==$hf_username)  //if current user
                return ('<form class="unassignit"  method="POST" action=""> <input type="hidden" name="cid" value="' . $c. '" /><input type="hidden" name="pid" value="' . $p1. '" /><input type="submit" name="unassignitem" value=""/>
                </form>');

            else  //not taken by anyone
                return ('<form method="POST" action=""> <input type="hidden" name="cid" value="' . $c. '" /><input type="hidden" name="pid" value="' . $p1. '" /><input type="submit" name="assignitem" value=""/>
                </form>');

    }
    //TEXT1
    if ($p1 == 'rev1')
    {
        $vquery0 = "select wpgcl_rev1 from wpg_concertlogs where wpgcl_concertid=".$c ;
        $results = $wpdb->get_results($vquery0);
        foreach ( $results AS $row )   $x= $row -> wpgcl_rev1;
        if ($x !='' and $x!=$hf_username)  { ('<span class="takenby">Taken</span><div class="takenby">Taken by '.$x.'</div>'); }
        else
            if  ($x==$hf_username)  //if current user
                return ('<form class="unassignit"  method="POST" action=""> <input type="hidden" name="cid" value="' . $c. '" /><input type="hidden" name="pid" value="' . $p1. '" /><input type="submit" name="unassignitem" value=""/>
                </form>');
            else //not taken by anyone
                return ('<form method="POST" action=""> <input type="hidden" name="cid" value="' . $c. '" /><input type="hidden" name="pid" value="' . $p1. '" /><input type="submit" name="assignitem" value=""/>
                </form>');

    }
    //TEXT2
    if ($p1 == 'rev2')
    {
        $vquery0 = "select wpgcl_rev2 from wpg_concertlogs where wpgcl_concertid=".$c ;
        $results = $wpdb->get_results($vquery0);
        foreach ( $results AS $row )   $x= $row -> wpgcl_rev2;
        if ($x !='' and $x!=$hf_username)  { ('<span class="takenby">Taken</span><div class="takenby">Taken by '.$x.'</div>'); }
        else
            if  ($x==$hf_username)  //if current user
                return ('<form class="unassignit"  method="POST" action=""> <input type="hidden" name="cid" value="' . $c. '" /><input type="hidden" name="pid" value="' . $p1. '" /><input type="submit" name="unassignitem" value=""/>
                </form>');
            else //not taken by anyone
                return ('<form method="POST" action=""> <input type="hidden" name="cid" value="' . $c. '" /><input type="hidden" name="pid" value="' . $p1. '" /><input type="submit" name="assignitem" value=""/>
                </form>');

    }


}


function giglogadmin_getfiltersphotog() {
    global $wpdb;

    //echo (var_dump($_POST["selectvenue"]));

    $results = $wpdb->get_results('select distinct wpgvenue_city from wpg_venues');
    $select= '<form method="POST" action=""><select name="selectcity">';
    $select.='<option value="ALL" ';
    if(isset($_POST["selectcity"]) && $_POST["selectcity"] == "ALL")
    { $select.= ' selected = "selected"';}
    $select.='> All cities</option>';
    foreach ( $results AS $row )
    {
        $select.='<option value="'.$row->wpgvenue_city.'"';
        if(isset($_POST["selectcity"]) && $_POST["selectcity"] == $row->wpgvenue_city)
        { $select.= ' selected = "selected"';}
        $select.=' >'. $row->wpgvenue_city.'</option>';
    }

    if(isset($_POST["selectcity"]) && $_POST["selectcity"] != "ALL")
    {
        $select.='</select>';
        //second drop down for venue

        $vquery = "select id, wpgvenue_name from wpg_venues";
        $vquery.= " where wpgvenue_city='".$_POST["selectcity"]."'";
        $resultsv = $wpdb->get_results($vquery);
        $select.= '<select name="selectvenue">';
        $select.='<option value="0" ';
        if(isset($_POST["selectvenue"]) && $_POST["selectvenue"] == "0")
        { $select.= ' selected = "selected"';}
        $select.='> All venues</option>';

        foreach ( $resultsv AS $rowv )
        {
            $select.='<option value="'.$rowv->id.'"';
            if(isset($_POST["selectvenue"]) && $_POST["selectvenue"] == $rowv->id)
            { $select.= ' selected = "selected"';}
            $select.=' >'. $rowv->wpgvenue_name.'</option>';
        }
        //end IF that checks if city was selected
    }
    $select.='</select><input type="submit" value="Filter"></form>';
    return $select;
}


function giglogadmin_getconcertsphotog ( ) {
    $hf_user = wp_get_current_user();
    $hf_username = $hf_user->user_login;
    $roles = ( array ) $hf_user->roles;
    global $wpdb;

    // Shortcodes RETURN content, so store in a variable to return
    $content = '<p>The available slots are marked with the green checkbox.
        If you click on it, it will be assigned to you and if you no longer
        wish to cover that concert, click on the red icon and you will be
        unassigned. A mail should be sent to the admin when this happens,
        but in order for the accreditation request to be sent, you have to
        mail live@eternal-terror.com with the template containing concert
        information. There might be some exceptions, but those are discussed
        case by case. So whenever you want a concert, assign yourself and send
        the template no later than 3 weeks before the concert.</p>

        <p>Admin will try to keep the concert status updated so that you know
        what the accreditation status is. You will get personal message if this
        is really close to the concert date.</p>';

    $content .= '<table class="assignit">';
    //    $content .= '</tr><th>CITY</th><th>ID</th><th>BAND</th><th>VENUE</th><th>DATE</th></tr>';

    $content .= '<tr class="assignithrow">
        <th>CITY</th><th>BAND</th><th>VENUE</th><th>DATE</th><th> </th>
        <th>PHOTO1</th><th>PHOTO2</th><th>TEXT1</th><th>TEXT2</th>
        <th>STATUS</th>'
    if (current_user_can('administrator')) //($hf_username == 'etadmin')
        $content .=  '<th>AdminButtons</th>';
    $content .= '</tr>';

    // Use the submitted "city" if any. Otherwise, use the default/static value.
    $cty= filter_input( INPUT_POST, 'selectcity' );
    $cty= $cty? $cty: 'ALL';

    $venue= filter_input( INPUT_POST, 'selectvenue' );
    //echo($_POST['selectvenue']);
    $venue= $venue? $venue: '0';


    $query =  "SELECT wpgc.id, wpgb.wpgband_name as band, wpgv.wpgvenue_name as venue, wpgc.wpgconcert_date, wpgc.wpgconcert_tickets, wpgc.wpgconcert_event, wpgv.wpgvenue_city, wpgv.wpgvenue_webpage, wpgps.wpgs_name
        FROM wpg_concerts wpgc, wpg_bands wpgb, wpg_venues wpgv, wpg_pressstatus wpgps, wpg_concertlogs wpgcl
        where wpgc.band=wpgb.id
        and wpgc.venue = wpgv.id
        and wpgconcert_date >= CURDATE()
        and wpgps.id = wpgcl.wpgcl_status
        and wpgcl.wpgcl_concertid=wpgc.id";

    $query .= ($cty == "ALL") ? "" : "  and wpgv.wpgvenue_city='" .$cty ."'";
    $query .= ($venue == "0") ? "" : "  and wpgv.id='" .$venue ."'";
    $query .=" order by wpgv.wpgvenue_city, wpgconcert_date" ;
    //echo($query);
    $results = $wpdb->get_results($query);


    $lastType = '';
    foreach ( $results AS $row ) {
        $content .= '<tr class="assignitr">';

        if($lastType != '' && $lastType !=  $row->wpgvenue_city) {
            $content .= '<td>'.$row->wpgvenue_city.'</td></tr><tr>';
        }

        if  ($lastType == '' )
        {$content .= '<td>'.$row->wpgvenue_city.'</td></tr><tr>';
        }
        // Modify these to match the database structure
        //     $content .= '<td>' . $row->id. '</td>';
        $content .= '<td></td>';
        $content .= '<td>' . $row->band. '</td>';
        $content .= '<td>' . $row->venue. '</td>';
        $fdate =  strtotime($row->wpgconcert_date);
        $newformat = date('d.M.Y',$fdate);

        //$content .= DATE_FORMAT($fdate,'%d.%b.%Y');
        $content .= '<td>' .$newformat. '</td>';
        $content .= '<td>'.giglogadmin_getpublishstatus($row->id ).'</td>';
        $content .= '<td>'.giglogadmin_returnuser('photo1', $row->id ).'</td>';
        $content .= '<td>'.giglogadmin_returnuser('photo2', $row->id ).'</td>';
        $content .= '<td>'.giglogadmin_returnuser('rev1', $row->id ).'</td>';
        $content .= '<td>'.giglogadmin_returnuser('rev2', $row->id ).'</td>';
        $content .= '<td  class="adminbuttons">'.$row -> wpgs_name;
        $content .= '</td>';
        if (current_user_can('administrator')) //($hf_username == 'etadmin')
        {   $content .= '<td  class="adminbuttons">';
            $content .= '<span><form method="POST" action=""> <input type="hidden" name="cid" value="' . $row->id.  '" /><input type="submit" name="reqsent" value="REQSENT"/><input type="submit" name="phok" value="PHOK"/><input type="submit" name="txtok" value="TXOK"/><input type="submit" name="allok" value="ALLOK"/><input type="submit" name="rej" value="REJ"/>
            </form></span>';
            $content .= '</td>';
        }    
        $content .= '</tr>';
        $lastType = $row->wpgvenue_city;
    }
    $content .= '</table>';

    if(isset($_POST['assignitem']))
    {
        echo (giglogadmin_assignconcert($_POST['pid'],$_POST['cid']));

        $url2=$_SERVER['REQUEST_URI'];
        header("Refresh: 1; URL=$url2");  //reload page
    }

    if(isset($_POST['unassignitem']))
    {
        echo (giglogadmin_unassignconcert($_POST['pid'],$_POST['cid']));

        $url3=$_SERVER['REQUEST_URI'];
        header("Refresh: 1; URL=$url3");  //reload page
    }

    if(isset($_POST['reqsent']))
    {
        echo (giglogadmin_assignconcert($_POST['pid'],$_POST['cid']));
        $usql = "UPDATE wpg_concertlogs  SET wpgcl_status=2  WHERE wpgcl_concertid=".$_POST['cid'];
        $uresults = $wpdb->get_results($usql);
        $url2=$_SERVER['REQUEST_URI'];
        header("Refresh: 1; URL=$url2");  //reload page
    }

    if(isset($_POST['phok']))
    {
        $usql = "UPDATE wpg_concertlogs  SET wpgcl_status=3  WHERE wpgcl_concertid=".$_POST['cid'];
        $uresults = $wpdb->get_results($usql);

        $url2=$_SERVER['REQUEST_URI'];
        header("Refresh: 1; URL=$url2");  //reload page
    }

    if(isset($_POST['txtok']))
    {
        $usql = "UPDATE wpg_concertlogs  SET wpgcl_status=4  WHERE wpgcl_concertid=".$_POST['cid'];
        $uresults = $wpdb->get_results($usql);

        $url2=$_SERVER['REQUEST_URI'];
        header("Refresh: 1; URL=$url2");  //reload page
    }

    if(isset($_POST['allok']))
    {
        $usql = "UPDATE wpg_concertlogs  SET wpgcl_status=5  WHERE wpgcl_concertid=".$_POST['cid'];
        $uresults = $wpdb->get_results($usql);

        $url2=$_SERVER['REQUEST_URI'];
        header("Refresh: 1; URL=$url2");  //reload page
    }

    if(isset($_POST['rej']))
    {
        $usql = "UPDATE wpg_concertlogs  SET wpgcl_status=6  WHERE wpgcl_concertid=".$_POST['cid'];
        $uresults = $wpdb->get_results($usql);

        $url2=$_SERVER['REQUEST_URI'];
        header("Refresh: 1; URL=$url2");  //reload page
    }

    // return the table
    return $content;
}

function giglogadmin_photographers()
{
    $output = giglogadmin_getfiltersphotog();
    $output .= giglogadmin_getconcertsphotog();

    return $output;
}
