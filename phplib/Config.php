<?php


class Config
{
    private $_cfgfile;
    private $_cfg = [];

    private $_throw_exception_on_notfound = false;        // Hard core debugging tool.

    /**
     * @param $cfgfile
     * @throws Exception
     */
    function __construct($cfgfile)
    {
        $this->_cfgfile = $cfgfile;
        $ret = array();

        if (!$this->_parsecfg($this->_cfgfile, $ret)) {
            if ($this->_throw_exception_on_notfound) {
                throw new \Exception("Parse failed " . $this->_cfgfile);
            }
        }
        $this->_cfg = $ret;
    }


    function _parsecfg($lfile, &$ret)
    {
        // populates $ret array with config parameters
        // and returns the array of config variables
        // $ret['sectionname']['parameter'] == 'value';

        $file = realpath($lfile);
        if (!$file) {
            error_log("Failed to realpath($lfile)");
            return false;
        }

        if (!is_file($file)) {
            error_log("File $file does nont exist");
            return (FALSE);
        }

        $fh = @fopen($file, "r");
        if (!$fh) {
            debuglog("DEBUG: fopen $file (resolved from '$lfile') failed");    // gid=".posix_getgid()."; egid=".posix_getegid());
            return (FALSE);
        }

        $section = "";

        while (feof($fh) == FALSE) {
            $str = fgets($fh, 4096);
//            echo "read '$str'<P>";
            if (!$str)
                break;

            $str = str_replace("\r", "", $str);
            $str = str_replace("\n", "", $str);
            $str = trim($str);
            if ($str == "" || $str[0] == '#' || $str[0] == '/')
                continue;

            if ($str[0] == '[') {
                $section = trim(substr($str, strpos($str, "[") + 1, strrpos($str, "]") - 1));
            }

            if (strstr($str, "=")) {
                $ret[$section][trim(substr($str, 0, strpos($str, "=")))] = trim(substr($str, strpos($str, "=") + 1));
            }
        }
        fclose($fh);
        return ($ret);
    }

    function getSection($section)
    {
        if (isset($this->_cfg[$section]))
            return $this->_cfg[$section];
        return false;
    }

    /** Returns a configuration value from the site configuration file
     * @param $section string section name
     * @param $var string variable name
     * @param $dfl string value if not found (otherwise a blank string is returned)
     * @return mixed|string
     */
    function get($section, $var, $dfl = NULL)
    {
        if (!isset($this->_cfg["$section"]["$var"])) {
            return $dfl;
        }
        return $this->_cfg["$section"]["$var"];
    }
}

