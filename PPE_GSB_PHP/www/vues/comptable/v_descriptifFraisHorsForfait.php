<?php
/**
 * Vue Liste des frais hors forfait
 *
 * PHP Version 7
 *
 * @category  PPE
 * @package   GSB
 * @author    Réseau CERTA <contact@reseaucerta.org>
 * @author    José GIL <jgil@ac-nice.fr>
 * @author    Alexy ROUSSEAU <contact@alexy-rousseau.com>
 * @copyright 2017-2019 Réseau CERTA
 * @license   Réseau CERTA
 * @version   GIT: <6>
 * @link      http://www.reseaucerta.org Contexte « Laboratoire GSB »
 */
?>
<hr>
<div class="row">
    <div class="panel panel-info">
        <div class="panel-heading">Descriptif des éléments hors forfait</div>
        <table class="table table-bordered table-responsive">
            <thead>
                <tr>
                    <th class="date">Date</th>
                    <th class="libelle">Libellé</th>
                    <th class="montant">Montant</th>
                    <th class="action">&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($lesFraisHorsForfait as $unFraisHorsForfait) {
                    $libelle = htmlspecialchars($unFraisHorsForfait['libelle']);
                    $date = $unFraisHorsForfait['date'];
                    $montant = $unFraisHorsForfait['montant'];
                    $id = $unFraisHorsForfait['id']; ?>
                    <tr>
                        <td><input type="text" name="txtDateHF" value="<?php echo $date ?>" placeholder="Date"></td>
                        <td><input type="text" name="txtLibelleHF" value="<?php echo $libelle ?>" placeholder="Libellé"></td>
                        <td><input type="text" name="txtMontantHF" value="<?php echo $montant ?>" placeholder="Montant"> </td>
                        <td>
                            <!-- On passe par des liens stylisés sous forme de boutons parce qu'un formulaire n'est pas valide en HTML dans un tableau -->
                            <form method="post" action="index.php?uc=validerFrais&action=validerMajFraisHF">
                                <input type="hidden" name="txtLibelleHF" value="<?php echo $libelle; ?>">
                                <input type="hidden" name="idLigneHF" value="<?php echo $id; ?>">
                                <input type="submit" name="valider" value="Valider" class="btn btn-success">
                                <input type="submit" name="refuser" value="Refuser" class="btn btn-danger">
                            </form>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<div class="row">
    <form method="post" action="#">
        <label for="txtNbHF">Nombre de justificatif(s) :</label>
        <input type="text" name="txtNbHF" size="2" value="<?php echo count($lesFraisHorsForfait); ?>">
        <div id="form-inline">
            <button class="btn btn-success" type="submit">Valider</button>
            <button class="btn btn-danger" type="reset">Refuser</button>
        </div>
    </form>
</div>