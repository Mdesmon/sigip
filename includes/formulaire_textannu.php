<?php
    // Javascript regex
    $regex_name = "^[A-Za-zÀ-ÖØ-öø-ÿ \-]+$";
    $regex_date = "^[0-9]{2}/[0-9]{2}/[0-9]{4}$";
    $regex_numen = "^(GIPFCIP|GIPCFAA|PEN)+([A-Z0-9]){5,7}$";
?>
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="sn">Nom :</label>
            <input type="text" name="sn" id="sn" class="form-control" required="required" pattern="<?php echo $regex_name; ?>">
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="nompatro">Nom de naissance (si différent) :</label>
            <input type="text" name="nompatro" id="nompatro" class="form-control">
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="givenname">Prénom :</label>
            <input type="text" name="givenname" id="givenname" class="form-control" required="required" pattern="<?php echo $regex_name; ?>" title="">
        </div>
        <div class="form-group">
            <label for="codecivilite">Civilité :</label>
            <select type="text" name="codecivilite" id="codecivilite" class="form-control" required="required" title="">
                <option disabled selected> -- Choisir une option -- </option>
                <option value="M">Monsieur</option>
                <option value="MM">Madame</option>
                <option value="MME">Mademoiselle</option>
            </select>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="datenaissance">Date de naissance :</label>
            <input type="date" name="datenaissance" id="datenaissance" class="form-control" value="" required="required" pattern="<?php echo $regex_date; ?>">
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="numen">NUMEN :</label>
            <input type="text" name="numen" id="numen" class="form-control" required="required" pattern="<?php echo $regex_numen; ?>" title="">
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label class="label-form">RNE :</label>
            <select type="text" name="rne" id="rne" class="form-control" required="required" title="">
                <option disabled selected> -- Choisir une option -- </option>
                <?php
                    foreach ($etablissements as $e) {
                        printf('<option value="%s">%s %s</option>', $e->rne(), $e->name(), $e->rne());
                    }
                ?>
            </select>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label class="label-form">Status :</label>
            <select type="text" name="title" id="title" class="form-control" required="required" title="">
                <option disabled selected> -- Choisir une option -- </option>
                <option value="CTR_GIP">Contractuel GIP</option>
                <option value="PERS_GIP">Personnel GIP</option>
            </select>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="dateff">Fin de fonction (décocher si aucun):</label>
            <div class="input-group">
                <div class="input-group-addon">
                    <input type="checkbox" id="finfonction" name="finfonction" checked>
                </div>
                <input type="date" id="dateff" name="dateff" class="form-control" required="required">
            </div>
        </div>
    </div>
</div>

<button class="pull-right btn btn-primary" type="submit">Valider</button>