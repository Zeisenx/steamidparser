<?php

require_once 'lib/steamid.php';

header( 'Content-Type: text/plain' );

//-----------------------------------------------------------------------------
function PrintLine( $text ) {
	echo $text . "\r\n"; 
	flush();
}
//-----------------------------------------------------------------------------
function PrintSubTest( $text ) {
	echo "  $text" . "\r\n"; 
	flush();
}


//-----------------------------------------------------------------------------
class Test {
	public $name;
	public $method;
	
	function Run() {
		// lol.
		$method = $this->method;
		return $method(); 
	}
	
	function __construct( $name, $method ) {
		$this->name = $name;
		$this->method = $method;
	}
}

//-----------------------------------------------------------------------------
final class Tests {

	public static $tests = array();
	/*
	public function __construct( $name ) {
		self::$tests[] = $this;
		$this->name = $name; 
	}*/
	
	/**
	 * Run this test return TRUE on pass FALSE on failure.
	 */
	//protected abstract function RunTest();
	/*
	public function Run() {
		PrintLine( "---" );
		PrintLine( "Running test: \"$this->name\"" );
		if( $this->RunTest() ) {
			PrintLine( "Passed." );
		} else {
			PrintLine( "*** Failed! ***" );
		}
		
	}*/
	
	public static function Run() {
		PrintLine( "---" );
		PrintLine( "Running all tests." );
		foreach( self::$tests as $test ) {
			PrintLine( "\"$test->name\"" );
			if( $test->Run() ) {
				PrintLine( "Passed." );
			} else {
				PrintLine( "*** Failed! ***" );
			}
		}
	}
	
	public static function Add( $name, $method ) {
		self::$tests[] = new Test( $name, $method );
	}
}

