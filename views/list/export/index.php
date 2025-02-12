<?php
    namespace Dompdf;

    require_once(__DIR__ . '/../../../libs/dompdf/autoload.inc.php');
    use Dompdf\Dompdf;
    use Dompdf\Options;

    try {
        createWithDOMPDF();
    }
    catch (Exception $e) {}

    function createWithDOMPDF() {
        global $ARCHIVES_DIR;
        $url = $_GET['url'];
        $title = $_GET['title'];
        $wid = $_GET['wid'];
        $archive_dir = $ARCHIVES_DIR . '/' . $wid;
        $html = file_get_contents($archive_dir . '/index.php');

        $dompdf = new Dompdf();
        $options = $dompdf->getOptions();
        $options->setDefaultFont('DejaVu Sans');
        $options->setIsPhpEnabled(True);
        $options->setIsJavascriptEnabled(True);
        $options->setIsRemoteEnabled(True);
        $options->setChroot($archive_dir . '/../');
        $options->setIsHtml5ParserEnabled(True);
        $dompdf->setOptions($options);
        $dompdf->setBasePath($archive_dir);

        $dompdf->loadHTML($html);
        $dompdf->setBasePath($archive_dir);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        //$dompdf->stream($title . '.pdf', array('Attachment'=>0));
        // NOTE: Uncomment this to enable the download
        $dompdf->stream($title . '.pdf');
    }
?>
