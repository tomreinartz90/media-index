<?php
  /**
   * Created by PhpStorm.
   * simple script to update the application from the master branch
   * User: taren
   * Date: 15-1-2018
   * Time: 20:50
   */

  $ch = curl_init();
  $source = "https://github.com/tomreinartz90/media-index/archive/master.zip"; // THE FILE URL
  curl_setopt( $ch, CURLOPT_URL, $source );
  curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
  $data = curl_exec( $ch );
  curl_close( $ch );

  // save as master.zip
  $destination = "master.zip"; // NEW FILE LOCATION
  $file = fopen( $destination, "w+" );
  fputs( $file, $data );
  fclose( $file );
  echo " zip downloaded; ";

  // unzip
  $zip = new ZipArchive;
  $res = $zip -> open( 'master.zip' ); // zip data
  if ( $res === true ) {
    $zip -> extractTo( '.' ); //extract data to folder
    $zip -> close();
    echo ' zip extracted; ';
    unlink( 'master.zip' );
    echo ' zip deleted; ';
  } else {
    echo ' unzip failed; ';
  }