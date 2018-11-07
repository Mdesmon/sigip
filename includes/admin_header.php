<header>
    <a href="../index.php">
        <img id="logo_organization" src="../api/active_organization_logo.php" />
    </a>

    <nav>
        <div class="underline"></div>
        <a href="parametres_generaux.php">
            <div class="item <?php if (basename($_SERVER['PHP_SELF']) === "parametres_generaux.php") echo "active"; ?>">
                <div class="icon div wheel2"></div>
                <div class="text">Paramètres Généraux</div>
                <div class="underline"></div>
            </div>
        </a>
        <a href="gestion_utilisateurs.php">
            <div class="item <?php if (basename($_SERVER['PHP_SELF']) === "gestion_utilisateurs.php") echo "active"; ?>">
                <div class="icon div userGroup"></div>
                <div class="text">Gestion utilisateurs</div>
                <div class="underline"></div>
            </div>
        </a>
        <a href="logs.php">
            <div class="item <?php if (basename($_SERVER['PHP_SELF']) === "logs.php") echo "active"; ?>">
                <div class="icon div spreadsheet"></div>
                <div class="text">Logs</div>
                <div class="underline"></div>
            </div>
        </a>
        
    </nav>
    <?php include 'avatar.php'; ?>
</header>