"use strict";

/*see helfull examle: 
 * 
 * https://media.prod.mdn.mozit.cloud/samples/domref/file-click-demo.html
 * 
 * 
 * */

window.addEventListener("load", function (event) {
    var images = document.getElementsByTagName("img");

    if (images) {
        console.log(images); // OK
        for (var i of images) {
            i.addEventListener("click",  function(event){
                console.log(i);
                ff();
            } )
    }
}
}
);

function ff() {
//    var f = document.getElementById("inImage");
//    var images = document.getElementsByTagName("img");
    
//    images[0].src = window.URL.createObjectURL(f.files[0]);
//    console.log(f);
    
    
    var input = document.getElementById("inImage");
var fReader = new FileReader();
fReader.readAsDataURL(input.files[0]);
fReader.onloadend = function(event){
    var img = document.getElementsByTagName("img");//getElementById("yourImgTag");
    img[0].src = event.target.result;
}
}
