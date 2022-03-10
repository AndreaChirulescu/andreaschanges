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
        static function update()
        {
            if ('POST' !== $_SERVER['REQUEST_METHOD'])
                return;

            if(isset($_POST['assignitem']))
            {
                $concert = GiglogAdmin_Concert::get(intval($_POST['cid']));
                $role = sanitize_text_field($_POST['pid']);

                if ($concert) {
                    GiglogAdmin_AdminPage::assignconcert($role, $concert);
                }

                $url2=$_SERVER['REQUEST_URI'];
                header("Refresh: 1; URL=$url2");  //reload page
            }

            if(isset($_POST['unassignitem']))
            {
                $concert = GiglogAdmin_Concert::get(intval($_POST['cid']));
                if ( ! $concert ) {
                    wp_die( "Invalid concert specified." );
                }

                $role = sanitize_text_field($_POST['pid']);

                GiglogAdmin_AdminPage::unassignconcert($role, $concert);

                $url3=$_SERVER['REQUEST_URI'];
                header("Refresh: 1; URL=$url3");  //reload page
            }

            // handle the status drop down
            if (isset($_POST['selectstatus']) && !empty($_POST['selectstatus']) && !empty($_POST['cid']))
            {
                if ($_POST['selectstatus'] > 0 && $_POST['selectstatus'] < count(self::STATUS_LABELS)) {
                    $concert = GiglogAdmin_Concert::get(intval($_POST['cid']));
                    if ( $concert ) {
                        $concert->set_status(intval($_POST['selectstatus']));
                        $concert->save();
                        GiglogAdmin_AdminPage::emailuser($concert,intval($_POST['selectstatus']));
                    }
                }
            }

            if(isset($_POST['newconcert'])) {
                if (empty($_POST['cname'])  || empty($_POST['selectvenueadmin']) || empty($_POST['cdate']) || empty($_POST['ticket']) || empty($_POST['eventurl'])) {
                    echo '<script language="javascript">alert("You are missing a value, concert was not created"); </script>';
                }
                else {
                    if (GiglogAdmin_Concert::create($_POST['cname'], $_POST['selectvenueadmin'], $_POST['cdate'], $_POST['ticket'], $_POST['eventurl'])) {
                        echo '<script language="javascript">alert("Yey, concert created"); </script>';
                    }
                    else {
                        echo '<script language="javascript">alert("Nay, concert was duplicated"); </script>';
                    }
                }
            }

            if (isset($_POST['editconcert']))
            {
                if (!isset($_POST['giglog_edit_concert_nonce'])
                    || wp_verify_nonce($_POST['giglog_edit_concert_nonce'], plugin_basename( __FILE__ )))
                {
                    header("{$_SERVER['SERVER_PROTOCOL']} 403 Forbidden");
                    wp_die('CSRF validation failed.', 403);
                }

                $roles = array_reduce(
                    ['photo1', 'photo1', 'rev1', 'rev2'],
                    function($roles, $r) {
                        if (isset($_POST[$r])) {
                            $roles[$r] = sanitize_user($_POST[$r]);
                        }
                        return $roles;
                    },
                    []
                );

                $attributes = [
                    'wpgconcert_name' => sanitize_text_field($_POST['cname']),
                    'venue' => intval($_POST['selectvenueadmin']),
                    'wpgconcert_date' => sanitize_text_field($_POST['cdate']),
                    'wpgconcert_ticket' => esc_url_raw($_POST['ticket']),
                    'wpgconcert_event' => esc_url_raw($_POST['eventurl']),
                    'wpgconcert_roles' => $roles,
                ];

                $concert = GiglogAdmin_Concert::get(intval($_POST['pid']));
                if ($concert && $concert->update((object) $attributes)) {
                    // let user know the concert was updated.
                    // Look into admin_notices
                }
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

        static function assignconcert(string $p1, GiglogAdmin_Concert $concert): void
        {
            $username = wp_get_current_user()->user_login;
            $concert->assign_role($p1, $username);
            $concert->save();

            $cuser = get_user_by( 'login', 'etadmin');

            if ( $cuser ) {
                $dest = $cuser->user_email;
                $subject = 'WP-GIGLOG '.$username.' has taken '.$p1. 'for concert '.$concert->cname();
                $body = 'WP-GIGLOG '.$username.' has taken '.$p1. 'for concert '.$concert->cname().', concert with ID ' .$concert->id();
                $headers = array('Content-Type: text/html; charset=UTF-8');

                wp_mail( $dest, $subject, $body );
            }
        }

        static function unassignconcert(string $p1, GiglogAdmin_Concert $concert): void
        {
            $username = wp_get_current_user()->user_login;
            $concert->remove_user_from_roles($username);
            $concert->save();

            $cuser = get_user_by( 'login', 'etadmin');

            if ( $cuser ) {
                $dest = $cuser->user_email;
                $subject = 'WP-GIGLOG '.$username.' has UNASSIGNED  '.$p1. 'for concert '.$concert->cname();
                $body = 'WP-GIGLOG '.$username.' has UNASSIGNED  '.$p1. 'for concert '.$concert->cname().', concert with ID ' .$concert->id();
                $headers = array('Content-Type: text/html; charset=UTF-8');

                wp_mail( $dest, $subject, $body );
            }
        }

        static function emailuser(GiglogAdmin_Concert $concert, $cstatus): void
        {
            $username = wp_get_current_user()->user_login;
            $useremail = 'live@eternal-terror.com';
            $dest = '';
            $roles = $concert -> roles();
            $x = '';

            foreach ($roles as $role) {
                if($role){
                    $cuser = get_user_by( 'login', $role);

                    if ( $cuser ) {
                        $dest .= $cuser->user_email . ',';
                    }
                }
            }

            $subject = 'Message from GIGLOG: Concert '.$concert->cname().' has a new status  '.$cstatus. '.';
            $body = 'You receive this message because you have assigned one of the roles for Concert '.$concert->cname().'.';
            $body .= '\r\n This is to inform you that there is a new status for the acreditation  '.$cstatus. '.';
            $body .= '\r\n Should you no longer want to receive updates about this concert, please log in to Giglog and remove yourself from the concert. Thanks!';
            $headers = array('Content-Type: text/plain; charset=UTF-8'); //it is text by default so no need for headers actually

            wp_mail( $dest, $subject, $body );
        }

    }
}
?>
