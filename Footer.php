<footer>
    <div class="container">
        <div class="content_footer">
            <div class="profil">
                <div class="logo_area">
                    <img src="./assets/img/logo3.png" alt="">
                    <span class="logo_name">PARK'O TOP</span>
                    <button class="dark-mode" id="moon"><i class="fa-solid fa-moon"></i></button>
                    <button class="light-mode" id="sun hide"><i class="fa-solid fa-sun"></i></button>
                </div>

                <div class="desc_area">
                    <p> L'application Park'O Top vous permet de chercher un parking pour stationner votre véhicule, en utilisant des technologies de géolocalisation. Via cette application, vous avez accès au routage et des propositions d'itinéraires partout en France, ainsi qu'une carte consultable pendant votre voyage. </p>
                </div>
            </div>

            <div class="service_area">
                <ul class="service_header">
                    <li class="service_name">A PROPOS</li>
                    <li><a href="#">Données Personnelles</a></li>
                    <li><a href="./legalNotice.php">Mentions Légales</a></li>
                </ul>
                <ul class="service_header">
                    <li class="service_name">AIDE</li>
                    <li><a href="./needHelp.php">Besoin d'aide</a></li>
                    <li><a href="./form.php">Contactez-nous</a></li>
                </ul>
                <ul class="service_header_last">
                    <li class="service_name">NOTRE GROUPE</li>
                    <li><a href="./aboutUs.php">Qui sommes-nous</a></li>
                </ul>
            </div>
        </div>

        <hr>
        
        <div class="footer_bottom">
            <div class="copy_right">
                <i class="fa-solid fa-copyright"></i>
                    <span>2023 PARK'O TOP</span> 
            </div>
        </div>
    </div>
</footer>

<script>
    const darkMode = document.getElementById("moon");
    const lightMode = document.getElementById('sun hide');

    darkMode.addEventListener('click', () => {
        document.body.classList.toggle('dark-mode');
        darkMode.classList.toggle('hide');
        lightMode.classList.remove('hide');
    })

    lightMode.addEventListener('click', () => {
        document.body.classList.toggle('dark-mode');
        lightMode.classList.toggle('hide');
        darkMode.classList.remove('hide');
    })
</script>