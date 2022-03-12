<?php

// SPDX-FileCopyrightText: 2021 Andrea Chirulescu <andrea.chirulescu@gmail.com>
// SPDX-FileCopyrightText: 2021 Harald Eilertsen <haraldei@anduin.net>
//
// SPDX-License-Identifier: AGPL-3.0-or-later

if ( !class_exists( 'GiglogAdmin_AdminPage' ) ) {
    require_once __DIR__ . '/../../venue.php';
    require_once __DIR__ . '/_concerts_table.php';
    require_once __DIR__ . '/_edit_concert_form.php';
    require_once __DIR__ . '/_new_venue_form.php';

    class GiglogAdmin_AdminPage
    {
        const STATUS_LABELS = [
            '',
            'Accred Requested',
            'Photo Approved',
            'Text Approved',
            'Photo and Text Approved',
            'Rejected'
        ];

        public static function render_html() : void
        {
            $page = new self();
            $page->render_page();
        }

        private function render_page() : void
        {
            $concerts = new GiglogAdmin_ConcertsTable();
            ?>
            <div class="wrap">
                <h1>Giglog Admin</h1>

                <p>The available slots are marked with the green checkbox.
                If you click on it, it will be assigned to you and if you no longer
                wish to cover that concert, click on the red icon and you will be
                unassigned. A mail should be sent to the admin when this happens,
                but in order for the accreditation request to be sent, you have to
                mail live@eternal-terror.com with the template containing concert
                information. There might be some exceptions, but those are discussed
                case by case. So whenever you want a concert, assign yourself and send
                the template no later than 3 weeks before the concert.</p>

                <p>Admin will try to keep the concert status updated so that you know
                what the accreditation status is. You will get personal message if this
                is really close to the concert date.</p>

                <p><?php echo $concerts->render() ?></p>
            </div>
            <?php
            if (current_user_can('administrator')) {
                $edit_form = new GiglogAdmin_EditConcertForm();
                $venue_form = new GiglogAdmin_NewVenueForm(); ?>
                <div>
                    <h3>Form to create/edit concerts and venues</h3>
                </div>
                <div class="editform">
                    <?php echo $edit_form->render() . $venue_form->render(); ?>
                </div><?php
            }
        }

        /**
         * @return void
         */
        static function update() : void
        {
            if ('POST' !== $_SERVER['REQUEST_METHOD'])
                return;

            if (isset($_POST['assignitem']) || isset($_POST['unassignitem']) || isset($_POST['selectstatus'])) {
                GiglogAdmin_ConcertsTable::update();
                return;
            }

            if (isset($_POST['newconcert']) || isset($_POST['editconcert'])) {
                GiglogAdmin_EditConcertForm::update();
                return;
            }

            if(isset($_POST['newvenue']))
            {
                if (!isset($_POST['giglog_new_venue_nonce'])
                    || wp_verify_nonce($_POST['giglog_new_venue_nonce'], plugin_basename( __FILE__ )))
                {
                    header("{$_SERVER['SERVER_PROTOCOL']} 403 Forbidden");
                    wp_die('CSRF validation failed.', 403);
                }

                if (empty($_POST['venuename']) || empty($_POST['venuecity'])) {
                    echo '<script language="javascript">alert("You are missing a value, venue was not created"); </script>';
                }
                else
                {
                    GiglogAdmin_Venue::create($_POST['venuename'],$_POST['venuecity']);
                    echo '<script language="javascript">alert("Yey, venue created"); </script>';
                }
            }
        }
    }
}
