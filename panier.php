<?php require_once "inc/data_base.php";

                if(isset($_SESSION['user']) && $_SESSION['user']['id_user'] <=5){
                  
                    require "composants/nav_amin.php"; 
                }
                  elseif(isset($_SESSION['user']) && $_SESSION['user']['id_user'] >5){
                    require "composants/nav_connect_user.php"; 
                  }else
                  {
                    require "composants/nav.php"; 
                    header('location: home.php');
                    die();


                }


        $requete = 'SELECT * FROM commande WHERE id_membre = :id_membre ORDER BY id_commande DESC';
        $r = $data_base->prepare($requete);
        $r->bindValue(':id_membre', $_SESSION['user']['id_user'], PDO::PARAM_INT);
        $r->execute();
        $panier = $r->fetchAll(PDO::FETCH_ASSOC);

    //   var_dump($panier);
    //   die();
//-------------------------------------------------------------------------------
            $montant_total = 0;
            
?>
            <div class="title_gestion_produit">
                <h1>Mon panier <br><span>Liste de produits dans votre paniner</span></h1>
            </div>
  
        
            
        <form action="" class="gestion_produit_table" method="POST">
       
         
           
     

          <?php 
         if (empty($panier)) {
            echo '<div class="panier_vide">Votre panier est vide !</div>';
            die();
        } else {
            echo '<table class="gestion_produit_table">';
            echo '<thead class="thead_gestion">';
            echo '<tr>';
            echo '<th>N°cmnd</th>';
            echo '<th>Photo</th>';
            echo '<th>Nom</th>';
            echo '<th>Nbr</th>';
            echo '<th>Prix</th>';
            echo '<th>Statut</th>';
            echo '<th>Actions</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
        
            foreach($panier as $panier)
          { 
                $quantite = intval($panier['quantity']); 
                $montant_commande = $panier['prix_produit_p'];
                $montant_total += $montant_commande * $quantite;
            ?>
            <tr>
                <td> <?= $panier['id_commande'] ?></td>
                <td class="img_gestion_produit"> 
                        <img src="<?= $panier['img_produit']?> " alt="" width="100%" height="100%">
                </td>
                <td><?=$panier['nom_produit']?></td>
                
                <td>          
                <input
                type="number" class="select_quantite_panier" value="<?= $panier['quantity'] ?>"
         data-commande-id="<?= $panier['id_commande'] ?>" data-montant="<?= $montant_commande ?>" name="quantite_pdt" min="1" max="2">
                    <input type="hidden" name="commande_id" value="<?= $panier['id_commande'] ?>">
                </td>

                
                <td><?= $panier['montant_commande']?></td>
               
                <td><?= $panier['etat_commande']?></td>
                <td>
                    <a href="detail_coupe_adulte.php?id_coupe=" class="see_produit"><i class="fa-regular fa-eye"></i></a>
                    <a href="delete_produit-panier.php?id_commande=<?= $panier['id_commande'] ?>" class="delete_produit"><i class="fa-solid fa-trash"></i></a>
</td>
<!--                     
                      <hr class="hr_commande"> -->
                </td>
              
            </tr>
        <?php }
        }
        
           ?> 
        </tbody>

        </table>
        <div class="master_bouton">
            <div class="tous_btn">
            <div class="total">
                <div class="texte">
                    <p>Total</p></div>
                <div class="prixTotal">
                    <p><?=  $montant_total?> EUR</p>
                </div>
            </div>
            <button class="commandez_tous">Commander</button>
        </div>
        </div>
        </form>

        <script>
    const inputsQuantite = document.querySelectorAll('.select_quantite_panier');
    const montantTotalElement = document.querySelector('.prixTotal p');
    let montantTotal = <?= $montant_total ?>; 

    inputsQuantite.forEach(input => {
        const montantCommandeInitial = parseFloat(input.dataset.montant);
        const quantiteInitiale = parseFloat(input.value); 

        input.dataset.quantite = quantiteInitiale;

        input.addEventListener('change', function() {
            const nouvelleQuantite = parseFloat(this.value);
            const montantCommande = montantCommandeInitial; 

            const montantCommandePrecedent = montantCommande * parseFloat(this.dataset.quantite);

            // Calculer le nouveau montant pour cette commande
            const nouveauMontantCommande = montantCommande * nouvelleQuantite;

          
            montantTotal = montantTotal - montantCommandePrecedent + nouveauMontantCommande;
            montantTotalElement.textContent = montantTotal.toFixed(2) + ' EUR';

            this.dataset.montant = nouveauMontantCommande;
            this.dataset.quantite = nouvelleQuantite;

      
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'traitement_mise_a_jour_quantite.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
        
                }
            };
            const data = `commande_id=${this.dataset.commandeId}&nouvelle_quantite=${nouvelleQuantite}`;
            xhr.send(data);
        });
    });
</script>
<script src="./js/nav.js"></script>
