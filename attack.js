// JavaScript Document
// Copie la date passé en argument dans la variable correspondante
function setDateFrom(fromdate) {
    document.getElementById("date_from").value = fromdate;
}
//idem
function setDateTo(todate) {
    document.getElementById("date_to").value = todate;
}

function valid() {
    document.forms.date.submit();
}

function clear_box() {
    document.getElementById('rapport').value = "";
}

function selectionner() {
    document.getElementById('bbcode').select();
}

function selectionner2() {
    document.getElementById('bbcode2').select();
}

function selectionner3() {
    document.getElementById('bbcode3').select();
}