

<?php 

    error_reporting(E_ALL);


    $valid = 1;  // for stopping invalid form 1 - PASS, 0 - FAIL, 2 - FAIL, 3 - PASS
    $errMsg = ""; // for general errors

    $name1 = $name2 = $name3 = $salutation = $age = $email = $phone = $date = $comment = "";

    
    if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['download'])) {

        // validation

        //Validation of Required fields
        if (empty ($_POST["nameFirst"]) || empty ($_POST["nameLast"]) || empty ($_POST["age"]) || empty ($_POST["email"]) || empty ($_POST["dateArrive"])){
            $valid = 0;
            $errMsg = "Some required field is empty";
        }
        

        //   Validation of the First, Middle, Last Name
        $name1 = $_POST ["nameFirst"];
        $name2 = $_POST ["nameMiddle"];
        $name3 = $_POST ["nameLast"];

        function testNames($o){  
            global $name1, $name2, $name3, $valid;
            if (preg_match ("/[\u{0021}-\u{0026}\u{0028}-\u{002C}\u{002E}-\u{0040}\u{005B}-\u{005E}\u{007B}-\u{007E}]/", $o)) {  //basic bad character filtered (matches bad chars)
                $valid = 0;
                $name1 = "Name: Wrong format";
                $name2 = "";
                $name3 = "";
            }
            if( (substr($o, 0, 1) == " ") || (substr($o, 0, 1) == "-") || (substr($o, 0, 1) == "'") ) {  // no space instead of character
                $valid = 0;
                $name1 = "Name: Wrong format";
                $name2 = "";
                $name3 = "";
            }
        }

        testNames($name1);
        testNames($name2);
        testNames($name3);
        


        //Salutation
        if (isset($_POST["salute"])) {
            $salutation = $_POST["salute"];
            if($salutation != "mr" && $salutation != "ms"
            && $salutation != "mrs" && $salutation != "sir"
            && $salutation != "prof" && $salutation != "dr" ){
                $valid = 0;
                $salutation = "Salutation: Wrong input";
            }
        }


        //Validation of Age
        $agecheck = $_POST["age"];

        if(substr($agecheck, 0, 1) == "0"){
            $valid = 0;
            $age = "Age: No zeros are allowed!";
            if($agecheck < 18 || $agecheck > 119){
                $valid = 0;
                $age .= "Age: Too young or doubtfully old!";
            }
        
        } else if($agecheck < 18 || $agecheck > 119){
            $valid = 0;
            $age = "Age: Too young or doubtfully old!";
        } else {
            $age = $_POST["age"];
        }

        

        // Validation of Email
        $testemail = $_POST["email"];
        $email = $_POST["email"];

        $pattern = "^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$^";  
        if (!preg_match ($pattern, $testemail) || (count(explode("@", $testemail)) > 2 ) ){   
            $valid = 0;
             $email = "Email: Bad format";
        }  else {

            $testemail = explode("@", $testemail);

            if ( (count(explode(".", $testemail[1])) > 2) || ((count(explode(".", $testemail[0])) > 2))  ){   
                $valid = 0;
                $email = "Email: Too many dots!";
            }  

        }


        // Validation of Phone
        $phone = $_POST ["phone"];  
        
        if (strlen($phone) > 0) {
            if (!preg_match("/^[+]+[0-9 -]*$/", $phone) || strlen($phone) > 24){  
                $valid = 0;
                $phone = "Phone: Bad format"; 
            }  
        }



        //Validation of Date. 

        try {
            function check ($date) {  // Ensures that date has correct type and format.

                if (($date != NULL) && (gettype($date) === 'string' ) ){  // Check data presence. Check it is a string

                    $date = explode("-", $date);

                    if( (count($date) == 3) && (strlen($date[0]) == 4) && (strlen($date[1]) == 2) && (strlen($date[2]) == 2)  ) {  // Check length (4-2-2)

                        if (
                        (is_numeric($date[0]) == 1) &&   // Check that all data are numbers
                        (is_numeric($date[1]) == 1) &&
                        (is_numeric($date[2]) == 1)
                        ) {
                        
                            if( checkdate($date[1], $date[2], $date[0]) ){  // Check that date is real (no 29 Feb)
                        
                            } else {
                                throw new Exception("bad number!");
                            }


                        } else {
                            throw new Exception("extraneous chars detected");                           
                        }

                    } else {
                        throw new Exception("bad length");   
                    }

                } else {
                    throw new Exception("no date or wrong data type");
                }
            }

            //$dateValue = "2022-11-11"; // Testing variable. Paste inside check function
            check ($_POST["dateArrive"]); 

            $date = date_create($_POST["dateArrive"]);
            $date = date_format($date, "Y-m-d");  
            // $testdate = date('Y-m-d', strtotime($_POST["dateArrive"]));   //  alternative version
            $testDateBegin = date('Y-m-d', strtotime("2022-01-01"));
            $testDateEnd = date('Y-m-d', strtotime("2032-01-01"));
                
            if (($date >= $testDateBegin) && ($date <= $testDateEnd)){        //Check date segment between current year plus 10 years
            }
            else{
                throw new Exception("bad data segment");
            }


        } catch (Exception $e) {
            $valid = 0;
            $date = "Date: " . $e->getMessage();
        }
        


       // Validation of comment
       $comment = $_POST["comment"];
   
       if(strlen($comment) > 1001) { 
           $valid = 0; 
           $comment = "Comment: Too long comment(";
        }

      

        // function trim data
        function input_data($data) {  
            $data = trim($data);  
            $data = stripslashes($data); 
            return $data;  
        }
        

        // Making an array
        $donald = array( input_data($name1), input_data($name2), input_data($name3), $salutation, $age, $email, $phone, $date, input_data($comment));
        


        // writing into data.csv file. It contains all registrations
        $text = "";
        if ($valid == 1){

            // for($i = 1; $i < 9; $i++) //Put array data inside of a string for myformdata.csv
            // {$text .= $donald[$i]; $text .= ";";}
            // $text .= $donald[9];

            // $handle = fopen('data.csv', "a+");  //Put this string inside of a .csv text document
            // fwrite($handle , $text);
            // fclose($handle);
        

            $fp = fopen('data.csv', 'a+');  // puts CSV FORMAT data inside data.csv
            fputcsv($fp, $donald, ";", '"');
            fclose($fp);

        
        }


   
        //finally allowing a response
        if ($valid == 1){ $valid = 3;}
        else { $valid = 2;}
        
    }


    
    // Download form data
    if (isset($_POST['download'])) {
          
        $donald = array( $_POST['name1'], $_POST['name2'], $_POST['name3'],
        $_POST['salutation'], $_POST['age'], $_POST['email'],$_POST['date'],
        $_POST['comment'] );
        

        $fp = fopen('myformdata.csv', 'w');
        fputcsv($fp, $donald, ";", '"');
        fclose($fp);



        
        // $handle = fopen('myformdata.csv', "w");  //Put this string inside of a .csv text document
        // fwrite($handle , $texx);
        // fclose($handle);




        $file = 'myformdata.csv';

                header('Content-Description: File Transfer');
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="'.basename($file).'"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($file));
                readfile($file);
                exit;
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

            <form action="index.php" method="POST" id="formReserve">
                <!-- First name -->
                <label for="nameFirst">First name: </label>   
                <input type="text" id="nameFirst" name="nameFirst"  minlength="1" size="30" required><span class="required"></span><br>
                <!-- Middle name -->
                <label for="nameMiddle">Middle name: </label>
                <input type="text" id="nameMiddle" name="nameMiddle" size="30"><br>
                <!-- Last name -->
                <label for="nameLast">Last name: </label>
                <input type="text" id="nameLast" name="nameLast"  minlength="1" size="30" required ><span class="required"></span><br>
                <!-- Salutation -->
                <legend>Salutation</legend>
                <div class=sal>
                    <label for="saluteMr">Mr</label>
                    <input type="radio" id="saluteMr" name="salute" value="mr">
                    <label for="saluteMs">Ms</label>
                    <input type="radio" id="saluteMs" name="salute" value="ms">
                    <label for="saluteMrs">Mrs</label>
                    <input type="radio" id="saluteMrs" name="salute" value="mrs">
                    <label for="saluteSir">Sir</label>
                    <input type="radio" id="saluteSir" name="salute" value="sir">
                    <label for="saluteProf">Prof</label>
                    <input type="radio" id="saluteProf" name="salute" value="prof">
                    <label for="saluteDr">Dr</label>
                    <input type="radio" id="saluteDr" name="salute" value="dr">
                    <br> <br>
                </div>
                <!-- Age -->
                <label for="age">Age: </label>
                <input type="number" id="age" name="age" size="6" min="18" required> <span class="required"></span><br> 
                <!-- E-mail -->
                <label for="email">E-mail: </label>
                <input type="email" id="email" name="email" required > <span class="required"></span><br>  
                <!-- Phone -->
                <label for="phone">Phone: </label>
                <input type="tel" id="phone" name="phone" pattern="[+]+[0-9 ]{2,}"><br>
                <!-- Date of arrival -->
                <label for="dateArrive">Date of arrival: </label>
                <input type="date" id="dateArrive" name="dateArrive" required min="2022-01-01" max="2032-01-01"> <span class="required"></span><br>  
                <!-- Comment -->
                <label for="comment">Comment: </label>
                <input type="text" id="comment" name="comment" size="56" max="1000" placeholder="Your comment. Max 1000 chars">
                <br>
                <!-- Submit -->
                <input type="submit" value="Submit" id="submitReservation" name="submitReservation">
                <!-- Reset -->
                <input type="reset" value="Reset"> 
            </form>
        



            <!-- PASS -->
        <?php if ( $_SERVER["REQUEST_METHOD"] == "POST" && $valid == 3) { ?>
            <div id="confirmedText">
                <?php
                        echo "<h2>Success!</h2>";
                        echo "Dear " . $salutation . " " .$name1 . " " . $name2 . " " .$name3 . ", you have been succesfully registered!";
                        echo "<br>";
                        echo "Your age:" . $age;
                        echo "<br>";
                        echo "Your e-mail: " . $email;
                        echo "<br>";
                        if( !empty($phone) ){ echo "Your phone: " . $phone; }                    
                        echo "<br>";
                        echo "Date of arrival: " . $date;
                        echo "<br>";
                        if( !empty($comment) ){ echo "Comment: " . $comment; }
                ?>
                        <!-- Download my data button -->
                        <form action="index.php" method="POST">
                        <h3>Download your form data</h3>
                        <input type= "submit" name="download" value="Download">
                        <input type="hidden" name="name1" value="<?php echo $name1 ?>">
                        <input type="hidden" name="name2" value="<?php echo $name2 ?>">
                        <input type="hidden" name="name3" value="<?php echo $name3 ?>">
                        <input type="hidden" name="salutation" value="<?php echo $salutation ?>">
                        <input type="hidden" name="age" value="<?php echo $age ?>">
                        <input type="hidden" name="email" value="<?php echo $email ?>">
                        <input type="hidden" name="phone" value="<?php echo $phone ?>">
                        <input type="hidden" name="date" value="<?php echo $date ?>">
                        <input type="hidden" name="comment" value="<?php echo $comment ?>">
                        </form>
            </div>
        
        <?php } ?>


        <!-- FAIL -->
        <?php if ( $_SERVER["REQUEST_METHOD"] == "POST" && $valid == 2) { ?>  
            <div id="confirmedError">
                    <?php
                            echo "<h2>Failure!</h2>";
                            echo "We have failed to send your form because of wrong input";
                            echo "<br>";
                            echo "<h3> Review your data to improve errors: </h3>";
                            echo "<br>";
                            if( !empty($errMsg) ){ echo $errMsg; }
                            echo "<br>";   
                            for($i = 1; $i < 10; $i++){
                                
                                if( !empty($donald[$i]) ){ 
                                    echo $donald[$i]; 
                                }                    
                                echo "<br>";

                            } 
                    ?> 
            </div>

        <?php }?>




                       

    </body>
</html>




