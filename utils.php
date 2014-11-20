<?php
class Utils
{
    public static function var_dump_str($var)
    {
        ob_start();
        var_dump($var);
        $contents = ob_get_contents();
        ob_end_clean();
        return $contents;
    }/* function var_dump_str() */
}
?>
