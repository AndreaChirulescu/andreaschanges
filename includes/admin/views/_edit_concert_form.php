<?php
// SPDX-FileCopyrightText: 2021 Andrea Chirulescu <andrea.chirulescu@gmail.com>
// SPDX-FileCopyrightText: 2021 Harald Eilertsen <haraldei@anduin.net>
//
// SPDX-License-Identifier: AGPL-3.0-or-later

if (!class_exists("GiglogAdmin_EditConcertForm"))
{
    class GiglogAdmin_EditConcertForm
    {
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



        public function render() : string
        {
            $cid = filter_input(INPUT_POST, "cid");
            $editing = filter_input(INPUT_POST, "edit") == "EDIT";

            if ($editing && !empty($cid))   //A bit overdoing with the checks if concert ID is empty both here and in find_cid. But based on that, things are NULL or not. Better ideas?
                $c = GiglogAdmin_Concert::get($cid);
            else
                $c = new GiglogAdmin_Concert((object)[]);

            $content='<div class="concertform">';
            $content.='<form method="POST" action="" class="concert" >'
                .'<div class="concertitems"><strong>CONCERT DETAILS</strong><br><br><fieldset>'
                . wp_nonce_field( plugin_basename( __FILE__ ), 'giglog_edit_concert_nonce' )
                .'<input type="hidden" name="pid" value="' .$c->id(). '" />'
                .'<label for="cname">Concert Name:</label><textarea id="cname" name="cname" value="'.$c->cname().'">'.$c->cname().'</textarea><br>'
                .'<label for="venue">Venue:</label>' . $this->get_venue_selector($c->venue()) . '<br>'
                //date has to be formatted else it is not red in the date field of html form
                .'<label for="cdate">Date:</label><input type="date" id="cdate" name="cdate" value="'.date('Y-m-d',strtotime($c->cdate())).'"><br>'
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

            return $content;
        }
    }
}
