<footer>
    <div class="container">
        <div class="content_footer">
            <div class="profil">
                <div class="logo_area">
                    <img src="./assets/img/logo.png" alt="">
                    <span class="logo_name">PARK'O TOP</span>
                    <button class="dark-mode" id="moon"><i class="fa-solid fa-moon"></i></button>
                    <button class="light-mode" id="sun hide"><i class="fa-solid fa-sun"></i></button>
                </div>
                <div class="desc_area">
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Illo laborum ipsa natus, qui aliquid magnam aut reprehenderit, dolorem quos, debitis nesciunt est placeat asperiores et impedit aliquam consectetur maiores quaerat.</p>
                </div>
                <div class="social_media">
                    <a href="#"><i class="fa-brands fa-linkedin"></i></a>
                    <a href="#"><i class="fa-brands fa-facebook"></i></a>
                    <a href="#"><i class="fa-brands fa-square-instagram"></i></a>
                    <a href="#"><i class="fa-brands fa-square-twitter"></i></a>
                </div>
            </div>
            <div class="service_area">
                <ul class="service_header">
                    <li class="service_name">A PROPOS</li>
                    <li><a href="#">Données Personnelles</a></li>
                    <li><a href="#">Mentions Légales</a></li>
                </ul>
                <ul class="service_header">
                    <li class="service_name">AIDE</li>
                    <li><a href="#">Besoin d'aide</a></li>
                    <li><a href="#">Contactez-nous</a></li>
                </ul>
                <ul class="service_header_last">
                    <li class="service_name">NOTRE GROUPE</li>
                    <li><a href="#">Qui sommes-nous</a></li>
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