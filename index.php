<?php
// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Vérifier si une image a été téléchargée
    if (empty($_FILES["image_file"]["tmp_name"])) {
        header("Location:index.php?message=er");
    }


    // Obtenir le nom de l'image sans l'extension
    $file_basename = pathinfo($_FILES["image_file"]["name"], PATHINFO_FILENAME);


    // Renommer l'image en y ajoutant le nom de base et la date et l'heure
    $file_extension = pathinfo($_FILES["image_file"]["name"], PATHINFO_EXTENSION);
    $new_image_name = $file_basename . '_' . date("Ymd_His") . '.' . $file_extension;


    // Inclure les informations de connexion à votre base de données
    $servername = "127.0.0.1";
    $username = "root";
    $password = "";
    $dbname = "info";


    // Connexion à la base de données MySQL
    $conn = new mysqli($servername, $username, $password, $dbname);


    // Vérifier si la connexion a réussi
    if ($conn->connect_error) {
        die("La connexion à la base de données a échoué : " . $conn->connect_error);
    }


 
    // Échapper les données pour éviter les attaques d'injection SQL
    $new_image_name = $conn->real_escape_string($new_image_name);


    // Requête d'insertion dans la table "image"
    $sql = "INSERT INTO image (libelle) VALUES ('$new_image_name')";


    if ($conn->query($sql) === TRUE) {
        // Déplacer l'image vers le dossier "images"
        $target_directory = "images/";
        $target_path = $target_directory . $new_image_name;
        if (!move_uploaded_file($_FILES["image_file"]["tmp_name"], $target_path)) {
            header("Location:index.php?message=er");
        }
        //redirection
        header("Location:index.php?message=ok");
    } else {
        header("Location:index.php?message=no");
    }


    // Fermer la connexion à la base de données
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <form action="" method="post" enctype="multipart/form-data">
        <?php if (isset($_GET["message"])) {
            if ($_GET["message"] == "ok") {
                ?>
                <div class="alert sucess">
                    <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
                    L'insertion de l'image dans la bdd  a réussi !
                </div>
                <?php
            } else {
                ?>
                <div class="alert fail">
                    <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
                    L'insertion de l'image dans la bdd a échoué !
                </div>
                <?php
            }
        } ?>


        <label for="images" class="drop-container" id="dropcontainer">
            <span class="drop-title">Déposez les fichiers ici</span>
            ou
            <input type="file" name="image_file" id="images" accept="image/*" required>
        </label>
        <button type="submit" id="submitBtn">Enregister l'image</button>
    </form>
    <script src="script.js"></script>
</body>
</html>
