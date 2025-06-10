<?php
require_once('phpqrcode/qrlib.php'); // chemin vers la librairie PHP QR Code

if (isset($_POST['upload'])) {

    //fonction directory
    $dist = makedir("upload/");

    // Récupération du fichier + vérification d'image
    $tmp_file = $_FILES['fichier']['tmp_name'];
    if (ValideImage($tmp_file)) {
        echo "Le fichier n'est pas une image";
        return;
    }

    $name = basename($_FILES['fichier']['name']);
    $img_path = $dist . $name;

    //verifcation de la copie
    if (!move($tmp_file, $img_path)) {
        echo "Impossible de copier le fichier dans $dist";
        return;
    }

    echo "<p>Image uploadée : <a href=\"$img_path\">$name</a></p>";

    // Fichiers temporaires et finaux
    $tmp_qr = $dist . 'tmp_qr.png';
    $final_qr = $dist . 'qr_' . pathinfo($name, PATHINFO_FILENAME) . '.png';
    // 1. Paramètres QR choisis par l'utilisateur
    $config= createConfig();

    // 2. Génération du qrcode via la fonctions
    qrcodegen("http://localhost:8888/$img_path", $config['size'], $config['margin'], $config['color'], $tmp_qr, $final_qr);

    // 5. Affichage
    echo "<h2>QR Code généré</h2>";
    echo "<p>-- Taille : {$config['size']}, marge : {$config['margin']}, couleur : {$config['color']}</p>";
    echo "<img src=\"$final_qr\" alt=\"QR Code\"><br>";
    echo "<a href=\"$final_qr\" download>🔽 Télécharger le QR Code</a>";
}


function makedir($content_dir)
{
    if (!is_dir($content_dir)) {
        mkdir($content_dir, 0755, true);
    }
}

function ValideImage($tmp_file)
{
    return is_uploaded_file($tmp_file) && getimagesize($tmp_file) === false;
}

function CheckName($basename)
{
    $basename = basename($_FILES['fichier']['name']);
    return preg_replace("/[^a-zA-Z0-9\.\-_]/", "_", $basename);
}

function move($tmp_file, $img_path)
{
    return move_uploaded_file($tmp_file, $img_path);
}


function qrcodegen($url, $size, $margin, $color, $tmp_qr, $final_qr)
{

    // 2. Génération QR N&B
    QRcode::png($url, $tmp_qr, QR_ECLEVEL_L, $size, $margin);

    // 4. Recoloration via GD
    list($r, $g, $b) = sscanf($color, "#%02x%02x%02x");
    $src = imagecreatefrompng($tmp_qr);
    $w = imagesx($src);
    $h = imagesy($src);
    $dst = imagecreatetruecolor($w, $h);
    // Fond blanc
    $white = imagecolorallocate($dst, 255, 255, 255);
    imagefill($dst, 0, 0, $white);
    // Couleur du QR
    $fg = imagecolorallocate($dst, $r, $g, $b);

    // Itération pixel par pixel
    for ($x = 0; $x < $w; $x++) {
        for ($y = 0; $y < $h; $y++) {
            $pixel = imagecolorat($src, $x, $y) & 0xFFFFFF;
            // si noir dans le QR original
            if ($pixel == 0x000000) {
                imagesetpixel($dst, $x, $y, $fg);
            }
        }
    }

    imagepng($dst, $final_qr);
    imagedestroy($src);
    imagedestroy($dst);
    unlink($tmp_qr);
}

function createConfig(){
    return [
        "size" => max(1, intval($_POST['qr_size'])),
        "margin"=> max(0, intval($_POST['qr_margin'])),
        "color"=>$_POST['qr_color']
    ];
}