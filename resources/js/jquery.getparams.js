//TODO load parameters into array on page load to avoid running through regex
//TODO make parameters writable, thus reloading the page with different parameters
//TODO make unnammed parameters readable and writeable too
$.getparam = function(name){
    var results = new RegExp('[\\?&]' + name + '=([^&#]*)').exec(window.location.href);
    return (results !== null ? decodeURIComponent(results[1]) : null);
}
