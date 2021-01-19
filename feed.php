<?php 
session_start();
if (!isset($_SESSION['connected_id'])){
    header('Location: login.php');
    exit();
}
include 'header.php';
include 'buttonlikelogic.php';
print_r($postlike_array);
?>
        <div id="wrapper">
            <?php
            /**
             * Cette page est TRES similaire à wall.php.
             * Vous avez sensiblement à y faire la meme chose.
             * Il y a un seul point qui change c'est la requete sql.
             */
            /**
             * Etape 1: Le mur concerne un utilisateur en particulier
             */
            $userId = $_GET['user_id'];
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
                 * Etape 3: récupérer le nom de l'utilisateur
                 */
                $laQuestionEnSql = "SELECT * FROM `users` WHERE id=" . intval($userId);
                $lesInformations = $mysqli->query($laQuestionEnSql);
                $user = $lesInformations->fetch_assoc();
                //@todo: afficher le résultat de la ligne ci dessous, remplacer XXX par l'alias et effacer la ligne ci-dessous
                ?>
                <img src="user.jpg" alt="Portrait de l'utilisatrice"/>
                <section>
                    <h3>Présentation</h3>
                    <p>Sur cette page vous trouverez tous les message des utilisatrices
                        auxquel est abonnée l'utilisatrice <?php echo $user['alias'] ?>
                        (n° <?php echo $_GET['user_id'] ?>)
                    </p>

                </section>
            </aside>
            <main>
                <?php
                /**
                 * Etape 3: récupérer tous les messages des abonnements
                 */
                $laQuestionEnSql = "SELECT `posts`.`content`,"
                        . "`posts`.`created`,"
                        . "`users`.`alias` as author_name,  "
                        . "`users`.`id` as user_id,  "
                        . "`posts`.`id` as post_id,  "
                        . "count(`likes`.`id`) as like_number,  "
                        . "GROUP_CONCAT(distinct`tags`.`label`) AS taglist "
                        . "FROM `followers` "
                        . "JOIN `users` ON `users`.`id`=`followers`.`following_user_id`"
                        . "JOIN `posts` ON `posts`.`user_id`=`users`.`id`"
                        . "LEFT JOIN `posts_tags` ON `posts`.`id` = `posts_tags`.`post_id`  "
                        . "LEFT JOIN `tags`       ON `posts_tags`.`tag_id`  = `tags`.`id` "
                        . "LEFT JOIN `likes`      ON `likes`.`post_id`  = `posts`.`id` "
                        . "WHERE `followers`.`followed_user_id`='" . intval($userId) . "' "
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
                 * A vous de retrouver comment faire la boucle while de parcours...
                 */
                 while ($post = $lesInformations->fetch_assoc())
                 {
                    if ($postlike_array[$post['post_id']]==1){

                        $btnLike = "Unlike";



                        } else {
                        $btnLike = "Like ! ♥";

                        }
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

                        <small>♥<?php echo $post['like_number'] ?></small>
                        <small>
                                <form method="post">
                                    <input type='submit' name='likeButton' value="<?php echo $btnLike ?>">
                                    <input type="hidden" name="postId" value="<?php echo $post['post_id'] ?>">
                                </form>
                        </small>
                        <a href=""><?php echo $post['taglist'] ?></a>,
                        <!--<a href="">#piscitur</a>,-->
                    </footer>
                </article>
                <?php
              }
                // et de pas oublier de fermer ici vote while
                ?>


            </main>
        </div>
    </body>
</html>
