<?php

// SPDX-FileCopyrightText: 2021 Andrea Chirulescu <andrea.chirulescu@gmail.com>
// SPDX-FileCopyrightText: 2021 Harald Eilertsen <haraldei@anduin.net>
//
// SPDX-License-Identifier: AGPL-3.0-or-later

if ( !class_exists( 'GiglogAdmin_AdminPage' ) ) {
    require_once __DIR__ . '/../../venue.php';

    class GiglogAdmin_AdminPage {
        public function __construct()
        {
        }

        public static function render_html() : void
        {
            $page = new self();
            $page->render_page();
        }

        public function render_page() : void
        {
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

                <p><?php echo $this->get_filters() ?></p>
                <p><?php echo $this->get_concerts() ?></p>
            </div>
            <?php
            if (current_user_can('administrator'))
                echo(GiglogAdmin_AdminPage::editforms());  //not sure why it doesn't show without the echo?
        }

        private function get_venue_selector( ?GiglogAdmin_Venue $invenue ): string
        {
            return \EternalTerror\ViewHelpers\select_field(
                "selectvenueadmin",
                array_map(fn($venue) => [$venue->id(), $venue->name()], GiglogAdmin_Venue::all_venues()),
                $invenue ? $invenue->id() : null);
        }


        private function get_user( ?int $cid, string $ctype): string
        {
            $hf_user = wp_get_current_user();
            $hf_username = $hf_user->user_login;
            $users = array_map(
                fn($usr) => $usr->user_login,
                get_users( array( 'fields' => array( 'user_login' ) ) ) );

            $current_user = $cid ? GiglogAdmin_Concertlogs::get_assigned_user( $cid, $ctype ) : null;

            return \EternalTerror\ViewHelpers\select_field(
                $ctype,
                array_map( fn($user) => [ $user, $user ], $users ),
                $current_user);
        }


        private function get_filters() : string
        {
            $cty = filter_input(INPUT_POST, 'selectcity', FILTER_SANITIZE_SPECIAL_CHARS);

            $select = '<form method="POST" action="">FILTER DATA:';
            $select .= \EternalTerror\ViewHelpers\select_field(
                "selectcity",
                array_map(fn($city) => [$city, $city], GiglogAdmin_Venue::all_cities()),
                $cty,
                "Select city...");


            if ( !empty($cty) ) {
                //second drop down for venue
                $select .= \EternalTerror\ViewHelpers\select_field(
                    "selectvenue",
                    array_map(
                        fn($venue) => [$venue->id(), $venue->name()],
                        GiglogAdmin_Venue::venues_in_city($cty)
                    ),
                    filter_input(INPUT_POST, 'selectvenue', FILTER_SANITIZE_SPECIAL_CHARS),
                    "Select venue...");
            }
            //option to select own concerts only
            $select .= '<input  class="ownconc" type="checkbox" value="1"';
                if(isset($_POST['my_checkbox'])) $select .=' checked="checked" ';
            $select.=' name="my_checkbox">Show own concerts only</input>';

            $select .= '<input type="submit" value="APPLY"></form>';

            return $select;
        }

        private function editforms(): string
        {
            $cid = filter_input(INPUT_POST, "cid");
            $editing = filter_input(INPUT_POST, "edit") == "EDIT";

            if ($editing && !empty($cid))   //A bit overdoing with the checks if concert ID is empty both here and in find_cid. But based on that, things are NULL or not. Better ideas?
                $c = GiglogAdmin_Concert::get($cid);
            else
                $c = new GiglogAdmin_Concert();

            $content='<div><h3>Form to create/edit concerts and venues</h3><br></div><div class="editform"><div class="concertform">';
            $content.='<form method="POST" action="" class="concert" >'
                .'<div class="concertitems"><strong>CONCERT DETAILS</strong><br><br><fieldset>'
                .'<input type="hidden" name="pid" value="' .$c->id(). '" />'
                .'<label for="cname">Concert Name:</label><textarea id="cname" name="cname" value="'.$c->cname().'">'.$c->cname().'</textarea><br>'
                .'<label for="venue">Venue:</label>' . $this->get_venue_selector($c->venue()) . '<br>'
                .'<label for="cdate">Date:</label><input type="date" id="cdate" name="cdate" value="'.$c->cdate().'"><br>'
                .'<label for="ticket">Tickets:</label><input type="text" id="ticket" name="ticket" value="'.$c->tickets().'"><br>'
                .'<label for="eventurl">Event link:</label><input type="text" id="eventurl" name="eventurl" value="'.$c->eventlink().'"><br>'
                .'</fieldset>';
            // actions differ if we update or create a concert, hence two buttons needed
            if ($editing)
                $content.='<p><input type="submit" name="editconcert" value="Edit Concert"></p>';
            else
                $content.='<p><input type="submit" name="newconcert" value="Create New Concert"></p>';

            $content.='</div>';
            $content.='<div class="useritems"><strong>ASSIGNMENT DETAILS</strong><br><br><fieldset>'
                .'<label for="photo1">Photo1:</label>'.$this->get_user($c->id(),'photo1').'<br>'
                .'<label for="photo2">Photo2:</label>'.$this->get_user($c->id(),'photo2').'<br>'
                .'<label for="rev1">Text1:</label>'.$this->get_user($c->id(),'rev1').'<br>'
                .'<label for="rev2">Text2:</label>'.$this->get_user($c->id(),'rev2').'<br>';

            $content.='<fieldset></div></form></div>';
            $content.='<div class="venueform"><form method="POST" action="" class="venue" ><strong>VENUE DETAILS</strong><br><br>'
                .'<fieldset><label for="venue">Venue Name:</label><input type="text" id="venuename" name="venuename"><br>'
                .'<label for="eventurl">Venue City:</label><input type="text" id="venuecity" name="venuecity"><br>'
                .'<p><input type="submit" name="newvenue" value="Create New Venue"></p>'
                .'<fieldset></form></div>';
            $content.='</div>';
            return $content;
        }

        private function adminactions( int $concert_id ) : string
        {
            global $wpdb;
            $query = "SELECT id,wpgs_name from wpg_pressstatus" ;
            $statuses = $wpdb->get_results($query);

            return
                '<form method="POST" action="">'
                . '<input type="hidden" name="cid" value="' . $concert_id .  '" />'
                . \EternalTerror\ViewHelpers\select_field(
                    'selectstatus',
                    array_map(fn($status) => [ $status->id, $status->wpgs_name ], $statuses),
                    GiglogAdmin_Concertlogs::get_status($concert_id))
                . '<input type="submit" value="SetStatus">'
                . '<input type="submit" name ="edit" value="EDIT">'
                . '</form>';
        }

        //function to calculate if the concert has been added in the past 10 days or before that and show a green NEW for the newest rows
        /**
         * @return null|string
         */
        private function getpublishstatus(int $concert_id)
        {
            global $wpdb;
            $date1 = new DateTime("now");
            $dsql = "select wpgcl_createddate from wpg_concertlogs where wpgcl_concertid=".$concert_id;
            $results = $wpdb->get_results($dsql);
            foreach ( $results AS $row ) {
                //$x = strtotime($row -> filedate);
                $x= date('Y-m-d H:i:s', strtotime($row -> wpgcl_createddate));
                $date2 = new DateTime($x, new DateTimeZone('Europe/London'));
                $dd = $date2 -> diff($date1) ->format("%a");
            }

            if ($dd <= 10) return ('<span style="color:green">NEW</span>');
        }


        private function get_concerts(): string
        {
            $hf_user = wp_get_current_user();
            $hf_username = $hf_user->user_login;
            $roles = $hf_user->roles;
            global $wpdb;

            $content = '<table class="assignit">';
            //    $content .= '</tr><th>CITY</th><th>ID</th><th>BAND</th><th>VENUE</th><th>DATE</th></tr>';

            $content .= '<tr class="assignithrow">
                <th>CITY</th><th>NAME</th><th>VENUE</th><th>DATE</th><th> </th>
                <th>PHOTO1</th><th>PHOTO2</th><th>TEXT1</th><th>TEXT2</th>
                <th>STATUS</th>';
                if (current_user_can('administrator')) //($hf_username == 'etadmin')
                $content .=  '<th>AdminOptions</th>';
                $content .= '</tr>';

            // Use the submitted "city" if any. Otherwise, use the default/static value.
            $cty = filter_input( INPUT_POST, 'selectcity', FILTER_SANITIZE_SPECIAL_CHARS );
            $cty = $cty ? $cty: 'ALL';

            $venue = filter_input( INPUT_POST, 'selectvenue', FILTER_SANITIZE_SPECIAL_CHARS );
            $venue = $venue ? $venue : '0';


            $query =  "SELECT wpgc.id, wpgconcert_name, wpgv.wpgvenue_name as venue, wpgc.wpgconcert_date, wpgc.wpgconcert_tickets, wpgc.wpgconcert_event, wpgv.wpgvenue_city, wpgv.wpgvenue_webpage, wpgps.wpgs_name
                FROM wpg_concerts wpgc,  wpg_venues wpgv, wpg_pressstatus wpgps, wpg_concertlogs wpgcl
                where wpgc.venue = wpgv.id
                and wpgconcert_date >= CURDATE()
                and wpgps.id = wpgcl.wpgcl_status
                and wpgcl.wpgcl_concertid=wpgc.id";

            $query .= ($cty == "ALL") ? "" : "  and wpgv.wpgvenue_city='" .$cty ."'";
            $query .= ($venue == "0") ? "" : "  and wpgv.id='" .$venue ."'";
            $query.= (empty($_POST['my_checkbox'])) ? "": " and (wpgcl_photo1 ='".$hf_username."' or wpgcl_photo2 ='".$hf_username."' or wpgcl_rev1 ='".$hf_username."' or wpgcl_rev2 ='".$hf_username."')";
            $query .=" order by wpgv.wpgvenue_city, wpgconcert_date, wpgc.id" ;
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
                $content .= '<td>' . $row->wpgconcert_name. '</td>';
                $content .= '<td>' . $row->venue. '</td>';
                $fdate =  strtotime($row->wpgconcert_date);
                $newformat = date('d.M.Y',$fdate);

                //$content .= DATE_FORMAT($fdate,'%d.%b.%Y');
                $content .= '<td>' .$newformat. '</td>';
                $content .= '<td>'.$this->getpublishstatus($row->id ).'</td>';
                $content .= '<td>'.$this->returnuser('photo1', $row->id ).'</td>';
                $content .= '<td>'.$this->returnuser('photo2', $row->id ).'</td>';
                $content .= '<td>'.$this->returnuser('rev1', $row->id ).'</td>';
                $content .= '<td>'.$this->returnuser('rev2', $row->id ).'</td>';
                $content .= '<td>'.$row -> wpgs_name.'</td>';

                if (current_user_can('administrator')) {
                    $content .=
                        '<td  class="adminbuttons">'
                        . $this->adminactions($row->id)
                        . '</td>';
                }
                $content .= '</tr>';
                $lastType = $row->wpgvenue_city;
            }
            $content .= '</table>';

            // return the table
            return $content;
        }


        /**
         * @return void
         */
        static function update()
        {
            global $wpdb;

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

            //handling the admin drop down menu
            if(isset($_POST['selectstatus']) && (isset($_POST['edit']) && $_POST['edit']!="EDIT") && !empty($_POST['cid']))
            {
               $usql = "UPDATE wpg_concertlogs  SET wpgcl_status=".$_POST['selectstatus']." WHERE wpgcl_concertid=".$_POST['cid'];
               $uresults = $wpdb->get_results($usql);
               //$url2=$_SERVER['REQUEST_URI'];  //doesn't seem to be needed actually, leaving here just in case
               //header("Refresh: 1; URL=$url2");  //reload page
            }

            if(isset($_POST['newconcert'])) {
                if (empty($_POST['cname'])  || empty($_POST['selectvenueadmin']) || empty($_POST['cdate']) || empty($_POST['ticket']) || empty($_POST['eventurl'])) {
                    echo '<script language="javascript">alert("You are missing a value, concert was not created"); </script>';
                }
                else {
                    if (GiglogAdmin_Concert::create($_POST['cname'], $_POST['selectvenueadmin'], $_POST['cdate'], $_POST['ticket'], $_POST['eventurl'])) {
                        echo '<script language="javascript">alert("Yey, concert created"); </script>';
                    }
                    else {
                        echo '<script language="javascript">alert("Nay, concert was duplicated"); </script>';
                    }
                }
            }

            if(isset($_POST['editconcert']))
            {
            IF (empty($_POST['cname'])  || empty($_POST['selectvenueadmin']) || empty($_POST['cdate']) || empty($_POST['ticket']) || empty($_POST['eventurl']))
                    echo '<script language="javascript">alert("You are missing a value, concert was not updated"); </script>';
            else
                {
                GiglogAdmin_Concert::update_concert($_POST['pid'],$_POST['cname'], $_POST['selectvenueadmin'], $_POST['cdate'], $_POST['ticket'], $_POST['eventurl']);
                GiglogAdmin_Concert::update_concertlog($_POST['pid'],$_POST['photo1'], $_POST['photo2'], $_POST['rev1'], $_POST['rev2']);
                echo '<script language="javascript">alert("Yay, concert updated"); </script>';
                }

            }


            if(isset($_POST['newvenue']))
            {
            IF (empty($_POST['venuename']) || empty($_POST['venuecity']))
                    echo '<script language="javascript">alert("You are missing a value, venue was not created"); </script>';
            else
                {
                GiglogAdmin_Venue::create($_POST['venuename'],$_POST['venuecity']);
                echo '<script language="javascript">alert("Yey, venue created"); </script>';
                }
            }
        }

        static function assignconcert($p1, $c): void
        {
            global $wpdb;

            $hf_user = wp_get_current_user();
            $hf_username = $hf_user->user_login;
            $to = 'live@eternal-terror.com';
            $subject = $hf_username.' has taken '.$p1. 'for a concert with id '.$c;
            $body = 'The email body content';
            $headers = array('Content-Type: text/html; charset=UTF-8');
            $usql = "UPDATE wpg_concertlogs  SET wpgcl_".$p1."='".$hf_username."'  WHERE wpgcl_concertid=".$c;
            $uresults = $wpdb->get_results($usql);
            $wpdb->insert( 'wpg_logchanges', array (
                'id' => '',
                'userid' => $hf_username,
                'action' => 'assigned '.$p1,
                'concertid' => $c));
            echo ($wpdb->last_error );
            wp_mail( $to, $subject, $body, $headers );
        }

        static function unassignconcert($p1, $c): void
        {
            global $wpdb;

            $hf_user = wp_get_current_user();
            $hf_username = $hf_user->user_login;
            $to = 'live@eternal-terror.com';
            $subject = $hf_username.' has UNASSINED  '.$p1. 'for a concert with id '.$c;
            $body = 'The email body content';
            $headers = array('Content-Type: text/html; charset=UTF-8');
            $usql = "UPDATE wpg_concertlogs  SET wpgcl_".$p1."=''  WHERE wpgcl_concertid=".$c;
            $uresults = $wpdb->get_results($usql);
            $wpdb->insert( 'wpg_logchanges', array (
                'id' => '',
                'userid' => $hf_username,
                'action' => 'unassigned '.$p1,
                'concertid' => $c));
            echo ($wpdb->last_error );
            wp_mail( $to, $subject, $body, $headers );
        }

        private function returnuser(string $p1, ?int $c) : ?string
        {
            $hf_user = wp_get_current_user();
            $hf_username = $hf_user->user_login;

            if (!$c) {
                return null;
            }

            $cl = GiglogAdmin_Concertlogs::get($c);
            $role = $cl->get_assigned_role( $hf_username );
            $assigned_user = $cl->assigned_user( $p1 );

            //first check if current slot is taken by current user
            if ( $role == $p1 ) {
                $f = '<form class="unassignit" method="POST" action="">'
                    . '  <input type="hidden" name="cid" value="' . $c. '" />'
                    . '  <input type="hidden" name="pid" value="' . $p1. '" />'
                    . '  <input type="submit" name="unassignitem" value="Your"/>'
                    . '</form>';
            }
            elseif ( $assigned_user ) { //check if slot is taken by another user
                $f = '<span class="takenby">Taken</span>'
                    . '<div class="takenby">Taken by ' . $assigned_user . '</div>';
            }
            elseif ( $role ) {
                // other slots for this concert are taken by user
                $f = '<span class="taken_by_self">-</span>';
            }
            else { //not taken by anyone
                $f = '<form method="POST" action="">'
                    . '  <input type="hidden" name="cid" value="' . $c. '" />'
                    . '  <input type="hidden" name="pid" value="' . $p1. '" />'
                    . '  <input  type="submit" name="assignitem" value=""/>'
                    . '</form>';
            }

            return $f;
        }
    }
}
?>
