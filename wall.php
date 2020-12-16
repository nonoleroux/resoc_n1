<?php
session_start();

$mysqli = new mysqli("localhost", "root", "", "socialnetwork");

//on recupere les info du proprietaire du mur
$userId= $_GET['user_id'];
$laQuestionEnSql = "SELECT * FROM `users` WHERE id=" . intval($userId);
$lesInformations = $mysqli->query($laQuestionEnSql);
$user = $lesInformations->fetch_assoc();


//Follow-Unfollow button
$aQuiSuisJeAbonnee= "SELECT *
FROM `followers`
WHERE `followed_user_id` ='".intval($_SESSION['connected_id']) . "'"
."AND `following_user_id` ='". $userId. "'" ;
$infoAbonnements = $mysqli->query($aQuiSuisJeAbonnee);
$dejaabonne=($infoAbonnements->num_rows!=0);


if  ($dejaabonne){
    $valueButtonFollow="Unfollow";
}
else {
    $valueButtonFollow="Follow";
}

//je verefie si c'est le formulaire de suivi
if (isset($_POST['followButton'])){


    if ($dejaabonne){
        $supprimeabo= "DELETE
        FROM `followers`
        WHERE `followed_user_id` ='".intval($_SESSION['connected_id']) . "'"
        ."AND `following_user_id` ='". $userId. "'";
        $requetesup = $mysqli->query($supprimeabo);
        $valueButtonFollow="Follow";
    }
    else {

        $nouvelAbo = "INSERT INTO `followers`(`id`, `followed_user_id`, `following_user_id`) "
        . "VALUEs (NULL, "
        . "" . $_SESSION['connected_id'] . ", "
        . "'" . $userId . "'"
        . ");";

        $ok = $mysqli->query($nouvelAbo);

        if ($ok) {
            echo "Vous êtes abonnée à " . $user['alias'] . " !";
            $valueButtonFollow="Unfollow";

        }
        else {
            echo "somthing went wrong";
        }

    }
}

?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>ReSoC - Mur</title>
    <meta name="author" content="Julien Falconnet">
    <link rel="stylesheet" href="style.css"/>
</head>
<body>
    <header>
        <img src="resoc.jpg" alt="Logo de notre réseau social"/>
        <nav id="menu">
            <a href="news.php">Actualités</a>
            <a href="wall.php?user_id=<?= $_SESSION['connected_id']?>">Mur</a>
            <a href="feed.php?user_id=<?= $_SESSION['connected_id']?>">Flux</a>
            <a href="tags.php?tag_id=1">Mots-clés</a>
        </nav>
        <nav id="user">
            <a href="#">Profil</a>
            <ul>
                <li><a href="settings.php?user_id=<?= $_SESSION['connected_id']?>">Paramètres</a></li>
                <li><a href="followers.php?user_id=<?= $_SESSION['connected_id']?>">Mes suiveurs</a></li>
                <li><a href="subscriptions.php?user_id=<?= $_SESSION['connected_id']?>">Mes abonnements</a></li>
                <li><a href="subscriptions.php?user_id=<?= $_SESSION['connected_id']?>">Deconnexion</a></li>
            </ul>

        </nav>
    </header>
    <div id="wrapper">
        <?php

        $userId= $_GET['user_id'];



        $mysqli = new mysqli("localhost", "root", "", "socialnetwork");
        ?>

        <aside>

            <img src="user.jpg" alt="Portrait de l'utilisatrice"/>
            <section>
                <h3>Présentation</h3>
                <p>Sur cette page vous trouverez tous les message de l'utilisatrice : <?php echo $user['alias'] ?>
                    (n° <?php echo $_GET['user_id'] ?>)
                </p>

<?php if($userId!==$_SESSION['connected_id']){ ?>
    <form method="post">

        <input type='submit' name='followButton' value=<?php echo $valueButtonFollow ?>> <?php } ?>

</form>
</section>
</aside>
<main>
    <?php if ($_SESSION['connected_id'] == $userId){ ?>
        <article>

            <h2>Poster un message</h2>
            <?php
            $mysqli = new mysqli("localhost", "root", "", "socialnetwork");
            /**
            * Récupération des informations sur l'utilsateur connecté
            */
            $requeteAlias = "SELECT `alias` FROM `users` WHERE id=" . intval($userId);
            $lesInformationsAlias = $mysqli->query($requeteAlias);
            $alias = $lesInformationsAlias->fetch_assoc();

            $enCoursDeTraitement = isset($_POST['submit']);
            if ($enCoursDeTraitement)
            {
                $authorId = $_SESSION['connected_id'];
                $postContent = $_POST['message'];


                //Etape 3 : Petite sécurité
                // pour éviter les injection sql : https://www.w3schools.com/sql/sql_injection.asp
                $authorId = intval($mysqli->real_escape_string($authorId));
                $postContent = $mysqli->real_escape_string($postContent);

                $lInstructionSql = "INSERT INTO `posts` "
                . "(`id`, `user_id`, `content`, `created`, `parent_id`) "
                . "VALUES (NULL, "
                . "" . $authorId . ", "
                . "'" . $postContent . "', "
                . "NOW(), "
                . "NULL);"
                . "";


                $ok = $mysqli->query($lInstructionSql);
                if ( ! $ok)
                {
                    echo "Impossible d'ajouter le message: " . $mysqli->error;
                }
            }
            ?>
            <form action= "" method="post">
                <input type='hidden' name='???' value='achanger'>
                <dl>

                    <dt><label for='message'>Message</label></dt>
                    <dd><textarea name='message'></textarea></dd>
                </dl>
                <input type='submit' value ='submit' name='submit'>
            </form>
        </article>
        <?php
    }
    ?>
    <?php
    /**
    * Etape 3: récupérer tous les messages de l'utilisatrice
    */
    $laQuestionEnSql = "SELECT `posts`.`content`,"
    . "`posts`.`created`,"
    . "`users`.`alias` as author_name,  "
    . "`users`.`id` as user_id,  "
    . "count(`likes`.`id`) as like_number,  "
    . "GROUP_CONCAT(distinct`tags`.`label`) AS taglist "
    . "FROM `posts`"
    . "JOIN `users` ON  `users`.`id`=`posts`.`user_id`"
    . "LEFT JOIN `posts_tags` ON `posts`.`id` = `posts_tags`.`post_id`  "
    . "LEFT JOIN `tags`       ON `posts_tags`.`tag_id`  = `tags`.`id` "
    . "LEFT JOIN `likes`      ON `likes`.`post_id`  = `posts`.`id` "
    . "WHERE `posts`.`user_id`='" . intval($userId) . "' "
    . "GROUP BY `posts`.`id`"
    . "ORDER BY `posts`.`created` DESC  "
    ;
    $lesInformations = $mysqli->query($laQuestionEnSql);
    if ( ! $lesInformations)
    {
        echo("Échec de la requete : " . $mysqli->error);
    }

    while ($post = $lesInformations->fetch_assoc())
    {

        ?>

        <article>
            <h3>
                <time datetime='2020-02-01 11:12:13' ><?php echo $post['created']?></time>
            </h3>
            <address><a href="wall.php?user_id=<?php echo $post['user_id'] ?>">  <?php echo $post['author_name'] ?></a></address>
            <div>
                <p><?php echo $post['content'] ?></p>
            </div>
            <footer>
                <small>♥ <?php echo $post['like_number'] ?></small>
                <a href=""><?php echo $post['taglist'] ?></a>,

            </footer>
        </article>
    <?php } ?>


</main>
</div>
</body>
</html>
