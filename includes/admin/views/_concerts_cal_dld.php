<?php

   // Fix PHP headers
    ob_start();

use Kigkonsult\Icalcreator\Vcalendar;
use DateTime;
use DateTimezone;


    function icalvalue(): string
    {
         $vcalendar = Vcalendar::factory( [ Vcalendar::UNIQUE_ID => "kigkonsult.se", ] )

    // with calendaring info
                 ->setMethod( Vcalendar::PUBLISH )
                 ->setXprop(
                      Vcalendar::X_WR_CALNAME,
                      "Calendar Sample"
                 )
                 ->setXprop(
                      Vcalendar::X_WR_CALDESC,
                      "This is a demo calendar"
                 )
                 ->setXprop(
                      Vcalendar::X_WR_RELCALID,
                      "3E26604A-50F4-4449-8B3E-E4F4932D05B5"
                 )
                 ->setXprop(
                      Vcalendar::X_WR_TIMEZONE,
                      "Europe/Stockholm"
                 );
                 
                    // create a new event
$event1 = $vcalendar->newVevent()
              ->setTransp( Vcalendar::OPAQUE )
              ->setClass( Vcalendar::P_BLIC )
              ->setSequence( 1 )
    // describe the event
              ->setSummary( 'Scheduled meeting with five occurrences' )
              ->setDescription(
                   'Agenda for the the meeting...',
                   [ Vcalendar::ALTREP =>
                       'CID:<FFFF__=0ABBE548DFE235B58f9e8a93d@coffeebean.com>' ]
              )
              ->setComment( 'It\'s going to be fun..' )
    // place the event
              ->setLocation( 'Kafé Ekorren Stockholm' )
              ->setGeo( '59.32206', '18.12485' )
    // set the time
              ->setDtstart(
                  new DateTime(
                      '20220421T090000',
                      new DateTimezone( 'Europe/Stockholm' )
                  )
              )
              ->setDtend(
                  new DateTime(
                      '20220421T100000',
                      new DateTimezone( 'Europe/Stockholm' )
                  )
              )
    // with recurrence rule


;

    // add alarm for the event
$alarm = $event1->newValarm()
             ->setAction( Vcalendar::DISPLAY )
    // copy description from event
             ->setDescription( $event1->getDescription())
    // fire off the alarm one day before
             ->setTrigger( '-P1D' );

    // alter day and time for one event in recurrence set
$event2 = $vcalendar->newVevent()
              ->setTransp( Vcalendar::OPAQUE )
              ->setClass( Vcalendar::P_BLIC )
    // reference to event in recurrence set
              ->setUid( $event1->getUid())
              ->setSequence( 2 )
    // pointer to event in the recurrence set
              ->setRecurrenceid( '20220505T090000 Europe/Stockholm' )
    // reason text
              ->setDescription(
                  'Altered day and time for event 2022-05-05',
                  [ Vcalendar::ALTREP =>
                      'CID:<FFFF__=0ABBE548DFE235B58f9e8a93d@coffeebean.com>' ]
              )
              ->setComment( 'Now we are working hard for two hours' )
    // the altered day and time with duration
              ->setDtstart(
                  new DateTime(
                      '20220504T100000',
                      new DateTimezone( 'Europe/Stockholm' )
                  )
              )
              ->setDuration( 'PT2H' )
    // add alarm (copy from event1)
              ->setComponent(
                  $event1->getComponent( Vcalendar::VALARM )
              );

$vcalendarString =
    // apply appropriate Vtimezone with Standard/DayLight components
    $vcalendar->vtimezonePopulate()
    // and create the (string) calendar
    ->createCalendar();
    
return($vcalendarString);
    }
	
	
	
function dldCal()
{


if(isset($_POST['download'])){
    $file = "test.ical";
    $txt = fopen($file, "w") or die("Unable to open file!");
fwrite($txt, icalvalue());
fclose($txt);
 
  $filename = "myzipfile.zip";

  if (file_exists($file)) {
     header('Content-Type: application/zip');
     header('Content-Disposition: attachment; filename="'.basename($file).'"');
     header('Content-Length: ' . filesize($file));
    ob_clean();
     flush();
    echo  readfile($file);
     // delete file
    // unlink($file);
     
  }
}
/*

            


header('Content-Type: text/calendar');
header('Content-Disposition: attachment; filename="'.basename($file).'"');
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Content-Length:'. filesize($file));
header('Pragma: public');
ob_clean();
//flush();
readfile('test.ical');
//echo file_get_contents($file);

*/




}

