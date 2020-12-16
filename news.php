<?php
session_start();
$userLiker = $_SESSION['connected_id'];

$mysqli = new mysqli("localhost", "root", "", "socialnetwork");
if ($mysqli->connect_errno) {
    echo ("Échec de la connexion : " . $mysqli->connect_error);
    echo ("<p>Indice: Vérifiez les parametres de <code>new mysqli(...</code></p>");
    exit();
}

// Etape 2: Poser une question à la base de donnée et récupérer ses informations
// cette requete vous est donnée, elle est complexe mais correcte,
// si vous ne la comprenez pas c'est normal, passez, on y reviendra
$laQuestionEnSql = "SELECT `posts`.`content`,"
    . "`posts`.`created`,"
    . "`users`.`alias` as author_name,  "
    . "`users`.`id` as user_id,  "
    . "`posts`.`id` as post_id,  "
    . "count(`likes`.`id`) as like_number,  "
    . "GROUP_CONCAT(distinct`tags`.`label`) AS taglist "
    . "FROM `posts`"
    . "JOIN `users` ON  `users`.`id`=`posts`.`user_id`"
    . "LEFT JOIN `posts_tags` ON `posts`.`id` = `posts_tags`.`post_id`  "
    . "LEFT JOIN `tags`       ON `posts_tags`.`tag_id`  = `tags`.`id` "
    . "LEFT JOIN `likes`      ON `likes`.`post_id`  = `posts`.`id` "
    . "GROUP BY `posts`.`id`"
    . "ORDER BY `posts`.`created` DESC  "
    . "LIMIT 10";
$lesInformations = $mysqli->query($laQuestionEnSql);
// Vérification
if (!$lesInformations) {
    echo ("Échec de la requete : " . $mysqli->error);
    echo ("<p>Indice: Vérifiez les la requete  SQL suivante dans phpmyadmin<code>$laQuestionEnSql</code></p>");
    exit();
}




if (isset($_POST['likeButton'])) {
    $postLiked = $_POST['postId'];
    // 1. connexion BDD
    $alreadyLiked="SELECT *
FROM `likes`
WHERE `user_id` ='".$userLiker ."'"
."AND `post_id`='".$postLiked."'";


$resultLike = $mysqli->query($alreadyLiked);
$isLiked=($resultLike->num_rows!=0);


    if (!$isLiked) {
        $likeRequete = "INSERT INTO `likes` "
            . "(`id`, `user_id`,`post_id`) "
            . "VALUES (NULL, "
            . "" . $userLiker . ", "
            . "'" . $postLiked . "'"
            . ");";

        $infoLike = $mysqli->query($likeRequete);
        $btnLike = "Unlike";

        // verification
        if (!$infoLike) {
            echo ("Échec de la requete like : " . $mysqli->error);
            exit();
        }
    } else {
        $unlikeRequest = "DELETE
    FROM `likes`
    WHERE `user_id` ='" . $userLiker . "'"
            . "AND `post_id`='" . $postLiked . "'";

        $resultUnlike = $mysqli->query($unlikeRequest);
        $btnLike = "Like ! ♥";
    }
}


?>
<!doctype html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>ReSoC - Actualités</title>
    <meta name="author" content="Julien Falconnet">
    <link rel="stylesheet" href="style.css" />
</head>

