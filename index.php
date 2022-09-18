<?php
    session_start();
    $TITLE = "Accueil";
    include("includes/bd.php");
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $nbrentreprise = mysqli_query($db,"Select COUNT(*) From Business;");
    $nbrcustommer = mysqli_query($db,"Select COUNT(*) From Customer;");
    $datacust = mysqli_fetch_assoc($nbrcustommer);
    $dataent = mysqli_fetch_assoc($nbrentreprise);
    include("includes/header.php");
?>
<!-- Container -->
<div class="w3-main">
    <section class="home-container w3-row w3-theme-l1">
        <div class="w3-center w3-col l6 s12 m12">
            <img src="<?="data:png;base64,".base64_encode(file_get_contents("img/siteweb.png"));?>" class="image-home w3-image w3-image" alt>
        </div>
        <div class="w3-col l6 s12 m12 text-home">
            <h2 class="w3-center">Quel est le but de notre entreprise ?</h2>
            <p class="w3-padding-small">Notre site a pour but de permettre à des clients de vendre leurs anciens produits
            pour que des entreprises puissent les réparer et les revendre.
            Cette pratique permet d'augmenter la durée de vie des produits et de limiter l'extraction de matériaux</p>
        </div>
    </section>

    <section class="home-container w3-row" >
        <div class="w3-col l6 s12 m12 text-home">
            <h2 class="w3-center">Quels sont les bénéfices pour les clients ?</h2> 
            <p class="w3-padding-small">Le client pourra vendre le matériel informatique qui ne fonctionne plus et celui qu'il n'utilise plus
                et pourra alors recevoir de l'argent dans sa cagnotte qu'il pourra échanger contre des produits neufs</p>
        </div>
        <div class="w3-center w3-col l6 s12 m12">
            <img src="<?="data:png;base64,".base64_encode(file_get_contents("img/client.png"));?>" class="image-home w3-image" alt>
        </div>
    </section>  

    <section class="home-container w3-row w3-theme-l1 " >
        <div class="w3-center w3-col l6 s12 m12">
            <img src="<?="data:png;base64,".base64_encode(file_get_contents("img/entreprise.png"));?>" class="image-home w3-image" alt>
        </div>
        <div class="w3-col l6 s12 m12 text-home">
            <h2 class="w3-center" >Quels sont les bénéfices pour les entreprises ?</h2>
            <p class="w3-padding-small">Les entreprises pourront obtenir du matériel informatique moins cher et le revendre après l'avoir réparé.
                Ce système permet à l'entreprise de limiter son impact environnemental en évitant d'extraire de nouveaux matériaux. </p>
        </div>  
    </section>           
    <section class="home-container w3-row" >
        <div class="w3-center w3-col l6 s12 m12">
            <h2>Nombre d'entreprises partenaires</h2>
            <p><?php echo $dataent['COUNT(*)'] ?></p>
        </div>
        <div class="w3-center w3-col l6 s12 m12">
            <h2 >Nombre d'utilisateurs inscrits</h2>
            <p ><?php echo $datacust['COUNT(*)'] ?></p> 
        </div> 
    </section>         
</div>
<?php
    include("includes/footer.php");
?>