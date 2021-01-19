<?php session_start();
if (!isset($_SESSION['connected_id'])){
    header('Location: login.php');
    exit();
}
include 'header.php' 
?>
        <div id="wrapper">
            <?php
            /**
             * Cette page est similaire à wall.php ou feed.php
             * mais elle porte sur les mots-clés (tags)
             */
            /**
             * Etape 1: Le mur concerne un mot-clé en particulier
             */
            $tagId = $_GET['tag_id'];
            ?>
            <?php
            /**
             * Etape 2: se connecter à la base de donnée
             */
            $mysqli = new mysqli("localhost", "root", "", "socialnetwork");
            ?>

            <aside>
                <?php
                /**
                 * Etape 3: récupérer le nom du mot-clé
                 */
                $laQuestionEnSql = "SELECT * FROM `tags` WHERE id=" . intval($tagId);
                $lesInformations = $mysqli->query($laQuestionEnSql);
                $tag = $lesInformations->fetch_assoc();
                //@todo: afficher le résultat de la ligne ci dessous, remplacer XXX par le label et effacer la ligne ci-dessous

                ?>
                <img src="user.jpg" alt="Portrait de l'utilisatrice"/>
                <section>
                    <h3>Présentation</h3>
                    <p>Sur cette page vous trouverez les derniers messages comportant
                        le mot-clé <?php echo $tag['label'] ?>
                        (n° <?php echo $_GET['tag_id'] ?>)
                    </p>

                </section>
            </aside>
            <main>
                <?php
                /**
                 * Etape 3: récupérer tous les messages avec un mot clé donné
                 */
                $laQuestionEnSql = "SELECT `posts`.`content`,"
                        . "`posts`.`created`,"
                        . "`users`.`alias` as author_name,  "
                        . "`users`.`id` as user_id,  "
                        . "count(`likes`.`id`) as like_number,  "
                        . "GROUP_CONCAT(DISTINCT `tags`.`label`) AS taglist "
                        . "FROM `posts_tags` as filter "
                        . "JOIN `posts` ON `posts`.`id`=filter.`post_id`"
                        . "JOIN `users` ON `users`.`id`=`posts`.`user_id`"
                        . "LEFT JOIN `posts_tags` ON `posts`.`id` = `posts_tags`.`post_id`  "
                        . "LEFT JOIN `tags`       ON `posts_tags`.`tag_id`  = `tags`.`id` "
                        . "LEFT JOIN `likes`      ON `likes`.`post_id`  = `posts`.`id` "
                        . "WHERE filter.`tag_id`='" . intval($tagId) . "' "
                        . "GROUP BY `posts`.`id`"
                        . "ORDER BY `posts`.`created` DESC  "
                ;
                $lesInformations = $mysqli->query($laQuestionEnSql);
                if ( ! $lesInformations)
                {
                    echo("Échec de la requete : " . $mysqli->error);
                }

                /**
                 * Etape 4: @todo Parcourir les messsages et remplir correctement le HTML avec les bonnes valeurs php
                 */
                while ($post = $lesInformations->fetch_assoc())
                {


                    ?>
                    <article>
                        <h3>
                            <time datetime='2020-02-01 11:12:13' >31 février 2010 à 11h12</time>
                        </h3>
                        <address><a href="wall.php?user_id=<?php echo $post['user_id'] ?>">  <?php echo $post['author_name'] ?></a></address>
                        <div>
                            <p><?php echo $post['content'] ?></p>
                        </div>
                        <footer>
                            <small>♥ <?php echo $post['like_number'] ?></small>
                            <a href=""><?php echo $post['taglist'] ?></a>,
                            <!-- <a href="">#piscitur</a>, -->
                        </footer>
                    </article>
                <?php } ?>


            </main>
        </div>
    </body>
</html>
