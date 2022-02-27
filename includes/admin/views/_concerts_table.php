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

        const FILTER_KEYS = [
            'city',
            'venue',
            'month',
            'only_mine'
        ];

        private string $username;

        public function __construct() {
            $this->username = wp_get_current_user()->user_login;
            $this->get_args();
        }

        public function render(): string
        {
            return $this->render_filters()
            . $this->render_concerts_table();
        }

        private function render_concert_table_header() : string
        {


            $content =                '<div style="overflow-x:auto;"><table class="assignit">';
            $content.= '<span style="font-size:0.8em;font-style: italic;"> Note: the iCal link will download a file with extension .ical which can be used to add the event to your calendar. For convenience, we set all events with start time at 19:00 but please check the actual event for the correct time.</span>';

            $content.=  '<tr class="assignithrow">';
            $content.= '<th>CITY</th><th>DATE</th><th>NAME</th><th>VENUE</th>';

            if (!is_admin()) {
                $content .= '<th>EVENT</th><th>TICKETS</th><th>Calendar</th>';
            }
            else {
                $content .= '<th></th><th>PHOTO1</th><th>PHOTO2</th><th>TEXT1</th><th>TEXT2</th><th>STATUS</th>';
                if (current_user_can('administrator')) {
                    $content .= '<th>AdminOptions</th>';
                }
            }

            $content .= '</tr>';

            return $content;
        }

        private function get_args() : void
        {
            $this->filter = [];

            // Use the submitted "city" if any. Otherwise, use the default/static value.
            $cty = filter_input( INPUT_GET, 'city', FILTER_SANITIZE_SPECIAL_CHARS );
            if ($cty) {
                $this->filter['city'] = $cty;
            }

            $venue = filter_input( INPUT_GET, 'venue', FILTER_SANITIZE_SPECIAL_CHARS );
            if ($venue) {
                $this->filter['venue_id'] = $venue;
            }

            $smonth = filter_input( INPUT_GET, 'month', FILTER_SANITIZE_SPECIAL_CHARS );
            if ($smonth) {
                $this->filter['month'] = $smonth;
            }

            if(isset($_GET['only_mine']) && $_GET['only_mone'] == '1') {
                $this->filter['currentuser'] = $this->username;
            }

            if (isset($_GET['page_no']) && $_GET['page_no'] != "" && is_numeric($_GET['page_no']) && isset($_GET['page_no']) == $this->page_no  ) {
                $this->page_no = intval($_GET['page_no']);
            } else {
                $this->page_no = 1;
            }
        }

        private function get_concerts() : ?array
        {
            $total_records_per_page = 15;

            $total_concerts = GiglogAdmin_Concert::count( $this->filter );
            $this->total_no_of_pages = ceil( $total_concerts / $total_records_per_page );

            //calculate OFFSET Value and SET other Variables
            $offset = ($this->page_no - 1) * $total_records_per_page;
            $this->previous_page = $this->page_no - 1;
            $this->next_page = $this->page_no + 1;


            if ($this->page_no > $this->total_no_of_pages ) {
                $this->page_no = 1;
            }

            $this->filter['offset'] =  $offset;
            $this->filter['recperpage'] =  $total_records_per_page;

            return GiglogAdmin_Concert::find_concerts($this->filter);
        }

        private function get_filter(string $f) : ?string
        {
            return isset( $this->filter[$f] ) ? $this->filter[$f] : null;
        }

        private function render_pagination() : string
        {
            $content =
            '<div id="pagtextbox" style="display:flex">'
            . '<span class="alignleft" style="text-align:left;flex:auto;">';

            if($this->page_no > 1) {
                $content .=
                '<span>'
                . '<a href="'. add_query_arg( 'page_no', 1 ) . '">'
                . 'First Page</a> -'
                . '</span>'
                . '<span>'
                . '<a href="' . add_query_arg( 'page_no', $this->previous_page ) . '">'
                . ' Previous</a></span>';
            }

            $content .= '</span>'
            . '<span class="aligncenter" style="text-align:center;flex:auto">'
            . '<strong>Page ' . $this->page_no . ' of ' . $this->total_no_of_pages . '</strong>'
            . '</span>';

            $content .= '<span class="alignright" style="text-align:right;flex:auto;float:none">';

            if ($this->page_no < $this->total_no_of_pages) {
                $content .=
                '<span>'
                . '<a href="' . add_query_arg( 'page_no', $this->next_page ) . '">'
                . 'Next</a> - '
                . '</span>'
                . '<span>'
                . '<a href="' . add_query_arg( 'page_no', $this->total_no_of_pages ) .'">'
                . 'Last Page</a>'
                . '</span>';
            }

            $content .=
            '</span>'
            . '</div>';

            return $content;
        }

        private function render_concerts_table() : string
        {
            //pagination. Change value as needed

            $concerts = $this->get_concerts();

            $lastType = '';

            $content = $this->render_concert_table_header();

            foreach ( $concerts AS $concert ) {
                $content .= '<tr class="assignitr">';

                if ($lastType != '' && $lastType !=  $concert->venue()->city()) {
                    $content .= '<td>' . $concert->venue()->city() . '</td>';
                }

                if  ($lastType == '' ) {
                    $content .= '<td>' . $concert->venue()->city() . '</td>';
                }

                if  ($lastType != '' && $lastType ==  $concert->venue()->city()) {
                    $content .= '<td></td>';
                }

                $fdate =  strtotime($concert->cdate());
                $newformat = date('d.M.Y',$fdate);
                //$content .= DATE_FORMAT($fdate,'%d.%b.%Y');
                $content .= '<td>' . $newformat . '</td>';
                $content .= '<td>'. $concert->cname() . '</td>';
                $content .= '<td>' . $concert->venue()->name() . '</td>';
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
                        $content .= '<td  class="adminbuttons">'
                        . $this->adminactions($concert)
                        . '</td>';
                    }
                }
                $content .= '<td> <a href="'.get_admin_url().'admin-ajax.php?action=giglog_export_ical&evid='.$concert->id().'">iCal</td>';
                $content .= '</tr>';
                $lastType = $concert->venue()->city();
            }

            $content .= '</table>';

            $content .= $this->render_pagination();

            //from main form that includes filters
            $content .= '</div></form></p>';

            // return the table
            return $content;
        }

        private function render_filters() : string
        {
            global $wp_locale;

            $select = '<p><form method="GET" action="" class="filterclass">FILTER DATA:  ';

            foreach( $_GET as $name => $val ) {
                if ( in_array( $name, self::FILTER_KEYS ) ) {
                    continue;
                }

                $select .= '<input type="hidden" name="' . esc_attr( $name )
                . '" value="' . esc_attr( $val ) . '">';
            }

            $cty = $this->get_filter('city');

            $select .= \EternalTerror\ViewHelpers\select_field(
                "city",
                array_map(fn($city) => [$city, $city], GiglogAdmin_Venue::all_cities()),
                $cty,
                "Select city...");


                if ( !empty($cty) ) {
                    //second drop down for venue
                    $select .= \EternalTerror\ViewHelpers\select_field(
                        "venue",
                        array_map(
                            fn($venue) => [$venue->id(), $venue->name()],
                            GiglogAdmin_Venue::venues_in_city($cty)
                        ),
                        $this->get_filter('venue_id'),
                        "Select venue...");
                    }

                    $select .= \EternalTerror\ViewHelpers\select_field(
                        "month",
                        array_map(
                            fn($m) => [ $m, $wp_locale->get_month( $m ) ],
                            range( 1, 12 )
                        ),
                        $this->get_filter('month'),
                        "Select month...");

                        $select.='</select>';

                        if(is_admin()) {
                            //option to select own concerts only
                            $select .= '<input name="only_mine" class="ownconc" type="checkbox" value="1"'
                            . checked( $this->get_filter( 'current_user' ) )
                            . '><label for="ownconcerts">Show own concerts only</label>';

                        }
                        //NOTE that I remvoed </form></p> and mvoed them up to render_concerts_table function
                        $select .= '<input class="applybutton" type="submit" value="Apply Filters">';

                        return $select;
                    }

                    private function adminactions( GiglogAdmin_Concert $concert ) : string
                    {
                        return
                        '<form class="adminactions" method="POST" action="">'
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
                                $f = '<form class="unassign_concert" method="POST" action="">'
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
                                $f = '<form class="assign_concert" method="POST" action="">'
                                . '  <input type="hidden" name="cid" value="' . $concert->id() . '" />'
                                . '  <input type="hidden" name="pid" value="' . $role. '" />'
                                . '  <input  type="submit" name="assignitem" value=""/>'
                                . '</form>';
                            }

                            return $f;
                        }
                    }
                }
