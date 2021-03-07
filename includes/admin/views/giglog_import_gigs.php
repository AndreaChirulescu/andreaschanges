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
            echo giglogadmin_getunprocessed();
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

            $table = 'wpg_files';
            $fo = new SplFileObject($file['tmp_name']);
            $r = 0;

            foreach ($fo as $newconcert) {
                $row = array(
                    'filename' => $fo,
                    'rowid' => $r++,
                    'rowcontent' => $newconcert
                );

                if ($wpdb->insert($table, $row) === false) {
                    $wpdb->bail();
                }
            }
        }
    }
}
