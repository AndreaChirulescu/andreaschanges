<?php
/*
 * Copyright (C) 2021 Harald Eilertsen, Andrea Chirulescu
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

if ( !class_exists( 'GiglogAdmin_AdminPage' ) ) {
    require_once __DIR__ . '/../../venue.php';

    class GiglogAdmin_AdminPage {
        static function render_html() {
            ?>
            <div class="wrap">
                <h1>Giglog Admin</h1>

                <p>The available slots are marked with the green checkbox.
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
                is really close to the concert date.</p>

                <p><?php echo GiglogAdmin_AdminPage::get_filters() ?></p>
                <p><?php echo GiglogAdmin_AdminPage::get_concerts() ?></p>
            </div>
            <?php
        }

        static function get_filters()
        {
            $cities = array_merge(["ALL"], GiglogAdmin_Venue::all_cities());
            $selected_city =
                filter_input(INPUT_POST, "selectcity", FILTER_SANITIZE_SPECIAL_CHARS)
                || $cities[0];

            $select = '<form method="POST" action=""><select name="selectcity">';

            foreach ( $cities AS $city ) {
                $select .= '<option value="' . $city . '"' . selected($city, $selected_city) . '>';
                $select .= $city . '</option>';
            }

            $select .= '</select>';

            if ( $selected_city != "ALL" ) {
                //second drop down for venue

                $venues = array_merge([[0, "ALL"]], GiglogAdmin_Venue::venues_in_city($selected_city));
                $selected_venue =
                    filter_input(INPUT_POST, "selectvenue", FILTER_SANITIZE_SPECIAL_CHARS)
                    || $venues[0];

                $select .= '<select name="selectvenue">';

                foreach ( $venues AS $venue ) {
                    $select .= '<option value="' . $venue[0] . '"' . selected($venue, $selected_venue) . '>';
                    $select .= $venue[1] . '</option>';
                }

                $select .= '</select>';
            }

            $select .= '<input type="submit" value="Filter"></form>';

            return $select;
        }

        static function get_concerts()
        {
            $hf_user = wp_get_current_user();
            $hf_username = $hf_user->user_login;
            $roles = ( array ) $hf_user->roles;
            global $wpdb;

            $content = '<table class="assignit">';
            //    $content .= '</tr><th>CITY</th><th>ID</th><th>BAND</th><th>VENUE</th><th>DATE</th></tr>';

            $content .= '<tr class="assignithrow">
                <th>CITY</th><th>BAND</th><th>VENUE</th><th>DATE</th><th> </th>
                <th>PHOTO1</th><th>PHOTO2</th><th>TEXT1</th><th>TEXT2</th>
                <th>STATUS</th></tr>';

            // Use the submitted "city" if any. Otherwise, use the default/static value.
            $cty = filter_input( INPUT_POST, 'selectcity', FILTER_SANITIZE_SPECIAL_CHARS );
            $cty = $cty ? $cty: 'ALL';

            $venue = filter_input( INPUT_POST, 'selectvenue', FILTER_SANITIZE_SPECIAL_CHARS );
            $venue = $venue ? $venue : '0';


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
            $results = $wpdb->get_results($query);

            $lastType = '';
            foreach ( $results AS $row ) {
                $content .= '<tr class="assignitr">';

                if($lastType != '' && $lastType !=  $row->wpgvenue_city) {
                    $content .= '<td>'.$row->wpgvenue_city.'</td></tr><tr>';
                }

                if  ($lastType == '' ) {
                    $content .= '<td>'.$row->wpgvenue_city.'</td></tr><tr>';
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
                $content .= '<td></td>'; //.giglogadmin_getpublishstatus($row->id ).'</td>';
                $content .= '<td>'.GiglogAdmin_AdminPage::returnuser('photo1', $row->id ).'</td>';
                $content .= '<td>'.GiglogAdmin_AdminPage::returnuser('photo2', $row->id ).'</td>';
                $content .= '<td>'.GiglogAdmin_AdminPage::returnuser('rev1', $row->id ).'</td>';
                $content .= '<td>'.GiglogAdmin_AdminPage::returnuser('rev2', $row->id ).'</td>';
                $content .= '<td  class="adminbuttons">'.$row -> wpgs_name;
                if (current_user_can('administrator')) //($hf_username == 'etadmin')
                    $content .= '<span><form method="POST" action=""> <input type="hidden" name="cid" value="' . $row->id.  '" /><input type="submit" name="reqsent" value="REQSENT"/><input type="submit" name="phok" value="PHOK"/><input type="submit" name="txtok" value="TXOK"/><input type="submit" name="allok" value="ALLOK"/><input type="submit" name="rej" value="REJ"/>
                    </form></span>';
                $content .= '</td>';
                $content .= '</tr>';
                $lastType = $row->wpgvenue_city;
            }
            $content .= '</table>';


            // return the table
            return $content;
        }

        static function update()
        {
            if ('POST' !== $_SERVER['REQUEST_METHOD'])
                return;

            // Use the submitted "city" if any. Otherwise, use the default/static value.
            $cty = filter_input( INPUT_POST, 'selectcity', FILTER_SANITIZE_SPECIAL_CHARS );
            $cty = $cty ? $cty: 'ALL';

            $venue = filter_input( INPUT_POST, 'selectvenue', FILTER_SANITIZE_SPECIAL_CHARS );
            $venue = $venue ? $venue : '0';

            if(isset($_POST['assignitem']))
            {
                GiglogAdmin_AdminPage::assignconcert($_POST['pid'],$_POST['cid']);

                $url2=$_SERVER['REQUEST_URI'];
                header("Refresh: 1; URL=$url2");  //reload page
            }

            if(isset($_POST['unassignitem']))
            {
                GiglogAdmin_AdminPage::unassignconcert($_POST['pid'],$_POST['cid']);

                $url3=$_SERVER['REQUEST_URI'];
                header("Refresh: 1; URL=$url3");  //reload page
            }

            if(isset($_POST['reqsent']))
            {
                GiglogAdmin_AdminPage::assignconcert($_POST['pid'],$_POST['cid']);
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
        }

        static function assignconcert($p1, $c)
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

        static function unassignconcert($p1, $c)
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

        static function returnuser($p1, $c)
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
    }
}
?>
