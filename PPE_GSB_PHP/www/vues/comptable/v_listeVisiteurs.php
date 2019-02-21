<?php
/**
 * Vue Liste des visiteurs & mois disponibles
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
 * @version   GIT: <11>
 * @link      http://www.reseaucerta.org Contexte « Laboratoire GSB »
 */
?>
<div class="row">
    <form action="index.php?uc=validerFrais&action=validerSaisieFraisVisiteur"
          method="post" role="form" id="formChoixVisiteur">
        <div class="form-inline">
            <div class="row">
                <label for="lstVisiteurs" accesskey="n">Choisir le visiteur : </label>
                <div class="form-group">
                    <select id="lstVisiteurs" name="lstVisiteurs" class="form-control">
                        <?php
                        foreach ($lesVisiteurs as $leVisiteur) {
                            $id = $leVisiteur['id'];
                            $nom = $leVisiteur['nom'];
                            $prenom = $leVisiteur['prenom'];

                            if($id == $idVisiteur) {
                                ?>
                                <option selected="selected" value="<?php echo $id ?>">
                                    <?php echo strtoupper($nom) . ' ' . $prenom ?> </option>
                                <?php
                            }
                            else {
                                ?>
                                <option value="<?php echo $id ?>">
                                <?php echo strtoupper($nom) . ' ' . $prenom ?> </option>
                                <?php
                            }
                        } ?>

                    </select>
                </div>

                <label for="lstMois" accesskey="n">Mois : </label>
                <div class="form-group">
                    <select id="lstMois" name="lstMois" class="form-control">
                        <?php
                        foreach ($lesMoisDisponibles as $unMois) {
                            $mois = $unMois['mois'];
                            $numAnnee = $unMois['numAnnee'];
                            $numMois = $unMois['numMois'];
                            if ($mois == $moisChoisi) {
                                ?>
                                <option selected="selected" value="<?php echo $mois ?>">
                                    <?php echo $numMois . '/' . $numAnnee ?> </option>
                                <?php
                            } else {
                                ?>
                                <option value="<?php echo $mois ?>">
                                    <?php echo $numMois . '/' . $numAnnee ?> </option>
                                <?php
                            }
                        }
                        ?>
                    </select>
                </div>
        </div>
    </form>
</div>