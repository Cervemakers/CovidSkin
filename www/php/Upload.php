<?php
//Si se quiere subir una imagen
if (isset($_POST['covid'])) {

    //Recogemos el archivo enviado por el formulario
    $archivo = date("Y-m-d H:i:s") . rand(); //-->Nombre que tendrá el archivo para evitar duplicidad y poderlo ordenar por fecha
    $json->result = "";
    $json->message = "";

    //Si el archivo contiene algo y es diferente de vacio
    if (isset($archivo) && $archivo != "") {
        //Obtenemos algunos datos necesarios sobre el archivo
        $tipo = $_FILES['image']['type'];
        $tamano = $_FILES['image']['size'];
        $temp = $_FILES['image']['tmp_name'];
	$extension = '.' . end(explode('.',$_FILES['image']['name'])); //-->Extension del archivo original
        $csvdata = "";
        $folder = "../BBDD/";

        $data = $_POST;
        $csvdata = "imatge,email,naixement,sexe,estatCovid,simptomesCovid\r\n";
        $csvdata = $csvdata . '"' . $archivo . $extension . '",'  . $_POST['email'] . "," . $_POST['age'] . "," . $_POST['gender'] . "," . $_POST['covid'] . "," . $_POST['covidSymptoms'];

        //Se comprueba si el archivo a cargar es correcto observando su extensión y tamaño
        if (!((strpos($tipo, "jpeg") || strpos($tipo, "jpg") || strpos($tipo, "png")) && ($tamano < 4000000))) {
            $json->result = "Error";
            $json->message = "L'extensió o el tamany dels arxius no es correcta.<br/>Es permeten el arxius .jpg, .jpeg, .png i de 4MB com a màxim.";
        }
        else {
            //Si la imagen es correcta en tamaño y tipo
            //Se intenta subir al servidor
            if (move_uploaded_file($temp, $folder . $archivo . $extension)) {
                //Cambiamos los permisos del archivo a 777 para poder modificarlo posteriormente
                chmod($folder . $archivo, 0777);

                //Generamos el CSV con toda la informacion del resto del formulario asi como con el nombre de la imagen
                $fname = $folder . $archivo . '.csv';
                $fp = fopen($fname,'wb');
                fwrite($fp,$csvdata);
                fclose($fp);

                //header("Content-Disposition: inline; filename=".$fname);
                //readfile($fname);

                //Mostramos el mensaje de que se ha subido con éxito
                $json->result = "OK";
                $json->message = "S'han pujat correctament les dades.";
            }
            else {
                //Si no se ha podido subir la imagen, mostramos un mensaje de error
                $json->result = "Error";
                $json->message = "Hi ha hagut algun error al pujar el fitxer. No s\'ha pogut guardar.";
            }
        }
    }

    //Devolvemos el resultado de todo el proceso
    header('Content-type: application/json');
    echo json_encode($json);

}

?>
