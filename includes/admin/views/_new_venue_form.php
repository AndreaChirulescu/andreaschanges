<?php
// SPDX-FileCopyrightText: 2021 Andrea Chirulescu <andrea.chirulescu@gmail.com>
// SPDX-FileCopyrightText: 2021 Harald Eilertsen <haraldei@anduin.net>
//
// SPDX-License-Identifier: AGPL-3.0-or-later

if ( !class_exists( "GiglogAdmin_NewVenueForm" ) )
{
    class GiglogAdmin_NewVenueForm
    {
        public function render() : string
        {
            return
                '<div class="venueform">'
                . '<p><strong>VENUE DETAILS</strong></p>'
                . '<form method="POST" action="" class="venue">'
                . '  <fieldset>'
                . '    <div class="field venue_name_field">'
                . '      <label for="venue">Venue Name:</label>'
                . '      <input type="text" id="venuename" name="venuename">'
                . '    </div>'
                . '    <div class="field venue_city_field">'
                . '      <label for="venuecity">Venue City:</label>'
                . '      <input type="text" id="venuecity" name="venuecity">'
                . '    </div>'
                . '    <div class="actions">'
                . '      <input type="submit" name="newvenue" value="Create New Venue">'
                . '    </div>'
                . '  <fieldset>'
                . '</form>'
                . '</div>';
        }
    }
}
