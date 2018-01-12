<?php

  /**
   * Created by PhpStorm.
   * User: t.reinartz
   * Date: 11-1-2018
   * Time: 12:35
   */
  class CacheUtil
  {

    /**
     * CacheUtil constructor.
     */
    public function __construct( $cachePath )
    {
      if ( isset( $cachePath ) ) {
        $this -> cachePath = $cachePath;
        if ( !file_exists( $cachePath ) ) {
          die( "$cachePath does not exist or is not writeable." );
        }
      } else {
        die( "Cannot create CacheUtil without CachePath" );
      }
    }

    public function get( $key )
    {
      $data = file_get_contents( $this -> cachePath . '/' . $key );
      return $data !== false ? json_decode( $data ) : $data;
    }

    public function set( $key, $data )
    {
      return file_put_contents( $this -> cachePath . '/' . $key, json_encode( $data ) );
    }

    public function has( $key )
    {
      return file_exists( $this -> cachePath . '/' . $key );
    }

    /**
     * simple method to get or set data in the cache, if the method $getData returns false it will sleep 3 seconds and try again with a maximum of 3 tries.
     * @param $cacheKey
     * @param $useCache
     * @param $getData
     * @return bool|mixed|string
     */
    public function getOrSetData( $cacheKey, $useCache, $getData )
    {
      $retries = 0;
      if ( !$this -> has( $cacheKey ) || $useCache === false ) {
        $data = $getData();
        if ( isset( $data ) && $data != false ) {
          $this -> set( $cacheKey, $data );
          return $data;
        } else if ( $retries < 4 ) {
          sleep( 3 );
          $getData();
        }
      }

      return $this -> get( $cacheKey );


    }
  }