<?php

// SPDX-FileCopyrightText: 2021 Andrea Chirulescu <andrea.chirulescu@gmail.com>
// SPDX-FileCopyrightText: 2021 Harald Eilertsen <haraldei@anduin.net>
//
// SPDX-License-Identifier: AGPL-3.0-or-later

if ( !class_exists( 'GiglogAdmin_ImportGigsPage' ) ) {
    require_once __DIR__ . '/../../concert.php';
    require_once __DIR__ . '/../../venue.php';

    class GiglogAdmin_ImportGigsPage {
        static function render_html(): void {
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

        static function submit_form(): void {
            if ('POST' === $_SERVER['REQUEST_METHOD'] && current_user_can('upload_files') && !empty($_FILES['giglog_import_file']['tmp_name'])) {
                if (isset($_POST['giglog_import_nonce']) && wp_verify_nonce($_POST['giglog_import_nonce'], plugin_basename( __FILE__ )) ) {
                    GiglogAdmin_ImportGigsPage::process_upload($_FILES['giglog_import_file']);
                }
                else {
                    header("{$_SERVER['SERVER_PROTOCOL']} 403 Forbidden");
                    wp_die('CSRF validation failed.', 403);
                }
            }
        }

        /**
         * Imports concert data from a file with tab separated values.
         *
         * The file must contain the following columns each separated by _one_
         * tab character:
         *
         *   1. Concertname
         *   2. Venuename or numeric venue id
         *   3. Concert date
         *   4. Ticket link
         *   5. Event info link
         *
         * Empty lines are ignored.
         *
         * @return void
         *
         * @param array<int, mixed> $file
         */
        static function process_upload(array $file): void
        {
            $fo = new SplFileObject($file['tmp_name']);
            $importerrors = [];
            $rid=0;

            foreach ($fo as $line) {
                $rid++;
                $line = trim( $line );
                if ( !empty($line) ) {
                    try {
                        self::process_line( $line );
                    }
                    catch (Exception $e) {
                        $importerrors[] = "Error importing line {$rid}: {$e->getMessage()}";
                    }
                }
            }

            if (!empty($importerrors)) {
                echo implode("<br>", $importerrors);
            }
            else {
                echo ('All rows imported ok');
            }
        }

        static function process_line( string $line ) : void
        {
            $resultArray = explode("\t", $line);

            // unsure if this is needed, considering we are also checking if
            // each individual important field is missing. But if they are not
            // replaced by tabs, then everything gets shifted so probably best
            // to check if a value is empty and NOT replaced by tab

            if (count($resultArray) < 6) {
                throw new Exception("missing a field");
            }

            if (! preg_match("/\d{4}\-\d{2}-\d{2}/",$resultArray[3])) {
                throw new Exception("invalid date: " . esc_html($resultArray[3]));
            }

            if (empty(trim($resultArray[0]))) {
                throw new Exception("missing concert name");
            }

            if (empty(trim($resultArray[1]))) {
                throw new Exception("missing venue");
            }

            if (empty(trim($resultArray[2]))) {
                throw new Exception("missing city");
            }

            $condate = date('Y-m-d', strtotime($resultArray[3]));

            if ($condate<date("Y-m-d")) {
                throw new Exception("has date in the past: " . esc_html($resultArray[3]));
            }

            $cname       = trim($resultArray[0]);
            $venue       = trim($resultArray[1]);

            if (is_numeric($venue)) {
                $venue = GiglogAdmin_Venue::get(intval($venue));
                if (!$venue) {
                    throw new Exception("invalid venue id: {$venue}");
                }
            }
            else {
                $venue = GiglogAdmin_Venue::find_or_create($venue,trim($resultArray[2]));
            }

            $ticketlink  = trim($resultArray[4]);
            $eventlink   = trim($resultArray[5]);

            GiglogAdmin_Concert::create($cname, $venue->id(), $condate, $ticketlink, $eventlink);
        }
    }
}
