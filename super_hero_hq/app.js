class SuperHero {
    constructor(id, h_name, r_name, publisher, fad, powers, teams) {
        this.id = id;
        this.hero_name = h_name;
        this.real_name = r_name;
        this.publisher = publisher;
        this.fad = fad;
        this.abilities = powers;
        this.teams = teams;
    }

    validateHero() {
        if (this.hero_name === "" || this.real_name === "" || this.publisher === "" || this.fad === "") {
            return false;
        }

        return true;
    }
}

class Conn {

    get(url, json_object, callback) {
        var httpRequest = new XMLHttpRequest();
        httpRequest.onreadystatechange = function () {
            if (httpRequest.readyState == 4 && httpRequest.status == 200) {
                callback(httpRequest.responseText);
            }
        }
        httpRequest.open("GET", url, true);
        httpRequest.send(json_object);
    }

    post(url, json_object, callback) {
        var httpRequest = new XMLHttpRequest();
        httpRequest.onreadystatechange = function () {
            if (httpRequest.readyState == 4 && httpRequest.status == 200) {
                callback(httpRequest.responseText);
            }
        }
        httpRequest.open("POST", url, true);
        httpRequest.send(json_object);
    }

    put(url, json_object, callback) {
        var httpRequest = new XMLHttpRequest();
        httpRequest.onreadystatechange = function () {
            if (httpRequest.readyState == 4 && httpRequest.status == 200) {
                callback(httpRequest.responseText);
            }
        }
        httpRequest.open("PUT", url, true);
        httpRequest.send(json_object);
    }
}

let conn = new Conn();
let heroesData;
let super_hero;
let powers = Array();
let teams = Array();


function getAllHeroes() {
    var url = 'http://localhost/php_rest_superherohq/api/getHeroes.php';
    conn.get(url, null, function (response) {
        var response1 = JSON.parse(response);
        heroesData = convertData(response1['data']);
        console.log(heroesData);
        populateTable();
    });
}

function getHeroesByName() {
    var h_name = document.getElementById('key_word').value;
    if (h_name !== '') {
        var url = `http://localhost/php_rest_superherohq/api/searchHero.php?hero=${h_name}`;
        conn.get(url, null, function (response) {
            var response1 = JSON.parse(response);
            heroesData = convertData(response1['data']);
            console.log(heroesData);
            populateTable();
        });
    }
    else {
        getAllHeroes();
    }
}

function convertData(heroes = Array()) {
    let superHeroes = Array();
    heroes.forEach(function (h) {
        var hero = new SuperHero(h['id'], h['hero_name']);
        superHeroes.push(hero);
    });
    return superHeroes;
}

function populateTable() {

    let heroesList = document.getElementById("heroesList");
    heroesList.innerHTML = '';

    heroesData.forEach(function (h) {
        let row = heroesList.insertRow();

        row.insertCell(0).innerHTML = h.hero_name;

        var a = document.createElement('a')
        var link = document.createTextNode('Details')
        var a2 = document.createElement('a')
        var link2 = document.createTextNode('Edit')

        a.className = "link"
        a.appendChild(link)
        a.title = "Details"
        a.href = `heroDetails.html?${h.id}`

        a2.className = "link"
        a2.appendChild(link2)
        a2.title = "Edit"
        a2.href = `updateHero.html?${h.id}`



        row.insertCell(1).append(a)
        row.insertCell(2).append(a2)

    });
}

function addToPowers() {
    let power = document.getElementById("power");
    if(power.value != "")
        powers.push(power.value);
    let list = document.getElementById("powers_list");
    list.innerHTML = "";
    power.value = "";
    powers.forEach(function (p) {
        var li = document.createElement("li");
        li.appendChild(document.createTextNode(p));
        list.appendChild(li);
    });
}

function addToTeams() {
    let team = document.getElementById("team");
    if(team.value != "")
        teams.push(team.value);
    let list = document.getElementById("teams_list");
    list.innerHTML = "";
    team.value = '';
    teams.forEach(function (p) {
        var li = document.createElement("li");
        li.appendChild(document.createTextNode(p));
        list.appendChild(li);
    });
}

