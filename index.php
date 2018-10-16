<?php
require __DIR__ . '/favicon-generator/src/FaviconGenerator.php';

$metaFavicons = array();

if(!empty($_POST) && !empty($_FILES)){

    try{
        $urlSite = "http://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];

        //directory to save file uploaded
        $dirToSaveFile = __DIR__ . '/img/';

        //directory to save favicon generated
        $dirToSaveFileFavicon = __DIR__ . '/favicon/';

        //get file uploaded type
        $fileType = explode("/", $_FILES['imagem_favicon']['type']);
        if(sizeof($fileType) == 2){
            $fileType = '.' . $fileType[1];
        }else{
            die('Erro: Formato de arquivo inválido.');
        }

        $uploadFile = $dirToSaveFile . 'logo-custom' . $fileType;

        if (move_uploaded_file($_FILES['imagem_favicon']['tmp_name'], $uploadFile)) {
            chmod($uploadFile, 0755);
            echo "<p>Arquivo válido, upload foi realizado com sucesso!</p>";
            echo "<pre>Detalhes do arquivo: ";
            print_r($_FILES);
            echo "</pre>";
        } else {
            echo "Upload de arquivo falhou!";
        }

        if(file_exists($uploadFile)){
            $fav = new FaviconGenerator($uploadFile);

            $fav->setCompression(FaviconGenerator::COMPRESSION_VERYHIGH);
            $fav->setConfig(array(
                'apple-background'    => FaviconGenerator::COLOR_GRAY,
                'apple-margin'        => 12,
                'android-background'  => FaviconGenerator::COLOR_GRAY,
                'android-margin'      => 12,
                'android-name'        => 'Site',
                'android-url'         => $urlSite,
                'android-orientation' => FaviconGenerator::ANDROID_PORTRAIT,
                'ms-background'       => FaviconGenerator::COLOR_GRAY,
            ));

            if($fav->createAll()){
                $metaFavicons = $metaFavicons = $fav->getHtml();
                echo "<h3>Favicons foram gerados com sucesso!</h3>";
              //  chmod($dirToSaveFileFavicon, 0777);
                $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dirToSaveFileFavicon), RecursiveIteratorIterator::SELF_FIRST);

                foreach($iterator as $item) {
                    chmod($item, 0777);
                }
            }else{
                echo "<p>Erro: houve alguma falha ao tentar gerar os arquivos favicons!</p>";
            }
        }
    }catch(Exception $e){
        die($e);
    }
}

?>
<html>
    <head>
        <title>PHP Favicon Generator</title>
        <link rel="stylesheet" href="css/style.css">
        <?php
            if(!empty($metaFavicons)) {
                print_r($metaFavicons);
            }
        ?>
    </head>
    <body>
        <h1>PHP Favicon Generate (Cross Browser)</h1>

        <form enctype="multipart/form-data" action="" method="POST">
            <p><small>Add a image (.png), size like a square (ex: 256px x 256px)</small></p>
            <input type="hidden" name="MAX_FILE_SIZE" value="512000" />
            <input type="file" name="imagem_favicon" class="input-custom">

            <p><small>Obs: Para melhor exibição e dimensionamento da imagem para favicon, o ideal é que o tipo do arquivo seja .PNG</small></p>
            <button type="submit" class="btn-custom">Gerar Favicon</button>
        </form>
    </body>
</html>