<?php
session_start();
require_once '../vendor/autoload.php';
use Dompdf\Dompdf;
use Dompdf\Options;

if (!isset($_SESSION['acuse_pdf'])) {
    header("Location: ../index.php");
    exit;
}

$data = $_SESSION['acuse_pdf'];
extract($data); // Esto crea las variables: $nombre, $telefono, $correo, etc.

$requisitos_filtrados = $data['requisitos'] ?? [];
$logo_src = 'data:image/png;base64,' . base64_encode(file_get_contents($data['logo']));

ob_start();
include 'acuse_template.php';
$html = ob_get_clean();

$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isPhpEnabled', true);
$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

$pdf_output = $dompdf->output();
$pdf_base64 = base64_encode($pdf_output);

unset($_SESSION['acuse_pdf']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cita registrada</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<script>
Swal.fire({
    icon: 'success',
    title: '¡Cita registrada!',
    text: 'El acuse se descargará automáticamente.',
    showConfirmButton: false,
    timer: 3000
});
const link = document.createElement('a');
link.href = 'data:application/pdf;base64,<?= $pdf_base64 ?>';
link.download = 'acuse_cita_<?= $folio ?>.pdf';
document.body.appendChild(link);
link.click();
document.body.removeChild(link);

setTimeout(() => {
    window.location.href = '../index.php';
}, 4500);
</script>
</body>
</html>
