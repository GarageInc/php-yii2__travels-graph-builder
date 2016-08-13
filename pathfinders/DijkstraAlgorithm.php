<?php

namespace app\pathfinders;
/**
 * Created by PhpStorm.
 * User: RinatF
 * Date: 01.08.2016
 * Time: 19:53
 */
class DijkstraAlgorithm
{

    public static function findPath($V, $a, $b){

        // initialize the array for storing
        $U = array();// the nearest path with its parent and weight
        $d = array();// the left nodes without the nearest path

        foreach(array_keys($V) as $u)
            $d[$u] = 99999999;

        $d[$a] = 0;

        // start calculating
        while(!empty($d)){
            $min = array_search(min($d), $d);//the most min weight

            if( $min == $b)
                break;

            foreach( $V[$min] as $key=>$u)
                if( !empty($d[$key]) && $d[$min] + $u < $d[$key]) {
                    $d[$key] = $d[$min] + $u;
                    $U[$key] = array($min, $d[$key]);
                }

            unset($d[$min]);
        }

        // list the path
        $path = array();
        $pos = $b;

        while( $pos != $a){
            $path[] = $pos;
            $pos = $U[$pos][0];
        }

        $path[] = $a;
        $path = array_reverse($path);

        return array(
            "length" => $U[$b][1],
            "path" => implode('|', $path)
        );
    }
}