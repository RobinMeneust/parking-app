const darkModeButton = document.getElementById("moon");
const lightModeButton = document.getElementById("sun");

darkModeButton.addEventListener('click', () => { switchThemeEvent(true); });
lightModeButton.addEventListener('click', () => { switchThemeEvent(false); });

if(window.addEventListener){
    window.addEventListener('load',refreshTheme,false);
}else{
    window.attachEvent('onload',refreshTheme);
}

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
        
        if(typeof switchUserGraphsTheme === "function")
            switchUserGraphsTheme(newTheme);
        if(typeof switchPredictGraphTheme === "function")
            switchPredictGraphTheme(newTheme);
    }
}