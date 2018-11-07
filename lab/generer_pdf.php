<?php
	require '../vendor/autoload.php';
	include "../includes/global_include.php";
	
	use Spipu\Html2Pdf\Html2Pdf;
	use Spipu\Html2Pdf\Exception\Html2PdfException;
	use Spipu\Html2Pdf\Exception\ExceptionFormatter;
	
	chdir('../vendor/spipu/html2pdf/examples');

	try {
		ob_start();

		?>

		<html>
			<body>
				Ceci est mon texte éditable : <input type="text" />
			</body>
		</html>

		<?php

		include '/res/exemple00.php';
		include '/res/exemple01.php';
		include '/res/exemple02.php';
		include '/res/exemple03.php';
		include '/res/exemple04.php';


		$content = ob_get_clean();
	
		$html2pdf = new Html2Pdf('P', 'A4', 'fr');
		$html2pdf->setDefaultFont('Arial');
		$html2pdf->writeHTML($content);
		$html2pdf->output('test.pdf');
	} catch (Html2PdfException $e) {
		$formatter = new ExceptionFormatter($e);
		echo $formatter->getHtmlMessage();
	}

?>