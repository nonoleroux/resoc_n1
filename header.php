
<!doctype html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>ReSoC</title>
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
                  <li><a href="deconnexion.php?user_id=<?= $_SESSION['connected_id']?>">Deconnexion</a></li>
              </ul>
            </nav>
        </header>
<?php print_r($_SESSION) ?>