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
                <article>Vous avez bien été déconnecté</br><?php echo $_SESSION['connected_id'] ?></article>
            </main>
        </div>
    </body>
</html>
