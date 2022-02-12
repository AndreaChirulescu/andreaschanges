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
            $content = '<div style="overflow-x:auto;"><table class="assignit">';
            //    $content .= '</tr><th>CITY</th><th>ID</th><th>BAND</th><th>VENUE</th><th>DATE</th></tr>';

            $content .= '<tr class="assignithrow"><th>CITY</th><th>DATE</th><th>NAME</th><th>VENUE</th>';

            if (!is_admin()) {
                $content .= '<th>EVENT</th><th>TICKETS</th>';
            }
            else {
                $content .= '<th> </th><th>PHOTO1</th><th>PHOTO2</th><th>TEXT1</th><th>TEXT2</th><th>STATUS</th>';
                if (current_user_can('administrator')) {
                    $content .=  '<th>AdminOptions</th>';
                }
            }
            $content .= '</tr>';

            //pagination. Change value as needed
            $total_records_per_page = 15;



            $filter = [];

            // Use the submitted "city" if any. Otherwise, use the default/static value.
            $cty = filter_input( INPUT_POST, 'selectcity', FILTER_SANITIZE_SPECIAL_CHARS );
            if ($cty) {
                $filter['city'] = $cty;
            }

            $venue = filter_input( INPUT_POST, 'selectvenue', FILTER_SANITIZE_SPECIAL_CHARS );
            if ($venue) {
                $filter['venue_id'] = $venue;
            }

            $smonth = filter_input( INPUT_POST, 'selectmonth', FILTER_SANITIZE_SPECIAL_CHARS );
            if ($smonth) {
                $filter['month'] = $smonth;
            }

            if(isset($_POST['ownconcerts']) && $_POST['ownconcerts'] == '1') {
                $filter['currentuser'] = wp_get_current_user()->user_login;
            }

            $concerts = GiglogAdmin_Concert::find_concerts($filter);


            //get number of pages for pagination
            $total_records = count($concerts);
            $total_no_of_pages = ceil($total_records / $total_records_per_page);
            $second_last = $total_no_of_pages - 1; // total pages minus 1

            if (isset($_GET['page_no']) && $_GET['page_no']!="" && $_GET['page_no']<=$total_no_of_pages) {
                $page_no = $_GET['page_no'];
            } else {
                $page_no = 1;
            }
            //calculate OFFSET Value and SET other Variables
            $offset = ($page_no-1) * $total_records_per_page;
            $previous_page = $page_no - 1;
            $next_page = $page_no + 1;
            $adjacents = "2";

            $filter['offset'] =  $offset;
            $filter['recperpage'] =  $total_records_per_page;

            $concertsp = GiglogAdmin_Concert::find_concerts($filter);


            $lastType = '';

            foreach ( $concertsp AS $concert ) {
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
                $content .= '</tr>';
                $lastType = $concert->venue()->city();
            }

            $content .= '</table>';

            $content.='<div id="pagtextbox">';
            $content.='<span class="alignleft">';

            if($page_no > 1) {
                $content.= '<span><a href="'. add_query_arg( 'page_no', 1, get_permalink() ) . '">First Page</a> - </span>';
            }

            if($page_no <= 1) {
                $content .="<span> </span>";
            }
            else {
                $content.= '<span> <a href="' . add_query_arg( 'page_no', $previous_page, get_permalink() ) . '">Previous</a></span>';
            }

            $content.='</span>';
            $content.='<span class="aligncenter"><div style="padding: 10px 20px 0px; border-top: dotted 1px #CCC;"><strong>Page '.$page_no.' of '.$total_no_of_pages.'</strong></div></span>';
            $content.='<span class="alignright">';

            if ($page_no >= $total_no_of_pages) {
                $content .= "<span></span>";
            }

            if ($page_no < $total_no_of_pages) {
                global $wp;
                $content .= '<span><a href="' . add_query_arg( 'page_no', $next_page, get_permalink() ) . '">Next</a> - </span>';
                $content .= '<span><a href="' . add_query_arg( 'page_no', $total_no_of_pages, get_permalink() ) .'">Last Page</a></span>';
            }

            $content.='</span>';
            $content.='</div>';

            //from main form that includes filters
            $content .= '</div></form></p>';

            // return the table
            return $content;
        }

        private function render_filters() : string
        {
            $cty = filter_input(INPUT_POST, 'selectcity', FILTER_SANITIZE_SPECIAL_CHARS);

            $select = '<p><form method="POST" action="" class="filterclass">FILTER DATA:  '
                . \EternalTerror\ViewHelpers\select_field(
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

            $select.=' Filter by month: ';
            $select.= '<select name="selectmonth" size="1"><option value="0" selected="selected">- - All - -</option>';
            for ($i = 0; $i < 12; $i++) {
                $time = strtotime(sprintf('%d months', $i));
                $label = date('F', $time);
                $value = date('n', $time);
                $select.= "<option value='$value'>$label</option>";
            }

            $select.='</select>';

            if(is_admin()) {
                //option to select own concerts only
                $select .= '<input name="ownconcerts" class="ownconc" type="checkbox" value="1"'
                    . checked(isset($_POST['ownconcerts']) ? $_POST['ownconcerts'] : false)
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
