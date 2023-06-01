<?php if(session_status() != PHP_SESSION_ACTIVE) {session_start();} ?>
<footer>
    <div class="container">
        <div class="content_footer">
            <div class="profil">
                <div class="logo_area">
                    <img src="./assets/img/logo3.png" alt="">
                    <span class="logo_name">PARK'O TOP</span>
                    <button class="dark-mode" id="moon"><i class="fa-solid fa-moon"></i></button>
                    <button class="light-mode hide" id="sun"><i class="fa-solid fa-sun"></i></button>
                </div>

                <div class="desc_area">
                    <p> L'application Park'O Top vous permet de chercher un parking pour stationner votre véhicule, en utilisant des technologies de géolocalisation. Via cette application, vous avez accès au routage et des propositions d'itinéraires partout en France, ainsi qu'une carte consultable pendant votre voyage. </p>
                </div>
            </div>

            <div class="service_area">
                <ul class="service_header">
                    <li class="service_name">A PROPOS</li>
                    <li><a href="./personalData.php">Données Personnelles</a></li>
                </ul>
                <ul class="service_header">
                    <li class="service_name">AIDE</li>
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
    const darkModeButton = document.getElementById("moon");
    const lightModeButton = document.getElementById("sun");
    const graphs = document.getElementsByClassName("graph");

    darkModeButton.addEventListener('click', () => { switchThemeEvent(true); });
    lightModeButton.addEventListener('click', () => { switchThemeEvent(false); });

    window.onload = refreshTheme;

    async function getCurrentTheme() {
        let url = "./PHP/theme.php?action=get";
        
        return fetch(url).then(function(response) {
            if(response.status >= 200 && response.status < 300) {
                return response.text();
            }
            throw new Error(response.statusText);
        }).catch((err)=>{
            console.error(err);
        });
    }

    /*
        Check what is the current theme and set it to the page
    */
    async function refreshTheme() {
        console.log("refresh");
        let currentTheme = await getCurrentTheme();
        if(currentTheme == "dark") {
            switchThemeEvent(true);
        } else {
            switchThemeEvent(false);
        }
    }

    /*
        Get the theme or toggle dark mode
    */
    async function switchTheme(){
        let url = "./PHP/theme.php?action=switch";
        
        return fetch(url).then(function(response) {
            if(response.status >= 200 && response.status < 300) {
                return response.text();
            }
            throw new Error(response.statusText);
        }).catch((err)=>{
            console.error(err);
        });
    }

    /*
        Event use to switch the theme mode
    */

    async function switchThemeEvent(darkModeOn) {
        if((!document.body.classList.contains('dark-mode') && darkModeOn) || document.body.classList.contains('dark-mode') && !darkModeOn) {
            let currentTheme = await getCurrentTheme();
            let newTheme = (darkModeOn ? "dark" : "light");
            if(currentTheme != newTheme)
                await switchTheme();
            document.body.classList.toggle('dark-mode');
            darkModeButton.classList.toggle('hide');
            lightModeButton.classList.toggle('hide');
            
            if(graphs != null) {
                for(let i=0; i<graphs.length; i++) {
                    //graph[i];
                    //TODO
                }
            }
        }
    }
</script>