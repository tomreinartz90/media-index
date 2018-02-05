<?php
  use phpFastCache\CacheManager;

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

      CacheManager ::setDefaultConfig( [
        "path" => __DIR__ . '/../../cache', // or in windows "C:/tmp/"
         "defaultTtl" =>  "7776000"
      ] );
//      $this -> cache = CacheManager ::getInstance( 'Sqlite' );
      $this -> cache = CacheManager ::getInstance( 'Files' );

    }

    public function get( $key )
    {
      $data = $this -> cache -> getItem( $key );
//      var_dump( $data != null ? $data -> get() : null);
      return $data !== null ? $data -> get() : false;
    }

    public function set( $key, $data )
    {
      $cachedData = $this -> cache -> getItem( $key ) -> set( $data );
      $this -> cache -> save( $cachedData );
    }

    public function has( $key )
    {
      return $this -> cache -> hasItem( $key );
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