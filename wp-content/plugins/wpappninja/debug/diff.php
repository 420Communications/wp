<?php
defined( 'ABSPATH' ) or die( 'Cheatin\' uh?' );

/*
    Paul's Simple Diff Algorithm v 0.1
    (C) Paul Butler 2007 <http://www.paulbutler.org/>
    May be used and distributed under the zlib/libpng license.
    
    This code is intended for learning purposes; it was written with short
    code taking priority over performance. It could be used in a practical
    application, but there are a few ways it could be optimized.
    
    Given two arrays, the function diff will return an array of the changes.
    I won't describe the format of the array, but it will be obvious
    if you use print_r() on the result of a diff on some test data.
    
    htmlDiff is a wrapper for the diff command, it takes two strings and
    returns the differences in HTML. The tags used are <ins> and <del>,
    which can easily be styled with CSS.  
*/
/**
 * Diff checker
 *
 * @since 4.0.1
 */
function wpappninja_diff($old, $new){
	$old = preg_replace('#<#', '&lt;', $old);
	$new = preg_replace('#<#', '&lt;', $new);
	$old = preg_replace('#>#', '&gt;', $old);
	$new = preg_replace('#>#', '&gt;', $new);
    $matrix = array();
    $maxlen = 0;
    foreach($old as $oindex => $ovalue){
        $nkeys = array_keys($new, $ovalue);
        foreach($nkeys as $nindex){
            $matrix[$oindex][$nindex] = isset($matrix[$oindex - 1][$nindex - 1]) ?
                $matrix[$oindex - 1][$nindex - 1] + 1 : 1;
            if($matrix[$oindex][$nindex] > $maxlen){
                $maxlen = $matrix[$oindex][$nindex];
                $omax = $oindex + 1 - $maxlen;
                $nmax = $nindex + 1 - $maxlen;
            }
        }   
    }
    if($maxlen == 0) return array(array('d'=>$old, 'i'=>$new));
    return array_merge(
        wpappninja_diff(array_slice($old, 0, $omax), array_slice($new, 0, $nmax)),
        array_slice($new, $nmax, $maxlen),
        wpappninja_diff(array_slice($old, $omax + $maxlen), array_slice($new, $nmax + $maxlen)));
}
function wpappninja_html_diff($old, $new, $title = ""){
	if ($old == $new) {return;}
    $ret = '<style>ins{font-weight:700;color:green;}del{font-weight:700;color:red}</style><br/><br/><hr/><br/><h2>' . $title . '</h2>';
	$ret .= '<h3>OLD</h3> ' . $old . ' <h3>NEW</h3> ' . $new . ' <h4>DIFF</h4>';
    $diff = wpappninja_diff(preg_split("/[\s]+/", $old), preg_split("/[\s]+/", $new));
    foreach($diff as $k){
        if(is_array($k))
            $ret .= (!empty($k['d'])?"<del>".implode(' ',$k['d'])."</del> ":'').
                (!empty($k['i'])?"<ins>".implode(' ',$k['i'])."</ins> ":'');
        else $ret .= $k . ' ';
    }
    return $ret;
}