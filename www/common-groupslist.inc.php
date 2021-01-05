<div id="divGroupsList" class="divGroupsList">
<h5>GROUPES</h5>

<div class="div-half-left">  
  <p>Les groupes permettent de regrouper plusieurs repos afin de les trier ou d'effectuer une action commune.</p>

  <table class="table-auto">
  <?php
    $repoGroupsFile = file_get_contents($REPO_GROUPS_FILE); // récupération de tout le contenu du fichier de groupes
    $repoGroups = shell_exec("grep '^\[@.*\]' $REPO_GROUPS_FILE"); // récupération de tous les noms de groupes si il y en a 
    // on va afficher le tableau de groupe seulement si la commande précédente a trouvé des groupes dans le fichier (résultat non vide) :
    if (!empty($repoGroups)) {
      echo "<p><b>Groupes actuels :</b></p>";
      $repoGroups = preg_split('/\s+/', trim($repoGroups)); // on éclate le résultat précédent car tout a été récupéré sur une seule ligne
      foreach($repoGroups as $groupName) {
        $groupName = str_replace(["[", "]"], "", $groupName); // On retire les [ ] autour du nom du groupe
        // on créé un formulaire pour chaque groupe, car chaque groupe sera modifiable :
        echo "<form action=\"\" method=\"post\">";
        echo "<tr>";
        // On veut pouvoir renommer le groupe, ou ajouter des repos à ce groupe, donc il faut transmettre le nom de groupe actuel (actualGroupName) :
        echo "<input type=\"hidden\" name=\"actualGroupName\" value=\"${groupName}\" />";
        // clien cliquable "corbeille" pour supprimer le groupe :
        echo "<td class=\"td-auto\"><a href=\"?action=deleteGroup&groupName=${groupName}\"><img src=\"images/trash.png\" /></a></td>";
        // on affiche le nom actuel du groupe dans un input type=text qui permet de renseigner un nouveau nom si on le souhaite (newGroupeName) :
        echo "<td colspan=\"100%\"><input type=\"text\" value=\"${groupName}\" name=\"newGroupName\" class=\"invisible_input\" /></td>";
        echo "</tr>";

        // On va récupérer la liste des repos du groupe et les afficher si il y en a (résultat non vide)
        $repoGroupList = shell_exec("sed -n '/\[${groupName}\]/,/\[/p' $REPO_GROUPS_FILE | sed '/^$/d' | grep -v '^\['"); // récupération des repos de ce groupe, en supprimant les lignes vides

        if (!empty($repoGroupList)) {
            $repoGroupList = preg_split('/\s+/', trim($repoGroupList)); // on éclate le résultat précédent car tout a été récupéré sur une seule ligne
            echo "<tr>";
            echo "<td></td>";
            echo "<td>⤷</td>";
            echo "<td class=\"td-auto\"><b>Repo</b></td>";
            if ($OS_TYPE == "deb") { echo "<td class=\"td-auto\"><b>Distribution</b></td>"; }
            if ($OS_TYPE == "deb") { echo "<td class=\"td-auto\"><b>Section</b></td>"; }
            echo "</tr>";

            foreach($repoGroupList as $repoName) {
                $rowData = explode(',', $repoName);
                $repoName = str_replace(['Name=', '"'], "", $rowData[0]); // on récupère la données et on formate à la volée en retirant Name=""
                if ($OS_TYPE == "deb") { // si Debian on récupère aussi la distrib et la section
                  $repoDist = str_replace(['Dist=', '"'], "", $rowData[2]); // on récupère la données et on formate à la volée en retirant Dist=""
                  $repoSection = str_replace(['Section=', '"'], "", $rowData[3]); // on récupère la données et on formate à la volée en retirant Section=""
                }
                echo "<tr>";
                echo "<td></td>";
                if ($OS_TYPE == "rpm") { echo "<td class=\"td-auto\"><a href=\"?action=deleteGroupRepo&groupName=${groupName}&repoName=${repoName} \"><img src=\"images/trash.png\" /></a></td>"; }
                if ($OS_TYPE == "deb") { echo "<td class=\"td-auto\"><a href=\"?action=deleteGroupRepo&groupName=${groupName}&repoName=${repoName}&repoDist=${repoDist}&repoSection=${repoSection}\"><img src=\"images/trash.png\" /></a></td>"; }
                echo "<td class=\"td-auto\">${repoName}</td>";
                if ($OS_TYPE == "deb") {
                  echo "<td class=\"td-auto\">${repoDist}</td>";
                  echo "<td class=\"td-auto\">${repoSection}</td>";
                }
                echo "</tr>";
            }
        }
        echo "<tr>";
        echo "<td></td>";
        echo "<td></td>";
        // entrées permettant d'ajouter un repo au groupe. Pour rappel le nom du groupe est transmis en hidden (voir début du formulaire) :
        echo "<td class=\"td-auto\"><input type=\"text\" name=\"groupAddRepoName\" autocomplete=\"off\" placeholder=\"Nom du repo\" \></td>";
        if ($OS_TYPE == "deb") { echo "<td class=\"td-auto\"><input type=\"text\" name=\"groupAddRepoDist\" autocomplete=\"off\" placeholder=\"Distribution\" \></td>"; }
        if ($OS_TYPE == "deb") { echo "<td class=\"td-auto\"><input type=\"text\" name=\"groupAddRepoSection\" autocomplete=\"off\" placeholder=\"Section\" \></td>"; }
        echo "<td><button type=\"submit\" class=\"button-submit-xsmall-blue\">Ajouter</button></td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td colspan=\"100%\"><button type=\"submit\" class=\"button-submit-large-green\">Enregistrer</button></td>";
        echo "</tr>";
        echo "</form>";
        // ligne séparatrice entre chaque groupe :
        echo "<tr><td colspan=\"100%\"><hr></td></tr>";
      }
    }?>
  </table>
  </div>

  <div class="div-half-right">
  <p>Ajouter un nouveau groupe :</p>
  <form action="" method="post">
    <input type="text" class="input-medium" name="addGroupName" autocomplete="off"></td>
    <button type="submit" class="button-submit-xsmall-blue">Ajouter</button></td>
  </form>
  
  </div>
</div>

<script> 
// Afficher ou masquer la div permettant de gérer les groupes (div s'affichant en bas de la page)
$(document).ready(function(){
  $("a#GroupsListToggle").click(function(){
    $("div#divGroupsList").slideToggle(150);
    $(this).toggleClass("open");
  });
});







// à supprimer : 
// Afficher des inputs supplémentaires si quelque chose est tapé au clavier dans le input 'Repo'
// Bind keyup event on the input
$('#input-repo').keyup(function() {
  
  // If value is not empty
  if ($(this).val().length == 0) {
    // Hide the element
    $('.td-hide').hide();
  } else {
    // Otherwise show it
    $('.td-hide').show();
  }
}).keyup(); // Trigger the keyup event, thus running the handler on page load
</script>