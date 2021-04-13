<?php
/*
 * snippet used to  upload files with concerts. File is tab delimited file.
 * Band Venue Date TicketLink Eventlink. The form is at the end of this snippet
 */

function giglogadmin_upload_files() {
    global $wpdb;
    $output = "";
    $dir   = wp_upload_dir()['basedir'].'/concertlists/';  //the basedir is from file uploader plugin, namely the uploads folder in which I created a concertlist folder
    if ( !file_exists($dir) ) {
        mkdir( $dir );
    }

    $cfiles = scandir($dir);
    foreach ($cfiles as $value) { //list all files in directory
        $my_id = 0; //reset my_id which is used to check if anything was inserted
        if (strlen($value) > 3 and (is_dir($dir . '/' . $value) == false)) {
            $output .= 'Filename: ' . $value . '<br />';
            $filecontent = file_get_contents($dir . '/' . $value);
            $listcontent = str_replace(array(
                "\r",
                "\n"
            ), '<br />', $filecontent); //tring to replace end of lines with brs for html

            $output .= '<b>FILE CONTENT</b><br />';
            $r = 1;
            //processing each line of the file into a new row in wpg_files table
            if (isset($_POST['InsertFileContent'])) {
                $lines = new SplFileObject($dir . '/' . $value);
                //and then execute a sql query here
                $table = 'wpg_files';
                foreach ($lines as $newconcert) {
                    $output .= '<li> ' . $newconcert . '</li>';
                    $wpdb->insert($table, array(
                        'id' => '',
                        'filename' => $value,
                        'rowid' => $r,
                        'rowcontent' => $newconcert
                    ));
                    $r++;
                    //$wpdb->print_error();
                    $output .= $wpdb->last_error;
                    $my_id = $wpdb->insert_id;
                    $output .= '<br />---------------<br />Inserted rowID ' . $my_id . '<br />';
                } //end processing each line
            } //end file processing


        } //end if that checks whether filename is longer than 3 and is actually a file

        if ($my_id > 0) //if anything was inserted, move file to handled
        {
            $output .= '<br />File <b><i> ' . $value . ' </i></b> will be movedto handled folder';
            rename($dir . '/' . $value, $dir . '/handled/' . $value);
        }

    } //end looping through all folder content

    if ($my_id > 0) {
        $url1 = $_SERVER['REQUEST_URI'];
        header("Refresh: 5; URL=$url1");
    } //reload page

    $output .= '<form method="POST" action=""><input type="submit" name="InsertFileContent" value="InsertFileContent"/></form>';
    return $output;
}
