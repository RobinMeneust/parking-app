let months=["janvier","février","mars","avril","mai","juin","juillet","août","septembre","octobre","novembre","décembre"];
let year = "2023";
let button = null;
const regex = new RegExp('^([0-9]{4})$'); // used to check if the given date (year) is correct

let visitsGraph = null;
let expensesGraph = null;
/*
    Initialize the page userGraphs.php
*/

function initialize() {
    button = document.getElementById('refreshDate');
    button.addEventListener("click",function(){refreshDate();});

    sendQueryGraph('./PHP/queryMsqliGet.php?d=expenses&y='+year).then((result) =>{
        let expensesValues = result;
        expensesGraph = createExpensesGraph(expensesValues);
    });
    
    sendQueryGraph('./PHP/queryMsqliGet.php?d=visits&y='+year).then((result) =>{
        let visitsValues = result;
        visitsGraph = createVisitsGraph(visitsValues);
    });
}

/*
    Create a graph with Chart.js
*/

function createGraph(idCanvas, type, yValues, borderColor, bgColor, doFill, title){
    return new Chart(idCanvas, {
        type: type,
        data:{
            labels:months,
            datasets: [{
                data: yValues,
                borderColor: borderColor,
                backgroundColor: bgColor,
                fill: doFill
            }]
        },
        options:{
            responsive: true,
            legend: {display: false},
            title: {
                display: true,
                text: title
            },
            scales: {
                yAxes: [{
                    display: true,
                    ticks: {
                        beginAtZero: true
                    }
                }]
            }
        }
    });
}

/*
    Create a graph giving the total number of visits per month for the given year
*/

function createVisitsGraph(visitsValues){
    return createGraph("visits", "bar", visitsValues, "black", "blue", false, "Nombre de stationnements par mois");
}

/*
    Create a graph giving the total number of expenses per month for the given year
*/

function createExpensesGraph(expensesValues){
    return createGraph("expenses", "line", expensesValues, "blue", "black", false, "Dépenses par mois");
}

/*
    Send data to the given url, fetch the result and return it in an array
*/

async function sendQueryGraph(url){
    let result = [0,0,0,0,0,0,0,0,0,0,0,0];
    // example : [{"d":"4","n":"12.00"}]
    return fetch(url).then(function(response) {
        if(response.status >= 200 && response.status < 300) {
            return response.json();
        }
        throw new Error(response.statusText);
    })
    .then(function(response) {
        if(response == "0"){
            alert("Vous n'êtes pas connecté");
        } else{
            for(let i=0; i<response.length; i++){
                result[parseInt(response[i]["d"])] = parseInt(response[i]["n"]);
            }
            return result;
        }
    });
}

/*
    Change the date of the data displayed in the 2 graphs
*/

function refreshDate(){
    let tempYear = document.getElementById('yearGraph').value;
    let tempYearInt = parseInt(tempYear)
    if(regex.test(tempYear) && tempYearInt >= 2000){
        year = tempYear;

        sendQueryGraph('./PHP/queryMsqliGet.php?d=expenses&y='+year).then((result) =>{
            let expensesValues = result;
            expensesGraph = createExpensesGraph(expensesValues);
        });
        
        sendQueryGraph('./PHP/queryMsqliGet.php?d=visits&y='+year).then((result) =>{
            let visitsValues = result;
            visitsGraph = createVisitsGraph(visitsValues);
        });
    } else{
        document.getElementById('yearGraph').value = "2023";
    }
}

