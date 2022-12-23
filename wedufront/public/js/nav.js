// sticky menu
$(window).on("scroll", function () {
  var scroll = $(window).scrollTop();
  if (scroll > 0) {
    $(".top-header").addClass("header-sticky");
    //use 'header-sticky' class on your main header
  } else {
    $(".top-header").removeClass("header-sticky");
  }
});

// toggle bar animation
const toggle_bar = document.querySelector(".toggle-bar");
let menuOpen = false;

toggle_bar.addEventListener("click", () => {
  if (!menuOpen) {
    toggle_bar.classList.add("open");
    menuOpen = true;
  } else {
    toggle_bar.classList.remove("open");
    menuOpen = false;
  }
});

//on click & on scroll navbar hide
let menu = document.querySelector(".toggle-bar");
let main_navbar = document.querySelector(".main-navbar");

//this code will show the disappear navbar. when there will be a 'active' class it will visible/block/clip path by css code
menu.addEventListener("click", () => {
  main_navbar.classList.toggle("active");
});

window.onscroll = () => {
  toggle_bar.classList.remove("open"); //toggle_bar is a var that i created above (when worked on toggle bar animation)
  menuOpen = false;
  main_navbar.classList.remove("active");
};
