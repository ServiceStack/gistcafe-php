<?php

namespace gistcafe;

class Inspect {

    public static function vars($args)
    {
        $inspectVarsPath = getenv('INSPECT_VARS');
        if (empty($inspectVarsPath) || empty($args)) {
            return;
        }

        $json = json_encode($args);
        $varsPath = str_replace("\\","/", $inspectVarsPath);
        if (strpos($varsPath,"/") !== false) {
            $dir = dirname($varsPath);
            @mkdir($dir, 0755, true);
        }

        @file_put_contents($varsPath, $json);
    }

    public static function dump($obj)
    {
        $to = json_encode($obj, JSON_PRETTY_PRINT);
        return str_replace('"','', str_replace('\\/','/', $to));
    }

    public static function printDump($obj)
    {
        echo Inspect::dump($obj) . "\n";
    }

    public static function dumpTable($rows)
    {
        $mapRows = Inspect::toArrayList($rows);
        $keys = Inspect::allKeys($mapRows);
        $colSizes = array();

        foreach ($keys as $k) {
            $max = strlen($k);
            foreach ($mapRows as $row) {
                $col = $row[$k];
                if (!empty($col)) {
                    $valSize = strlen("{$col}");
                    if ($valSize > $max) {
                        $max = $valSize;
                    }
                }
            }
            $colSizes[$k] = $max;
        }

        $colSizesLength = count($colSizes);
        $rowWidth = array_sum($colSizes)
            + ($colSizesLength * 2)
            + ($colSizesLength + 1);
        $dashes = str_repeat("-", $rowWidth-2);
        $sb = array();
        array_push($sb, "+{$dashes}+");
        $head = "|";
        foreach ($keys as $k) {
            $head .= Inspect::alignCenter($k, $colSizes[$k]) . "|";
        }
        array_push($sb, $head);
        array_push($sb, "|{$dashes}|");

        foreach ($mapRows as $row) {
            $to = "|";
            foreach ($keys as $k) {
                $to .= Inspect::alignAuto($row[$k], $colSizes[$k]) . "|";                
            }
            array_push($sb, $to);
        }

        array_push($sb, "+{$dashes}+");

        return join("\n", $sb);
    }

    public static function printDumpTable($rows)
    {
        echo Inspect::dumpTable($rows) . "\n";
    }

    public static function toArrayList($rows)
    {
        $array = json_decode(json_encode($rows), true);
        return $array;
    }

    public static function allKeys($rows)
    {
        $to = array();
        foreach ($rows as $row) {
            foreach ($row as $k => $v) {
                if (!in_array($k, $to)) {
                    array_push($to, $k);
                }
            }
        }
        return $to;
    }

    public static function alignLeft($str, $len, $pad = " ")
    {
        if ($len < 0) return "";
        $aLen = $len + 1 - strlen($str);
        if ($aLen <= 0) return $str;
        return $pad . $str . str_repeat($pad, $aLen);
    }

    public static function alignCenter($str, $len, $pad = " ")
    {
        if ($len < 0) return "";
        if (empty($str)) $str = "";
        $nLen = strlen($str);
        $half = floor($len / 2.0 - $nLen / 2.0);
        $odds = abs(($nLen % 2) - ($len % 2));
        return str_repeat($pad, $half + 1) . $str . str_repeat($pad, $half + 1 + $odds);
    }

    public static function alignRight($str, $len, $pad = " ")
    {
        if ($len < 0) return "";
        $aLen = $len + 1 - strlen($str);
        if ($aLen <= 0) return $str;
        return str_repeat($pad, $aLen) . $str . $pad;
    }

    public static function alignAuto($obj, $len, $pad = " ")
    {
        $str = "{$obj}";
        if (strlen($str) <= $len) {
            if (is_numeric($obj)) {
                return Inspect::alignRight($str, $len, $pad);
            }
            return Inspect::alignLeft($str, $len, $pad);
        }
        return $str;
    }
}

?>