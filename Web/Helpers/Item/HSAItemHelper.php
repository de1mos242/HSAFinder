<?php

/**
 * Description of HSAItemHelper
 *
 * @author de1mos <de1m0s242@gmail.com>
 */
class HSAItemHelper {
    public static function LineDirectionToLocalString($value) {
        switch ($value) {
            case "FRONT":
                return "передняя";
            case "REAR":
                return "задняя";
            default:
                break;
        }
        return $value;
    }
    
    public static function HandDirectionToLocalString($value) {
        switch ($value) {
            case "LEFT":
                return "левая";
            case "RIGHT":
                return "правая";
            default:
                break;
        }
        return $value;
    }
    public static function TypeToLocalString($value) {
        switch ($value) {
            case "GAS":
                return "газовая";
            case "OIL":
                return "масляная";
            default:
                break;
        }
        return $value;
    }
}

?>
