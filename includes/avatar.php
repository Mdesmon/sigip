<?php
    if (Controls::isConnected()){
        $userAvatar = Controls::getConnectedUser();
        $link = (Controls::control_user([APPRENANT])) ? "../app/acceuil_user.php" : "../app/acceuil.php";
        ?>
            <div id="avatar" onclick="if(event.target===this)toggleClass('active', this.firstElementChild);" onselectstart="event.preventDefault();">
                <ul class="arrow_box">
                    <li class="bold"><?php echo $userAvatar->name() . " " . $userAvatar->lastName() ?></li>
                    <hr>
                    <a href="<?php echo $link ?>">
                        <li class="item"><div class="icon div-indent apps"></div>Menu principal</li>
                    </a>
                    <a href="../admin/logout.php">
                        <li class="logout"><div class="icon div-indent connexion"></div>Se déconnecter</li>
                    </a>
                </ul>
            </div>
        <?php
    }
    else {
        ?>
            <a href="../login.php">
                <div id="btnLogin" class="btnHelly">Se connecter</div>
            </a>
        <?php
    }
?>