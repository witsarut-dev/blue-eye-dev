<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
use Dompdf\Dompdf;

class pdf {
    public function __construct() {
        require_once("dompdf/autoload.inc.php");
    }

    function loadPDF()
    {
        return new DOMPDF();
    }
}

?>