import React, { useState, useEffect } from "react";
import Link from "next/link";
import PopupForm from "../Forms/PopupForm";
import { Dropdown } from "react-bootstrap";
import API from "../../ReactCommon/utility/api";
import { toast } from "react-toastify";
import { useRouter } from 'next/router';
import detect from "../../ReactCommon/utility/detect";
// import beforeLogo from "../../public/loader.gif";
import Image from "next/image";
import beforeLogo from "../../public/loader.gif";
import FacebookLogin from './../../ReactCommon/Components/facebook'
let main_navbar;
let menuOpen = false;
const NavigationBar = (props) => {
  const router = useRouter();
  const [headerClass, setHeaderClass] = useState("");
  const [checkLoginToken, setLoginToken] = useState("");
  const [activeMenu, setActiveMenu] = useState("");
  const [activeSubMenu, setActiveSubMenu] = useState("");
  // const togglePopup = () => {
  //   console.log("yes");
  //   setIsOpen(!isOpen);
  // };
  function posY(elm) {
    var test = elm, top = 0;
    while (!!test && test.tagName.toLowerCase() !== "body") {
      top += test.offsetTop;
      test = test.offsetParent;
    }
    return top;
  }

  function viewPortHeight() {
    var de = document.documentElement;
    if (!!window.innerWidth) { return window.innerHeight; }
    else if (de && !isNaN(de.clientHeight)) { return de.clientHeight; }
    return 0;
  }

  function scrollY() {
    if (window.pageYOffset) { return window.pageYOffset; }
    return Math.max(document.documentElement.scrollTop, document.body.scrollTop);
  }

  function checkvisible(elm) {
    var vpH = viewPortHeight(), // Viewport Height
      st = scrollY(), // Scroll Top
      y = posY(elm);
    return (y > (vpH + st));
  }
  function listenToScroll(defaultH = 0) {
    const winScroll =
      document.body.scrollTop || document.documentElement.scrollTop
    const height =
      document.documentElement.scrollHeight -
      document.documentElement.clientHeight + defaultH
    const scrolled = winScroll / height * 100;
    return scrolled;
  }

  const listenScrollEvent = () => {
    if (window.scrollY > 0) {
      setHeaderClass("header-sticky");
    } else {
      setHeaderClass("");
    }
    if(detect.isMobile()){
      // console.log("is mobile",detect.isMobile());
      return;
    }
    // detect.isMobile
    if (document.getElementById("sidebar")) {
      let sidebarOffset = document.querySelector('#sidebar').offsetTop;
      let sliderOffset = document.getElementById("sliderOff").clientHeight;
      let propSection = document.getElementById("propSection").clientHeight;
      let offsetFooter = document.querySelector('#footers').clientHeight;
      let visible = checkvisible(document.getElementById("footers"));
      sidebarOffset = sidebarOffset + sliderOffset;
      offsetFooter = propSection - document.documentElement.clientHeight;
      let heights = document.documentElement.clientHeight
      heights = offsetFooter = document.documentElement.clientHeight - window.scrollY + offsetFooter;
      heights = offsetFooter = document.documentElement.clientHeight - window.scrollY + offsetFooter;
      let scrolled = listenToScroll(document.documentElement.clientHeight);
      if (sliderOffset >= window.scrollY) {
        document.getElementById("sidebar").classList.remove("sidebar-sticky");
        document.getElementById('sidebar').classList.remove("sidebar-fixed-v2");
      }
      if (window.scrollY > sliderOffset) {
        document.getElementById("sidebar").classList.add("sidebar-sticky");
      }
      if (!visible) {
        if (scrolled > 62) {
          document.getElementById("sidebar").classList.remove("sidebar-sticky");
          document.getElementById('sidebar').classList.add("sidebar-fixed-v2")
        }
      }
      if (visible) {
        document.getElementById('sidebar').classList.remove("sidebar-fixed-v2")
      }
      if (sidebarOffset <= window.scrollY) {
        document.getElementById("sidebar").classList.add("sidebar-sticky");
      }
      else {
        if (offsetFooter >= window.scrollY) {
          // document.getElementById("sidebar").classList.remove("sidebar-sticky");
        }
      }
    }
  };
  const toggleBar = () => {
    // toggle bar animation
    const toggle_bar = document.querySelector(".toggle-bar");
    try {
      if (toggle_bar) {
        toggle_bar.addEventListener("click", () => {
          if (!menuOpen) {
            toggle_bar.classList.add("open");
            menuOpen = true;
          } else {
            toggle_bar.classList.remove("open");
            menuOpen = false;
          }
        });
      }
    } catch (error) {

    }



    //on click & on scroll navbar hide
    let menu = document.querySelector(".toggle-bar");
    main_navbar = document.querySelector(".main-navbar");
    //this code will show the disappear navbar. when there will be a 'active' class it will visible/block/clip path by css code
    menu.addEventListener("click", () => {
      main_navbar.classList.toggle("active");
    });
  };
  const toggleBarClosed = () => {

    try {
      if (toggle_bar) {
        const toggle_bar = document.querySelector(".toggle-bar");
        toggle_bar.classList.remove("open"); //toggle_bar is a var that i created above (when worked on toggle bar animation)
        menuOpen = false;
        main_navbar.classList.remove("active");
      }
    } catch (error) {

    }
  };

  useEffect(() => {
    
    toggleBar();
    window.addEventListener("scroll", listenScrollEvent,{passive: true});
    window.addEventListener("scroll", toggleBarClosed,{passive: true});

    return () => {

      window.addEventListener("scroll", toggleBarClosed,{passive: true});
      window.removeEventListener("scroll", listenScrollEvent,{passive: true});
    };
  }, []);
  async function logout() {
    let token = localStorage.getItem("login_token");
    //const logout_cre = await requestToAPI("", "logout", "POST", token);
    try {
      window.FB.logout();
    } catch (e) {

    }
    const logout_cre = API.jsonApiCall("logout", "", "POST", null, {
      "Content-Type": "application/json",
    })
      .then((res) => {
        toast.success("Logout SuccessFully");
      })
      .catch((e) => {
        toast.error("Something went wrong try later!");
      });

    localStorage.removeItem("login_token");
    localStorage.removeItem("userDetail");
    localStorage.setItem("userLoggedIn", false);
    window.location.href = "/";
  }
  useEffect(() => {
    const loginToken = localStorage.getItem("login_token");
    if (checkLoginToken !== loginToken && loginToken) {
      setLoginToken(loginToken);
    }
  });
  function redirectToProfile() {
    router.push('/profile');
  }

  const { isOpen } = props;
  // active current menu
  useEffect(() => {
    const page_path = router.asPath;
    let sub_menu = page_path;
    // page_path.replace(/\//g, '');
    let DropDownMenu
    if (sub_menu === '/') {
      DropDownMenu = "home";
    }
    if (sub_menu === '/map' || sub_menu === '/buyinghomes' || sub_menu === '/sellinghomes') {
      DropDownMenu = "properties";
    }
    if (sub_menu == '/calculator/mortgage-calculator' || sub_menu == '/calculator/land-transfer-tax-calculator' || sub_menu == '/calculator/mortgage-affordability-calculator') {
      DropDownMenu = "calculator";
    }
    if (sub_menu == '/aboutUs' || sub_menu == '/ContactUs') {
      DropDownMenu = "aboutUs";
    }

    setActiveMenu(DropDownMenu);
    setActiveSubMenu(sub_menu);

  }, [router]);
  return (
    <>
      {/* <!-- top navigation area start--> */}
      <header className={`top-header ${headerClass}`} id="top-header">
        <div className="container-fluid nav-container">
          {/* <!-- Brand logo --> */}
          <div className="logo">
            <Link href="/">
              <a><Image
                src={props.logo ? props.logo : "/images/logo/logo.png"}
                alt={"logo"}
                title={"Home"}
                // layout='fill'
                width={100}
                height={40}
                layout="responsive"
                objectFit="fill"
                className="img-fluid"
                // placeholder="blur"
                // blurDataURL={props.logo}
                priority={true}
              /></a>
            </Link>
          </div>
          <div className="d-flex align-items-center">
            <ul>
              {/* <!-- sign in button for mobile  --> */}
              {(!checkLoginToken || checkLoginToken === null) && (
                <li className="sign-inbtn-mb me-2">
                  <button
                    className="common-btn"
                    id="togglebtn"
                    onClick={props.togglePopUp}
                  >
                    Login
                  </button>
                </li>
              )}
              {checkLoginToken && checkLoginToken !== null && (
                  <Dropdown>
                    <Dropdown.Toggle variant="" id="dropdown-basic1">
                      <span className={activeSubMenu == '/profile' ? 'active' : ''}><img src="../images/social-icon/user.png" alt="user" width="31px" height="30px" /></span>
                    </Dropdown.Toggle>
                    <Dropdown.Menu>
                      <Dropdown.Item onClick={redirectToProfile} className={activeSubMenu == '/profile' ? 'active' : ''}>My Profile</Dropdown.Item>
                      <Dropdown.Item onClick={logout}>
                        Logout
                      </Dropdown.Item>
                    </Dropdown.Menu>
                  </Dropdown>
              )}
            </ul>
            {/* <!-- hamburger menu icon --> */}
            <div className="toggle-bar">
              <span className="toggle-icon"></span>
            </div>
          </div>
          {/* <!-- main nav bar --> */}
          <nav className="main-navbar">
            <ul>
              <li className={activeMenu == 'home' ? 'active' : ''} onClick={toggleBarClosed}>
                <Link href="/">Home</Link>
              </li>
              <li>
                <Dropdown>
                  <Dropdown.Toggle variant="" id="dropdown-basic2" className={activeMenu == 'properties' ? 'active' : ''}>
                    <span>Properties</span>
                  </Dropdown.Toggle>
                  <Dropdown.Menu className="openDropdown dropdownMobile">
                    <Dropdown.Item onClick={toggleBarClosed} className={activeSubMenu == '/map' ? 'active' : ''}><Link href="/map?status=Sale&propertyType=Residential"><a className="dropLinks">All Properties</a></Link></Dropdown.Item>
                    <Dropdown.Item onClick={toggleBarClosed} className={activeSubMenu == '/buyinghomes' ? 'active' : ''}><Link href="/buyinghomes"><a className="dropLinks">Buy</a></Link></Dropdown.Item>
                    <Dropdown.Item onClick={toggleBarClosed} className={activeSubMenu == '/sellinghomes' ? 'active' : ''}><Link href="/sellinghomes"><a className="dropLinks">Sell</a></Link></Dropdown.Item>
                  </Dropdown.Menu>
                </Dropdown>
              </li>
              <li onClick={toggleBarClosed} className={activeSubMenu == '/blogs' ? 'active' : ''}>
                <Link href="/blogs">Blog</Link>
              </li>
              <li className={activeMenu == 'calculator' ? 'active' : ''} onClick={toggleBarClosed}>
                <Link href="/calculator/mortgage-calculator">
                  Calculator
                </Link>
              </li>
              <li className={activeSubMenu == '/homevalue' ? 'active' : ''} onClick={toggleBarClosed}>
                <Link href="/homevalue">
                  Home Valuation
                </Link>
              </li>
              <li>
                <Dropdown>
                  <Dropdown.Toggle variant="" id="dropdown-basic3" className={activeMenu == 'aboutUs' ? 'active' : ''}>
                    <span >About</span>
                  </Dropdown.Toggle>

                  <Dropdown.Menu className="openDropdown dropdownMobile">
                    <Dropdown.Item onClick={toggleBarClosed} className={activeSubMenu == '/aboutUs' ? 'active' : ''}> <Link href="/aboutUs"><a className="dropLinks">About Wedu</a></Link></Dropdown.Item>
                    <Dropdown.Item onClick={toggleBarClosed} className={activeSubMenu == '/ContactUs' ? 'active' : ''}> <Link href="/ContactUs"><a className="dropLinks">Contact Us</a></Link></Dropdown.Item>

                  </Dropdown.Menu>

                </Dropdown>
              </li>
              {checkLoginToken && checkLoginToken !== null && (
                <>
                  <li>
                    <Dropdown>
                      <Dropdown.Toggle variant="" id="dropdown-basic" className={activeSubMenu == '/profile' ? 'active' : ''}>
                        <span ><img src="../images/social-icon/user.png" alt="user" width="25px" height="25px" /></span>
                      </Dropdown.Toggle>

                      <Dropdown.Menu className="openDropdown">
                        <Dropdown.Item onClick={redirectToProfile} className={activeSubMenu == '/profile' ? 'active' : ''}><a className="dropLinks">My Profile</a></Dropdown.Item>
                        <Dropdown.Item onClick={logout} >
                          <a className="dropLinks">Logout</a>
                        </Dropdown.Item>
                      </Dropdown.Menu>
                    </Dropdown>
                  </li>
                </>
              )}
              {(!checkLoginToken || checkLoginToken === null) && (
                <li className="sign-inbtn">
                  <button
                    className="common-btn"
                    id="togglebtn"
                    onClick={props.togglePopUp}
                  >
                    Login
                  </button>
                </li>
              )}
            </ul>
          </nav>
        </div>
        {isOpen && <PopupForm handleClose={props.togglePopUp} />}
      </header>
      {/* <!-- top navigation area end--> */}
    </>
  );
};
export default NavigationBar;