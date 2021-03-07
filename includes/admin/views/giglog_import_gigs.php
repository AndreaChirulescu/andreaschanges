<?php
/*
      Copyright (C) 2021 Harald Eilertsen, Andrea Chirulescu

      This program is free software: you can redistribute it and/or modify
      it under the terms of the GNU Affero General Public License as
      published by the Free Software Foundation, either version 3 of the
      License, or (at your option) any later version.

      This program is distributed in the hope that it will be useful,
      but WITHOUT ANY WARRANTY; without even the implied warranty of
      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
      GNU Affero General Public License for more details.

      You should have received a copy of the GNU Affero General Public License
      along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

if ( !class_exists( 'GiglogAdmin_ImportGigsPage' ) ) {
    class GiglogAdmin_ImportGigsPage {
        static function render_html() {
            ?>
            <div class="wrap">
                <h1>Import gigs</h1>
                <p>Import gig data from a tab separated data file.</p>
                <form action="<?php menu_page_url( 'giglog_import' ) ?>" enctype="multipart/form-data" method="post">
                    <?php wp_nonce_field( plugin_basename( __FILE__ ), 'giglog_import_nonce' ); ?>
                    <label for="giglog_import_file">File: </label>
                    <input type="file" name="giglog_import_file" id="giglog_import_file">
                    <?php submit_button(); ?>
                </form>
            </div>
            <?php
        }

        static function submit_form() {
            if ('POST' === $_SERVER['REQUEST_METHOD'] && current_user_can('upload_files') && !empty($_FILES['giglog_import_file']['tmp_name'])) {
                $nonce = $_POST['giglog_import_nonce'];
                $valid_nonce = isset($nonce) && wp_verify_nonce($nonce);
                GiglogAdmin_ImportGigsPage::process_upload($_FILES['giglog_import_file']);
            }
        }

        static function process_upload($file) {
            global $wpdb;

            $concertlist = '<p>Inserted the following:</p>';
            $newconcert= [];
            $fo = new SplFileObject($file['tmp_name']);

            foreach ($fo as $line) {
                if ( empty($line) ) {
                    // Skip empty lines
                    continue;
                }

                $resultArray = explode("\t", $line);
                $band        = $resultArray[0];
                $venue       = $resultArray[1];
                $condate     = date('Y-m-d', strtotime($resultArray[2]));
                $ticketlink  = $resultArray[3];
                $eventlink   = $resultArray[4];
                //first item in the row should be band $resultArray[0]; second should be venue $resultArray[1]; third should be concert date $resultArray[2];
                //fourth item is ticketlink $resultArray[3];  fifth item is eventlink $resultArray[4];

                //processing band
                $bandsql = 'SELECT id FROM wpg_bands WHERE upper(wpgband_name)="' . $band . '"';
                $results = $wpdb->get_results($bandsql);
                if ($results)
                    $newconcert[0] = $results[0]->id;
                else {
                    $wpdb->insert('wpg_bands', array(
                        'id' => '',
                        'wpgband_name' => $band
                    ));
                    echo ($wpdb->last_error);
                    $newconcert[0] = $wpdb->insert_id;
                }
                //done processing band

                //processing venue
                if (is_numeric($venue))
                    $newconcert[1] = $venue;
                else {
                    $venuesql = 'SELECT id FROM wpg_venues WHERE upper(wpgvenue_name)="' . $venue . '"';
                    $results  = $wpdb->get_results($venuesql);
                    if ($results)
                        $newconcert[1] = $results[0]->id;
                    else {
                        $wpdb->insert('wpg_venues', array(
                            'id' => '',
                            'wpgvenue_name' => $venue
                        ));
                        echo ($wpdb->last_error);
                        $newconcert[1] = $wpdb->insert_id;
                    }
                }
                //done processing venue

                //not sure how to check dates, hopefully manual verification of files will take care of it

                //check if concert already exists and return ID if it does.  Not checking by date, to be considered
                $csql = 'SELECT id from wpg_concerts where band = ' . $newconcert[0] . ' and venue = ' . $newconcert[1] . ' and wpgconcert_date ="' . $condate . '"';

                $cresults = $wpdb->get_results($csql);
                if ($cresults) {
                    $concertlist .= 'DUPLICATE ROW detected BAND ' . $band . ' with band ID ' . $newconcert[0];
                    $concertlist .= ', VENUE ' . $venue . ' with venue ID ' . $newconcert[1];
                    $concertlist .= ', CONCERTDATE ' . $condate;
                    $concertlist .= ' <br />';
                } else {
                    $wpdb->insert('wpg_concerts', array(
                        'id' => '',
                        'band' => $newconcert[0],
                        'venue' => $newconcert[1],
                        'wpgconcert_date' => $condate,
                        'wpgconcert_tickets' => $ticketlink,
                        'wpgconcert_event' => $eventlink
                    ));
                    echo ($wpdb->last_error);
                    $newconcertid = $wpdb->insert_id;

                    $concertlist .= 'BAND ' . $band . ' with band ID ' . $newconcert[0];
                    $concertlist .= ', VENUE ' . $venue . ' with venue ID ' . $newconcert[1];
                    $concertlist .= ', CONCERTDATE ' . $condate . ', Ticket LINK ' . $ticketlink . ', event LINK' . $eventlink;
                    $concertlist .= ' <br />';
                }
            }
        }
    }
}
