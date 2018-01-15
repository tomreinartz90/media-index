<?php
  /**
   * Created by PhpStorm.
   * simple script to update the application from the master branch
   * User: taren
   * Date: 15-1-2018
   * Time: 20:50
   */

  // Function to remove folders and files
  function rrmdir($dir) {
    if (is_dir($dir)) {
      $files = scandir($dir);
      foreach ($files as $file)
        if ($file != "." && $file != "..") rrmdir("$dir/$file");
      rmdir($dir);
    }
    else if (file_exists($dir)) unlink($dir);
  }

  $ch = curl_init();
  $source = "https://github.com/tomreinartz90/media-index/archive/master.zip"; // THE FILE URL
  echo "starting download of $source \n";


  curl_setopt( $ch, CURLOPT_URL, $source );

  curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
  curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, true );


  curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
  $data = curl_exec( $ch );
  curl_close( $ch );

  // save as master.zip
  $destination = "master.zip"; // NEW FILE LOCATION
  $file = fopen( $destination, "w+" );
  fputs( $file, $data );
  fclose( $file );
  echo " zip downloaded; \n";

  // unzip
  $zip = new ZipArchive;
  $res = $zip -> open( 'master.zip' ); // zip data
  if ( $res === true ) {
    $zip -> extractTo( '.' ); //extract data to folder
    $zip -> close();
    echo " zip extracted; \n";
    unlink( 'master.zip' );
    echo " zip deleted; \n";
  } else {
    echo " unzip failed; \n";
    var_dump( $res );
  }

  echo "start move of files from src folder\n";
  //move updated files to root
  $oldfolderpath = __DIR__ . "/media-index-master/src";
  $newfolderpath = __DIR__ . "/src";

  if ( file_exists( $newfolderpath ) )
    rrmdir( $newfolderpath );
  rename( $oldfolderpath, $newfolderpath );
  echo "all done\n";