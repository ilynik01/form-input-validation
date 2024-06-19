
<?php

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['downloaddata'])) {

    $file = 'data.csv';

    if (file_exists($file)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.basename($file).'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        readfile($file);
        exit;
    }

}

?>


<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="stylesheet" href="styles/style.css">
        <meta charset="utf-8"> 
        <title>Form validation</title>


    </head>

    <body>
        <nav>
            <a href="index.php">Index</a>
            <a href="download.php">Download</a>
        </nav>

        <form action="download.php" method="POST">

        <div id="reservedText">
            <h3> Number of registrations: 
                
            <?php 
            $fileline="data.csv";
            $linecount = 0;
            $handle = fopen($fileline, "r");
            while(!feof($handle)){        // counting number of registrated people
            $line = fgets($handle);
            $linecount = $linecount + substr_count($line, "\n");
            }

            fclose($handle);

            echo $linecount;
            ?>

            </h3>
        </div>

            <!-- Download button -->
            <input type="submit" value="Download data" id="downloaddata" name="downloaddata">

        </form>


        </body>
</html>
