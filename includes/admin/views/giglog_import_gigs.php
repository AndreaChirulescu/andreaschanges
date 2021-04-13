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

if ( !class_exists( 'GiglogAdmin_ImportGigsPage' ) ) {
    require_once __DIR__ . '/../../band.php';
    require_once __DIR__ . '/../../concert.php';
    require_once __DIR__ . '/../../concertlogs.php';
    require_once __DIR__ . '/../../venue.php';

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

        /**
         * Imports concert data from a file with tab separated values.
         *
         * The file must contain the following columns each separated by _one_
         * tab character:
         *
         *   1. Bandname
         *   2. Venuename
         *   3. Concert date
         *   4. Ticket link
         *   5. Event info link
         *
         * Empty lines are ignored.
         */
        static function process_upload($file) {
            $newconcert= [];
            $fo = new SplFileObject($file['tmp_name']);

            foreach ($fo as $line) {
                $line = trim( $line );
                if ( empty($line) ) {
                    // Skip empty lines
                    continue;
                }

                $resultArray = explode("\t", $line);
                $band        = trim($resultArray[0]);
                $venue       = trim($resultArray[1]);
                $condate     = date('Y-m-d', strtotime($resultArray[2]));
                $ticketlink  = trim($resultArray[3]);
                $eventlink   = trim($resultArray[4]);
                //first item in the row should be band $resultArray[0]; second should be venue $resultArray[1]; third should be concert date $resultArray[2];
                //fourth item is ticketlink $resultArray[3];  fifth item is eventlink $resultArray[4];

                $newconcert[0] = GiglogAdmin_Band::find_or_create($band,'NO');

                if (is_numeric($venue))
                    $newconcert[1] = $venue;
                else {
                    $v = GiglogAdmin_Venue::find_or_create($venue,'Oslo');  //phase 666 of the project should maybe consider both city and band country when creating concerts/importing files
                    $newconcert[1] = $v->id();
                }

                //not sure how to check dates, hopefully manual verification of files will take care of it

                $cresults = GiglogAdmin_Concert::get($newconcert[0], $newconcert[1], $condate);
                if ($cresults) {
                    error_log( 'DUPLICATE ROW detected: '
                        . ' BAND ' . $band . ' with band ID ' . $newconcert[0]
                        . ', VENUE ' . $venue . ' with venue ID ' . $newconcert[1]
                        . ', CONCERTDATE ' . $condate);
                } else {
                    $id = GiglogAdmin_Concert::find_or_create(
                        '',
                        $newconcert[0],
                        $newconcert[1],
                        $condate,
                        $ticketlink,
                        $eventlink);

                    error_log( 'NEW CONCERT ADDED: '
                        . ' ID: ' . $id->id()
                        . ' BAND ' . $band . ' with band ID ' . $newconcert[0]
                        . ', VENUE ' . $venue . ' with venue ID ' . $newconcert[1]
                        . ', CONCERTDATE ' . $condate
                        . ', Ticket LINK ' . $ticketlink
                        . ', Event LINK ' . $eventlink);

                    GiglogAdmin_Concertlogs::add($id->id());

                    /*the last line can be replaced by a trigger
                    CREATE TRIGGER `insertIntoPhotoLogs` AFTER INSERT ON `wpg_concerts`
                    FOR EACH ROW INSERT INTO wpg_concertlogs (
                    wpg_concertlogs.id,
                    wpg_concertlogs.wpgcl_concertid,
                    wpg_concertlogs.wpgcl_status)

                    VALUES
                    (null, new.id, 1)
                    */
                }
            }
        }
    }
}
