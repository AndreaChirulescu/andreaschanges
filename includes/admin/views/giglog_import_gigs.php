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
         * @param array<int, mixed>
         */
        static function process_upload(array $file): void {
            $newconcert= [];
            $fo = new SplFileObject($file['tmp_name']);

            foreach ($fo as $line) {
                $line = trim( $line );
                if ( empty($line) ) {
                    // Skip empty lines
                    continue;
                }

                $resultArray = explode("\t", $line);
                $cname       = trim($resultArray[0]);
                $venue       = trim($resultArray[1]);

                if (is_numeric($venue)) {
                    $venue = GiglogAdmin_Venue::get($venue);
                }
                else {
                    $venue = GiglogAdmin_Venue::find_or_create($venue,'Oslo');
                }

                $condate     = date('Y-m-d', strtotime($resultArray[2]));
                $ticketlink  = trim($resultArray[3]);
                $eventlink   = trim($resultArray[4]);

                GiglogAdmin_Concert::create($cname, $venue->id(), $condate, $ticketlink, $eventlink);
            }
        }
    }
}
