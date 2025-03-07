<?php
function description( $str, $len = 150 ) {
	if ( strlen( $str ) >= $len ) {
		$str = substr( $str, 0, $len );
		$space = strrpos( $str, " " );
		$str = substr( $str, 0, $space ) . "...";
	}
	return $str;
}