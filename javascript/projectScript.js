function updateFixturesWithWeek(weekID) {
    var fix = document.getElementById("fixtures_body");
    
    var c = fix.children;
    for (var i = 0; i < c.length; i++) {
        var r = c[i];
        
        var week = parseInt(r.dataset.week);
        r.hidden = (week != weekID);
    }
}

function updateFixturesWithWeek(weekID, radios) {
    var fix = document.getElementById("fixtures_body");
    
    var c = fix.children;
    for (var i = 0; i < c.length; i++) {
        var r = c[i];
        
        var week = parseInt(r.dataset.week);
        r.hidden = (week != weekID);
        
        if(radios) {
            r.children[0].checked = false;
        }
    }
    
    var inputstohide = document.getElementsByClassName("inputstohide");
    for(var i = 0; i < inputstohide.length; i++) {
        inputstohide[i].hidden = true;
    }
    document.getElementById("score_err").hidden = true;
}

function updateFixturesWithTeam(teamID, weekID) {
    if(teamID == -1) updateFixturesWithWeek(weekID);
    else {
        var fix = document.getElementById("fixtures_body");
        
        var c = fix.children;
        for (var i = 0; i < c.length; i++) {
            var r = c[i];
            
            var home = parseInt(r.dataset.home);
            var away = parseInt(r.dataset.away);
            var week = parseInt(r.dataset.week);
            
            r.hidden = ((home != teamID &&
                away != teamID) ||
                week < weekID);
        }
    }
}

function updateView(isWeek, weekID) {
    document.getElementById("fixtures_week_group").hidden = !isWeek;
    document.getElementById("fixtures_team_group").hidden = isWeek; 
    
    if(isWeek) {
        updateFixturesWithWeek(document.getElementById("fixtures_week").value);
    } else {
        updateFixturesWithTeam(document.getElementById("fixtures_team").value, weekID);
    }
}

function enableInputs(home, away, week) {
    var inputstohide = document.getElementsByClassName("inputstohide");
    for(var i = 0; i < inputstohide.length; i++) {
        inputstohide[i].hidden = false;
    }
    
    document.getElementById("homeID").value = home;
    document.getElementById("awayID").value = away;
    document.getElementById("weekID").value = week;
}

function validateScore() {
    var reg = /^\d+$/;
    
    var score1 = document.getElementById("score1").value;
    var score2 = document.getElementById("score2").value;
    
    if(score1 == '' || score2 == '' || !reg.test(score1) || !reg.test(score2)) {
        document.getElementById("score_err").hidden = false;
        return false;
    } 
    document.getElementById("score_err").hidden = true;
    return true;
    
}