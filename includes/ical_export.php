<?php

// SPDX-FileCopyrightText: 2022 Andrea Chirulescu <andrea.chirulescu@gmail.com>
// SPDX-FileCopyrightText: 2022 Harald Eilertsen <haraldei@anduin.net>
//
// SPDX-License-Identifier: AGPL-3.0-or-later

use Kigkonsult\Icalcreator\Vcalendar;

if ( ! class_exists( "GiglogAdmin_IcalExport" ) )
{
    class Giglogadmin_IcalExport
    {
        public static function export_ical()
        {
            $evid = $_GET['evid'];

            $concert = GiglogAdmin_Concert::get($evid);
            $cfullname = $concert->cname().' live at '. $concert->venue()->name() .', '.$concert->venue()->city();
            $cshortname=substr($cfullname,0,20);
            $fdate =  strtotime($concert->cdate());
            $newformat = date('Ymd',$fdate);
            // create a new calendar
            $vcalendar = Vcalendar::factory( [ Vcalendar::UNIQUE_ID => "kigkonsult.se", ] )
                // with calendaring info
                ->setMethod( Vcalendar::PUBLISH )
                ->setXprop(
                    Vcalendar::X_WR_CALNAME,
                    "Calendar Sample"
                )
                ->setXprop(
                    Vcalendar::X_WR_CALDESC,
                    "Concert ".$cfullname . ""
                )
                ->setXprop(
                    Vcalendar::X_WR_RELCALID,
                    "3E26604A-50F4-4449-8B3E-E4F4932D05B5"
                )
                ->setXprop(
                    Vcalendar::X_WR_TIMEZONE,
                    "Europe/Oslo"
                );

            // create a new event
            $event1 = $vcalendar->newVevent()
                                ->setTransp( Vcalendar::OPAQUE )
                                ->setClass( Vcalendar::P_BLIC )
                                ->setSequence( 1 )
                            // describe the event
                                ->setSummary("".$cfullname."" )
                                ->setDescription("".$cfullname."" )
                                ->setComment  ("".$cfullname."" )
                            // place the event
                                ->setLocation( "".$concert->venue()->name() .', '.$concert->venue()->city() ."" )
                            // set the time
                                ->setDtstart(
                                    new DateTime(
                                        $newformat.'T190000',
                                        new DateTimezone( 'Europe/Oslo' )
                                    )
                                )
                                ->setDuration ("PT4H")

                            ;
            $vcalendarString =
                // apply appropriate Vtimezone with Standard/DayLight components
                $vcalendar->vtimezonePopulate()
                // and create the (string) calendar
                          ->createCalendar();

            header( 'Content-Type: text/calendar' );
            header( 'content-disposition: attachment;filename='.$cshortname.'.ics');
            echo $vcalendarString;
            die();
        }
    }

    /** @psalm-suppress HookNotFound */
    add_action( 'wp_ajax_nopriv_giglog_export_ical', [ 'GiglogAdmin_IcalExport', 'export_ical' ] );

    /** @psalm-suppress HookNotFound */
    add_action( 'wp_ajax_giglog_export_ical', [ 'GiglogAdmin_IcalExport', 'export_ical' ] );
}