<body>
    <header>
        <img src="resoc.jpg" alt="Logo de notre réseau social" />
        <nav id="menu">
            <a href="news.php">Actualités</a>
            <a href="wall.php?user_id=<?= $_SESSION['connected_id'] ?>">Mur</a>
            <a href="feed.php?user_id=<?= $_SESSION['connected_id'] ?>">Flux</a>
            <a href="tags.php?tag_id=1">Mots-clés</a>
        </nav>
        <nav id="user">
            <a href="#">Profil</a>
            <ul>
                <li><a href="settings.php?user_id=<?= $_SESSION['connected_id'] ?>">Paramètres</a></li>
                <li><a href="followers.php?user_id=<?= $_SESSION['connected_id'] ?>">Mes suiveurs</a></li>
                <li><a href="subscriptions.php?user_id=<?= $_SESSION['connected_id'] ?>">Mes abonnements</a></li>
                <li><a href="subscriptions.php?user_id=<?= $_SESSION['connected_id'] ?>">Deconnexion</a></li>
            </ul>
        </nav>
    </header>
    <div id="wrapper">
        <aside>
            <img src="user.jpg" alt="Portrait de l'utilisatrice" />
            <section>
                <h3>Présentation</h3>
                <p>Sur cette page vous trouverez les derniers messages de
                    tous les utilisatrices du site.</p>
            </section>
        </aside>
        <main>
            <!-- L'article qui suit est un exemple pour la présentation et
                  @todo: doit etre retiré -->

            <?php
            /*
                  // C'est ici que le travail PHP commence
                  // Votre mission si vous l'acceptez est de chercher dans la base
                  // de données la liste des 5 derniers messsages (posts) et
                  // de l'afficher
                  // Documentation : les exemples https://www.php.net/manual/fr/mysqli.query.php
                  // plus généralement : https://www.php.net/manual/fr/mysqli.query.php
                 */


            //verification
            // if ($mysqli->connect_errno)
            // {
            //     echo("Échec de la connexion : " . $mysqli->connect_error);
            //     echo("<p>Indice: Vérifiez les parametres de <code>new mysqli(...</code></p>");
            //     exit();
            // }

            // // Etape 2: Poser une question à la base de donnée et récupérer ses informations
            // // cette requete vous est donnée, elle est complexe mais correcte,
            // // si vous ne la comprenez pas c'est normal, passez, on y reviendra
            // $laQuestionEnSql = "SELECT `posts`.`content`,"
            //         . "`posts`.`created`,"
            //         . "`users`.`alias` as author_name,  "
            //         . "`users`.`id` as user_id,  "
            //         . "`posts`.`id` as post_id,  "
            //         . "count(`likes`.`id`) as like_number,  "
            //         . "GROUP_CONCAT(distinct`tags`.`label`) AS taglist "
            //         . "FROM `posts`"
            //         . "JOIN `users` ON  `users`.`id`=`posts`.`user_id`"
            //         . "LEFT JOIN `posts_tags` ON `posts`.`id` = `posts_tags`.`post_id`  "
            //         . "LEFT JOIN `tags`       ON `posts_tags`.`tag_id`  = `tags`.`id` "
            //         . "LEFT JOIN `likes`      ON `likes`.`post_id`  = `posts`.`id` "
            //         . "GROUP BY `posts`.`id`"
            //         . "ORDER BY `posts`.`created` DESC  "
            //         . "LIMIT 10";
            // $lesInformations = $mysqli->query($laQuestionEnSql);
            // // Vérification
            // if ( ! $lesInformations)
            // {
            //     echo("Échec de la requete : " . $mysqli->error);
            //     echo("<p>Indice: Vérifiez les la requete  SQL suivante dans phpmyadmin<code>$laQuestionEnSql</code></p>");
            //     exit();
            // }

            // // Etape 3: Parcourir ces données et les ranger bien comme il faut dans du html
            // // NB: à chaque tour du while, la variable post ci dessous reçois les informations du post suivant.
            while ($post = $lesInformations->fetch_assoc()) {
                $alreadyLiked2 = "SELECT *
            FROM `likes`
            WHERE `user_id` ='" . $userLiker . "'"
                    . "AND `post_id`='" . $post['post_id'] . "'";


                $resultLike2 = $mysqli->query($alreadyLiked2);
                $isLiked2 = ($resultLike2->num_rows != 0);

                if ($isLiked2) {
                    $btnLike = "Unlike";
                } else {
                    $btnLike = "Like ! ♥";
                }

            ?>
                <article>
                    <h3>
                        <time><?= $post['created'] ?></time>
                        <!--ceci est un short-tag (pareil que les lignes du dessous)-->
                    </h3>
                    <address><a href="wall.php?user_id=<?php echo $post['user_id'] ?>"> <?php echo $post['author_name'] ?></a></address>
                    <div>
                        <p><?php echo $post['content'] ?></p>

                    </div>
                    <footer>

                        <small>♥<?php echo $post['like_number'] ?></small>
                        <small>
                            <form method="post">
                                <input type='submit' name='likeButton' value="<?php echo $btnLike ?>">
                                <input type="hidden" name="postId" value="<?php echo $post['post_id'] ?>">
                            </form>
                        </small>
                        <a href=""><?php echo $post['taglist'] ?></a>,
                    </footer>
                </article>
            <?php
                // avec le <?php ci-dessus on retourne en mode php
            } // cette accolade ferme et termine la boucle while ouverte avant.
            ?>

        </main>
    </div>
</body>

</html>