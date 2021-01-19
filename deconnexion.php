<?php 
session_start();
if (!isset($_SESSION['connected_id'])){
    header('Location: login.php');
    exit();
}
session_destroy();
include 'header.php';
?>
        <div id="wrapper" > 
            <main>
                <article>Vous avez bien été déconnecté</br></article>
                <a href='login.php'><button class='btn login'>Se connecter</button></a>
            </main>
        </div>
    </body>
</html>
