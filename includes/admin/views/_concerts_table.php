    <?php
    // SPDX-FileCopyrightText: 2021 Andrea Chirulescu <andrea.chirulescu@gmail.com>
    // SPDX-FileCopyrightText: 2021 Harald Eilertsen <haraldei@anduin.net>
    //
    // SPDX-License-Identifier: AGPL-3.0-or-later

    if (!class_exists("GiglogAdmin_ConcertsTable"))
    {
        class GiglogAdmin_ConcertsTable
        {
            const STATUS_LABELS = [
                '',
                'Accred Requested',
                'Photo Approved',
                'Text Approved',
                'Photo and Text Approved',
                'Rejected'
            ];

            private string $username;

            public function __construct() {
                $this->username = wp_get_current_user()->user_login;
            }

            public function render(): string
            {
                return $this->render_filters()
                    . $this->render_concerts_table();
            }

            private function render_concerts_table() : string
            {
                $content = '<table class="assignit">';
                //    $content .= '</tr><th>CITY</th><th>ID</th><th>BAND</th><th>VENUE</th><th>DATE</th></tr>';

                $content .= '<tr class="assignithrow"><th>CITY</th><th>NAME</th><th>VENUE</th><th>DATE</th>';
                 if(!is_admin())
                  $content .= '<th>EVENT</th><th>TICKETS</th>';

                  else
                  {
                      $content .= '<th> </th><th>PHOTO1</th><th>PHOTO2</th><th>TEXT1</th><th>TEXT2</th><th>STATUS</th>';
                  if (current_user_can('administrator'))
                     $content .=  '<th>AdminOptions</th>';
                  }
                    $content .= '</tr>';

                $filter = [];

                // Use the submitted "city" if any. Otherwise, use the default/static value.
                $cty = filter_input( INPUT_POST, 'selectcity', FILTER_SANITIZE_SPECIAL_CHARS );
                if ($cty) $filter['city'] = $cty;

                $venue = filter_input( INPUT_POST, 'selectvenue', FILTER_SANITIZE_SPECIAL_CHARS );
                if ($venue) $filter['venue_id'] = $venue;

                if(isset($_POST['ownconcerts']) &&  $_POST['ownconcerts'] == '1')
            $filter['currentuser'] = wp_get_current_user()->user_login;

                $concerts = GiglogAdmin_Concert::find_concerts($filter);

                $lastType = '';

                foreach ( $concerts AS $concert ) {
                    $content .= '<tr class="assignitr">';

                    if ($lastType != '' && $lastType !=  $concert->venue()->city()) {
                        $content .= '<td>' . $concert->venue()->city() . '</td>">';
                    }

                    if  ($lastType == '' ) {
                        $content .= '<td>' . $concert->venue()->city() . '</td>';
                    }
                    // Modify these to match the database structure
                    //     $content .= '<td>' . $row->id. '</td>';
                    //     $content .= '<td>' . $row->id. '</td>';
                    if  ($lastType != '' && $lastType ==  $concert->venue()->city()) $content .= '<td></td>';
                    $content .= '<td>'. $concert->cname() . '</td>';
                    $content .= '<td>' . $concert->venue()->name() . '</td>';
                    $fdate =  strtotime($concert->cdate());
                    $newformat = date('d.M.Y',$fdate);

                    //$content .= DATE_FORMAT($fdate,'%d.%b.%Y');
                        $content .= '<td>' . $newformat . '</td>';
                    if(!is_admin()){
                    $content .= '<td><a target="_blank" href="'.$concert->eventlink() .'">Link</a></td>';
                    $content .= '<td><a target="_blank" href="'.$concert->tickets() .'">Tickets</a></td>';

                    }
                    else {
                        $content .= '<td class="publishstatus">' . $this->mark_new_concert($concert) . '</td>';

                        $content .= '<td class="assigneduser">' . $this->assign_role_for_user_form('photo1', $concert) . '</td>';
                        $content .= '<td class="assigneduser">' . $this->assign_role_for_user_form('photo2', $concert) . '</td>';
                        $content .= '<td class="assigneduser">' . $this->assign_role_for_user_form('rev1', $concert) . '</td>';
                        $content .= '<td class="assigneduser">' . $this->assign_role_for_user_form('rev2', $concert) . '</td>';

                        $content .= '<td>' . self::STATUS_LABELS[$concert->status()] . '</td>';

                        if (current_user_can('administrator')) {
                            $content .=
                            '<td  class="adminbuttons">'
                            . $this->adminactions($concert)
                            . '</td>';
                        }
                    }
                    $content .= '</tr>';
                    $lastType = $concert->venue()->city();
                }
                $content .= '</table>';

                // return the table
                return $content;
            }

            private function render_filters() : string
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
                if(is_admin()) {
                //option to select own concerts only
                $select .= '<input name="ownconcerts" class="ownconc" type="checkbox" value="1"'
                    . checked(isset($_POST['ownconcerts']) ? $_POST['ownconcerts'] : false)
                    . '><label for="ownconcerts">Show own concerts only</label>';
                }
                $select .= '<input type="submit" value="APPLY"></form>';

                return $select;
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

            /**
             * Display a mark on the concert if it is new.
             * I.e. imported/created within the last ten days.
             *
             * @return null|string
             */
            private function mark_new_concert(GiglogAdmin_Concert $concert) : string
            {
                $now = new DateTime();
                $new_entry = $now->diff($concert->created())->days <= 10;
                if ($new_entry) {
                    return '<span style="color:green">NEW</span>';
                }
                else {
                    return '';
                }
            }

            private function assign_role_for_user_form(string $role, GiglogAdmin_Concert $concert) : ?string
            {
                $roles = $concert->roles();
                $assigned_user = array_key_exists($role, $roles) ? $roles[$role] : NULL;

                //first check if current slot is taken by current user
                if ( $assigned_user == $this->username ) {
                    $f = '<form class="unassignit" method="POST" action="">'
                        . '  <input type="hidden" name="cid" value="' . $concert->id() . '" />'
                        . '  <input type="hidden" name="pid" value="' . $role . '" />'
                        . '  <input type="submit" name="unassignitem" value=""/>'
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