function createHero() {
    let h_name = document.getElementById("h_name").value;
    let r_name = document.getElementById("r_name").value;
    let publisher = document.getElementById("publisher").value;
    let fad = document.getElementById("fad").value;

    super_hero = new SuperHero(null, h_name, r_name, publisher, fad, powers, teams);

    if (super_hero.validateHero())
        conn.post("http://localhost/php_rest_superherohq/api/createHero.php", JSON.stringify(super_hero), function (response) {
            /*
            var aux = response.split(">");
            //alert(aux[aux.length - 1]);
            var msg = JSON.parse(aux[aux.length-1][0]['message']);
            
            alert(msg);
            */
            alert("Hero Updated");
            
        });
    else
        alert("Please fill the form")
}


function getHeroDetails() {
    var id = window.location.search.replace("?", "");
    var hero = new SuperHero(id, null, null, null, null, null, null);
    conn.get(`http://localhost/php_rest_superherohq/api/getHeroDetails.php?id=${id}`, JSON.stringify(hero), function (response) {
        var response1 = JSON.parse(response);
        heroesData = response1['data'][0];
        super_hero = new SuperHero(heroesData["id"], heroesData["hero_name"], heroesData["real_name"], heroesData["publisher"], heroesData["fad"], heroesData["abilities"], heroesData["affiliations"])
        console.log(super_hero);

        loadData()
    });
}

function loadData() {
    document.getElementById("h_name").innerHTML = super_hero.hero_name
    document.getElementById("r_name").innerHTML = super_hero.real_name
    document.getElementById("publisher").innerHTML = super_hero.publisher
    var data = String(super_hero.fad).split("-");
    document.getElementById("fad").innerHTML = data[2] + "/" + data[1] + "/" + data[0]
    if (super_hero.abilities.length != 0) {
        let list = document.getElementById("powers_list");
        list.innerHTML = "";
        super_hero.abilities.forEach(function (p) {
            var li = document.createElement("li");
            li.appendChild(document.createTextNode(p));
            list.appendChild(li);
        });
    }

    if (super_hero.teams.length != 0) {
        let list = document.getElementById("teams_list");
        list.innerHTML = "";
        super_hero.teams.forEach(function (p) {
            var li = document.createElement("li");
            li.appendChild(document.createTextNode(p));
            list.appendChild(li);
        });
    }

}

function getHeroData() {
    var id = window.location.search.replace("?", "");
    var hero = new SuperHero(id, null, null, null, null, null, null);
    conn.get(`http://localhost/php_rest_superherohq/api/getHeroDetails.php?id=${id}`, JSON.stringify(hero), function (response) {
        var response1 = JSON.parse(response);
        heroesData = response1['data'][0];
        super_hero = new SuperHero(heroesData["id"], heroesData["hero_name"], heroesData["real_name"], heroesData["publisher"], heroesData["fad"], heroesData["abilities"], heroesData["affiliations"])
        console.log(super_hero);

        loadForm()
    });
}

function loadForm() {
    document.getElementById("h_name").value = super_hero.hero_name
    document.getElementById("r_name").value = super_hero.real_name
    document.getElementById("publisher").value = super_hero.publisher
    document.getElementById("fad").value = super_hero.fad
    powers = super_hero.abilities
    teams = super_hero.teams
    addToPowers()
    addToTeams()
}

function updateHero() {
    let h_name = document.getElementById("h_name").value;
    let r_name = document.getElementById("r_name").value;
    let publisher = document.getElementById("publisher").value;
    let fad = document.getElementById("fad").value;
    let id = super_hero.id

    super_hero = new SuperHero(id, h_name, r_name, publisher, fad, powers, teams);

    if (super_hero.validateHero()) {
        console.log(super_hero)
        conn.put("http://localhost/php_rest_superherohq/api/updateHero.php", JSON.stringify(super_hero), function (response) {
            alert("Hero Updated");
        });
    }
    else
        alert("Please fill de Form.")
}

function resetPowers() {
    powers = new Array()
    let list = document.getElementById("powers_list");
    list.innerHTML = "";
}

function resetTeams() {
    teams = new Array()
    let list = document.getElementById("teams_list");
    list.innerHTML = "";
}

