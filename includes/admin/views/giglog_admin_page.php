<?php

// SPDX-FileCopyrightText: 2021 Andrea Chirulescu <andrea.chirulescu@gmail.com>
// SPDX-FileCopyrightText: 2021 Harald Eilertsen <haraldei@anduin.net>
//
// SPDX-License-Identifier: AGPL-3.0-or-later

if ( !class_exists( 'GiglogAdmin_AdminPage' ) ) {
    require_once __DIR__ . '/../../venue.php';

    class GiglogAdmin_AdminPage {
        private string $username;

        const STATUS_LABELS = [
            '',
            'Accred Requested',
            'Photo Approved',
            'Text Approved',
            'Photo and Text Approved',
            'Rejected'
        ];

        public function __construct()
        {
            $this->username = wp_get_current_user()->user_login;
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


        private function user_dropdown_for_role( GiglogAdmin_Concert $concert, string $role): string
        {
            $users = array_map(
                fn($usr) => $usr->user_login,
                get_users( array( 'fields' => array( 'user_login' ) ) ) );

            $roles = $concert->roles();

            $current_user = array_key_exists($role, $roles) ? $roles[$role] : NULL;

            return \EternalTerror\ViewHelpers\select_field(
                $role,
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
                $c = new GiglogAdmin_Concert((object)[]);

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
                .'<label for="photo1">Photo1:</label>'.$this->user_dropdown_for_role($c,'photo1').'<br>'
                .'<label for="photo2">Photo2:</label>'.$this->user_dropdown_for_role($c,'photo2').'<br>'
                .'<label for="rev1">Text1:</label>'.$this->user_dropdown_for_role($c,'rev1').'<br>'
                .'<label for="rev2">Text2:</label>'.$this->user_dropdown_for_role($c,'rev2').'<br>';

            $content.='<fieldset></div></form></div>';
            $content.='<div class="venueform"><form method="POST" action="" class="venue" ><strong>VENUE DETAILS</strong><br><br>'
                .'<fieldset><label for="venue">Venue Name:</label><input type="text" id="venuename" name="venuename"><br>'
                .'<label for="eventurl">Venue City:</label><input type="text" id="venuecity" name="venuecity"><br>'
                .'<p><input type="submit" name="newvenue" value="Create New Venue"></p>'
                .'<fieldset></form></div>';
            $content.='</div>';
            return $content;
        }

        private function adminactions( GiglogAdmin_Concert $concert ) : string
        {
            return
                '<form method="POST" action="">'
                . '<input type="hidden" name="cid" value="' . $concert->id() .  '" />'
                . \EternalTerror\ViewHelpers\select_field(
                    'selectstatus',
                    array_map(fn($i) => [ $i, self::STATUS_LABELS[$i] ], range(1, count(self::STATUS_LABELS) - 1)),
                    $concert->status())
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
            global $wpdb;

            $content = '<table class="assignit">';
            //    $content .= '</tr><th>CITY</th><th>ID</th><th>BAND</th><th>VENUE</th><th>DATE</th></tr>';

            $content .= '<tr class="assignithrow">
                <th>CITY</th><th>NAME</th><th>VENUE</th><th>DATE</th><th> </th>
                <th>PHOTO1</th><th>PHOTO2</th><th>TEXT1</th><th>TEXT2</th>
                <th>STATUS</th>';
                if (current_user_can('administrator'))
                $content .=  '<th>AdminOptions</th>';
                $content .= '</tr>';

            $filter = [];

            // Use the submitted "city" if any. Otherwise, use the default/static value.
            $cty = filter_input( INPUT_POST, 'selectcity', FILTER_SANITIZE_SPECIAL_CHARS );
            if ($cty) $filter['city'] = $cty;

            $venue = filter_input( INPUT_POST, 'selectvenue', FILTER_SANITIZE_SPECIAL_CHARS );
            if ($venue) $filter['venue_id'] = $venue;

            $concerts = GiglogAdmin_Concert::find_concerts($filter);

            $lastType = '';

            foreach ( $concerts AS $concert ) {
                $content .= '<tr class="assignitr">';

                if ($lastType != '' && $lastType !=  $concert->venue()->city()) {
                    $content .= '<td>' . $concert->city() . '</td></tr><tr>';
                }

                if  ($lastType == '' ) {
                    $content .= '<td>' . $concert->venue()->city() . '</td></tr><tr>';
                }
                // Modify these to match the database structure
                //     $content .= '<td>' . $row->id. '</td>';
                $content .= '<td></td>';
                $content .= '<td>' . $concert->cname() . '</td>';
                $content .= '<td>' . $concert->venue()->name() . '</td>';
                $fdate =  strtotime($concert->cdate());
                $newformat = date('d.M.Y',$fdate);

                //$content .= DATE_FORMAT($fdate,'%d.%b.%Y');
                $content .= '<td>' . $newformat . '</td>';
                $content .= '<td>' . /* $concert->isnew() */ '' . '</td>';

                $roles = $concert->roles();

                $content .= '<td>' . $this->assign_role_for_user_form('photo1', $concert) . '</td>';
                $content .= '<td>' . $this->assign_role_for_user_form('photo2', $concert) . '</td>';
                $content .= '<td>' . $this->assign_role_for_user_form('rev1', $concert) . '</td>';
                $content .= '<td>' . $this->assign_role_for_user_form('rev2', $concert) . '</td>';

                $content .= '<td>' . self::STATUS_LABELS[$concert->status()] . '</td>';

                if (current_user_can('administrator')) {
                    $content .=
                        '<td  class="adminbuttons">'
                        . $this->adminactions($concert)
                        . '</td>';
                }
                $content .= '</tr>';
                $lastType = $concert->venue()->city();
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
                $concert = GiglogAdmin_Concert::get(intval($_POST['cid']));
                $role = sanitize_text_field($_POST['pid']);

                if ($concert) {
                    GiglogAdmin_AdminPage::assignconcert($role, $concert);
                }

                $url2=$_SERVER['REQUEST_URI'];
                header("Refresh: 1; URL=$url2");  //reload page
            }

            if(isset($_POST['unassignitem']))
            {
                GiglogAdmin_AdminPage::unassignconcert($_POST['pid'],$_POST['cid']);

                $url3=$_SERVER['REQUEST_URI'];
                header("Refresh: 1; URL=$url3");  //reload page
            }

            // handle the status drop down
            if (isset($_POST['selectstatus']) && !empty($_POST['selectstatus']) && !empty($_POST['cid']))
            {
                if ($_POST['selectstatus'] > 0 && $_POST['selectstatus'] < count(self::STATUS_LABELS)) {
                    $concert = GiglogAdmin_Concert::get(intval($_POST['cid']));
                    $concert->set_status(intval($_POST['selectstatus']));
                    $concert->save();
                }
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
                $roles = array_reduce(
                    ['photo1', 'photo1', 'rev1', 'rev2'],
                    function($roles, $r) {
                        if (isset($_POST[$r])) {
                            $roles[$r] = sanitize_user($_POST[$r]);
                        }
                        return $roles;
                    },
                    []
                );

                $attributes = [
                    'wpgconcert_name' => sanitize_text_field($_POST['cname']),
                    'venue' => intval($_POST['selectvenueadmin']),
                    'wpgconcert_date' => sanitize_text_field($_POST['cdate']),
                    'wpgconcert_ticket' => esc_url_raw($_POST['ticket']),
                    'wpgconcert_event' => esc_url_raw($_POST['eventurl']),
                    'wpgconcert_roles' => $roles,
                ];

                $concert = GiglogAdmin_Concert::get(intval($_POST['pid']));
                if ($concert->update((object) $attributes)) {
                    // let user know the concert was updated.
                    // Look into admin_notices
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

        static function assignconcert($p1, GiglogAdmin_Concert $concert): void
        {
            $username = wp_get_current_user()->user_login;
            $concert->assign_role($p1, $username);
            $concert->save();

            $to = 'live@eternal-terror.com';
            $subject = $username.' has taken '.$p1. 'for a concert with id '.$concert->id();
            $body = 'The email body content';
            $headers = array('Content-Type: text/html; charset=UTF-8');

            wp_mail( $to, $subject, $body, $headers );
        }

        static function unassignconcert($p1, $c): void
        {
            global $wpdb;

            $to = 'live@eternal-terror.com';
            $subject = $this->username.' has UNASSINED  '.$p1. 'for a concert with id '.$c;
            $body = 'The email body content';
            $headers = array('Content-Type: text/html; charset=UTF-8');
            $usql = "UPDATE wpg_concertlogs  SET wpgcl_".$p1."=''  WHERE wpgcl_concertid=".$c;
            $uresults = $wpdb->get_results($usql);
            $wpdb->insert( 'wpg_logchanges', array (
                'id' => '',
                'userid' => $this->username,
                'action' => 'unassigned '.$p1,
                'concertid' => $c));
            echo ($wpdb->last_error );
            wp_mail( $to, $subject, $body, $headers );
        }

        private function assign_role_for_user_form(string $role, GiglogAdmin_Concert $concert) : ?string
        {
            $roles = $concert->roles();
            $assigned_user = array_key_exists($role, $roles) ? $roles[$role] : NULL;

            //first check if current slot is taken by current user
            if ( $assigned_user == $this->username ) {
                $f = '<form class="unassignit" method="POST" action="">'
                    . '  <input type="hidden" name="cid" value="{$cconcert->id()}" />'
                    . '  <input type="hidden" name="pid" value="{$role}" />'
                    . '  <input type="submit" name="unassignitem" value="Your"/>'
                    . '</form>';
            }
            elseif ( $assigned_user ) { //check if slot is taken by another user
                $f = '<span class="takenby">Taken</span>'
                    . '<div class="takenby">Taken by ' . $assigned_user . '</div>';
            }
            elseif ( array_search($this->username, $roles) ) {
                // other slots for this concert are taken by user
                $f = '<span class="taken_by_self">-</span>';
            }
            else { //not taken by anyone
                $f = '<form method="POST" action="">'
                    . '  <input type="hidden" name="cid" value="' . $concert->id() . '" />'
                    . '  <input type="hidden" name="pid" value="' . $role. '" />'
                    . '  <input  type="submit" name="assignitem" value=""/>'
                    . '</form>';
            }

            return $f;
        }
    }
}
?>
