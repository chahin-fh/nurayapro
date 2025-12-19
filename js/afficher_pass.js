function affiche_pass(){
    var mp1=document.getElementById("password");
    var mp2=document.getElementById("password2");
    var checked = document.getElementById("show-password").checked;
    if (checked) {
        mp1.type="text";
        mp2.type="text";
    }else{
        mp1.type="password";
        mp2.type="password";
    }
}