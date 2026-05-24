<?php
$content = file_get_contents('app/Http/Controllers/Dupak/PengajuanController.php');
$pos = strpos($content, 'Hapus detail kegiatan terkait');
if ($pos !== false) {
    echo substr($content, $pos - 20, 400);
} else {
    echo "Not found\n";
}