//-----------------------------------------------------------------------------
Tests::Add( "Conversion Test", function() {
	
	PrintSubTest( "32-bit detect" );
	$steamid = SteamID::Parse( " STEAM_1:1:54499221 " );
	if( $steamid === FALSE ) return FALSE;
	PrintSubTest( "32-bit direct" );
	$steamid = SteamID::Parse( "STEAM_0:1:54499221", SteamID::FORMAT_32BIT );
	if( $steamid === FALSE ) return FALSE;
	PrintSubTest( "32-bit as 64 error" );
	$steamid = SteamID::Parse( "STEAM_1:1:54499221", SteamID::FORMAT_64BIT );
	if( $steamid !== FALSE ) return FALSE;
	
	PrintSubTest( "64-bit detect" );
	$steamid = SteamID::Parse( "76561198069264171" );
	if( $steamid === FALSE ) return FALSE;
	if( $steamid->Format( SteamID::FORMAT_32BIT ) != "STEAM_1:1:54499221" ) return FALSE;
	
	PrintSubTest( "64-bit direct" );
	$steamid = SteamID::Parse( "76561198069264171", SteamID::FORMAT_64BIT );
	if( $steamid === FALSE ) return FALSE;
	if( $steamid->Format( SteamID::FORMAT_32BIT ) != "STEAM_1:1:54499221" ) return FALSE;
	
	PrintSubTest( "64-bit errornous" );
	$steamid = SteamID::Parse( "76533611981069263334171", SteamID::FORMAT_64BIT );
	if( $steamid !== FALSE ) return FALSE;
	
	PrintSubTest( "64-bit as raw" );
	$steamid = SteamID::Parse( "76561198069264171", SteamID::FORMAT_RAW );
	if( $steamid !== FALSE ) return FALSE;
	
	PrintSubTest( "v3 detect" );
	$steamid = SteamID::Parse( "[U:1:108998443]" );
	if( $steamid === FALSE ) return FALSE;
	if( $steamid->Format( SteamID::FORMAT_32BIT ) != "STEAM_1:1:54499221" ) return FALSE;
	
	PrintSubTest( "v3 direct" );
	$steamid = SteamID::Parse( "[U:1:108998443]", SteamID::FORMAT_V3 );
	if( $steamid === FALSE ) return FALSE;
	if( $steamid->Format( SteamID::FORMAT_32BIT ) != "STEAM_1:1:54499221" ) return FALSE;
	
	PrintSubTest( "s32 detect" );
	$steamid = SteamID::Parse( "108998443" );
	if( $steamid === FALSE ) return FALSE;
	if( $steamid->Format( SteamID::FORMAT_32BIT ) != "STEAM_1:1:54499221" ) return FALSE;
	
	PrintSubTest( "s32 direct" );
	$steamid = SteamID::Parse( "108998443", SteamID::FORMAT_S32 );
	if( $steamid === FALSE ) return FALSE;
	if( $steamid->Format( SteamID::FORMAT_32BIT ) != "STEAM_1:1:54499221" ) return FALSE;
	
	PrintSubTest( "raw direct" );
	$steamid = SteamID::Parse( "108998443", SteamID::FORMAT_RAW );
	if( $steamid === FALSE ) return FALSE;
	if( $steamid->Format( SteamID::FORMAT_32BIT ) != "STEAM_1:1:54499221" ) return FALSE;
	
	PrintSubTest( "community URL 1" );
	$steamid = SteamID::Parse( "http://www.steamcommunity.com/profiles/76561198069264171" );
	if( $steamid === FALSE ) return FALSE;
	if( $steamid->Format( SteamID::FORMAT_32BIT ) != "STEAM_1:1:54499221" ) return FALSE;
	PrintSubTest( "community URL 2" );
	$steamid = SteamID::Parse( "http://steamcommunity.com/profiles/76561198069264171" );
	if( $steamid === FALSE ) return FALSE;
	if( $steamid->Format( SteamID::FORMAT_32BIT ) != "STEAM_1:1:54499221" ) return FALSE;
	PrintSubTest( "community URL 3" );
	$steamid = SteamID::Parse( "www.steamcommunity.com/profiles/76561198069264171" );
	if( $steamid === FALSE ) return FALSE;
	if( $steamid->Format( SteamID::FORMAT_32BIT ) != "STEAM_1:1:54499221" ) return FALSE;
	PrintSubTest( "community URL 4" );
	$steamid = SteamID::Parse( "steamcommunity.com/profiles/76561198069264171" );
	if( $steamid === FALSE ) return FALSE;
	if( $steamid->Format( SteamID::FORMAT_32BIT ) != "STEAM_1:1:54499221" ) return FALSE;
	
	PrintSubTest( "random conversions..." );
	for( $i = 0; $i < 5000; $i++ ) {
		// get a 32bit unsigned value
		$raw = bcadd( (string)mt_rand( 0, 2147483647 ), (string)mt_rand( 0, 2147483647 ), 0 );
		
		$steamid = SteamID::Parse( $raw, SteamID::FORMAT_RAW );
		
		for( $j = 0; $j < 8; $j++ ) {
			$format = mt_rand( 1, 5 );
			$formatted = $steamid->Format( $format );
			$steamid2 = SteamID::Parse( $formatted );
			if( $steamid2 === FALSE || $steamid2->Format( $format ) != $formatted ) {
				PrintSubTest( "failure:" );
				var_dump( $steamid );
				return FALSE;
			}
			$steamid = $steamid2;
		}
	}
	
	return TRUE;
} );


Tests::Add( "Large SteamID conversions", function () {

	for( $i = 0; $i < 255; $i++ ) {
		// get a 64bit-ish value
		$a = bcdiv( (string)mt_rand(), (string)mt_getrandmax() );
		$a = bcmul( $a, SteamID::MAX_VALUE, 0 );
	
		$steamid = SteamID::Parse( $a, SteamID::FORMAT_RAW );
		
		for( $j = 0; $j < 8; $j++ ) {
			$format = mt_rand( 1, 5 );
			$formatted = $steamid->Format( $format );
			if( $format == SteamID::FORMAT_S32 ) {
			
				// for S32 check for proper failure.
				if( $formatted === FALSE ) {
					if( bccomp( $steamid->value, '4294967296' ) >= 0 ) {
						
						continue;
					}
				}
			}
			$steamid2 = SteamID::Parse( $formatted );
			if( $steamid2 === FALSE || $steamid2->Format( $format ) != $formatted ) {
				PrintSubTest( "failure: (format=$format)" );
				var_dump( $steamid );
				return FALSE;
			}
			$steamid = $steamid2;
		}
	}
	
	return TRUE;
} );
 
bcscale( 32 );

Tests::Run();

?>