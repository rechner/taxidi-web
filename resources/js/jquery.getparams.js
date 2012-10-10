//TODO load parameters into array on page load to avoid running through regex
//TODO make parameters writable, thus reloading the page with different parameters
//TODO make unnammed parameters readable and writeable too
$.getparam=function(a){
  a=RegExp("[\\?&]"+a+"=([^&#]*)").exec(window.location.href);
  return null!==a?decodeURIComponent(a[1]):null
};
