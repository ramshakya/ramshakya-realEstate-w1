// import logo from "../../public/images/logo/logo.png";
import PopupForm from "../Forms/PopupForm";
import { requestToAPI } from "../../pages/api/api";
import { useRouter } from "next/router";
import { useEffect, useState } from "react";
import detect from "../../ReactCommon/utility/detect";
import Link from "next/link";
import EmptyHeart from "./../../public/images/icon/heart1.png";
const Navbar = (props) => {
  const router = useRouter();
  const { isOpen, logo, logoAlt } = props;
  const [checkLoginToken, setLoginToken] = useState("");
  const [headerClass, setHeaderClass] = useState("");
  useEffect(() => {
    window.addEventListener("scroll", listenScrollEvent);
  }, []);
  function posY(elm) {
    var test = elm,
      top = 0;
    while (!!test && test.tagName.toLowerCase() !== "body") {
      top += test.offsetTop;
      test = test.offsetParent;
    }
    return top;
  }

  function viewPortHeight() {
    var de = document.documentElement;
    if (!!window.innerWidth) {
      return window.innerHeight;
    } else if (de && !isNaN(de.clientHeight)) {
      return de.clientHeight;
    }
    return 0;
  }

  function scrollY() {
    if (window.pageYOffset) {
      return window.pageYOffset;
    }
    return Math.max(
      document.documentElement.scrollTop,
      document.body.scrollTop
    );
  }

  function checkvisible(elm) {
    var vpH = viewPortHeight(), // Viewport Height
      st = scrollY(), // Scroll Top
      y = posY(elm);
    return y > vpH + st;
  }
  function listenToScroll(defaultH = 0) {
    const winScroll =
      document.body.scrollTop || document.documentElement.scrollTop;
    const height =
      document.documentElement.scrollHeight -
      document.documentElement.clientHeight +
      defaultH;
    const scrolled = (winScroll / height) * 100;
    return scrolled;
  }

  const listenScrollEvent = () => {
    if (document.getElementById("sidebar")) {
      if (detect.isMobile()) {
        console.log("isMobile", true);
        return true;
      }
      let sidebarOffset = document.querySelector("#sidebar").offsetTop;
      let sliderOffset = document.getElementById("sliderOff").clientHeight;
      let propSection = document.getElementById("propSection").clientHeight;
      let offsetFooter = document.querySelector("#footers").clientHeight;
      let visible = checkvisible(document.getElementById("footers"));
      sidebarOffset = sidebarOffset + sliderOffset;
      offsetFooter = propSection - document.documentElement.clientHeight;
      let heights = document.documentElement.clientHeight;
      heights = offsetFooter =
        document.documentElement.clientHeight - window.scrollY + offsetFooter;
      let scrolled = listenToScroll(document.documentElement.clientHeight);
      if (offsetFooter >= window.scrollY) {
        document.getElementById("sidebar").classList.add("sidebar-sticky");
      }
      if (!visible) {
        if (scrolled > 82) {
          document.getElementById("sidebar").classList.remove("sidebar-sticky");
          document.getElementById("sidebar").classList.add("sidebar-fixed-v2");
        }
      } else {
        document.getElementById("sidebar").classList.remove("sidebar-fixed-v2");
      }
      if (sidebarOffset <= window.scrollY) {
        document.getElementById("sidebar").classList.add("sidebar-sticky");
      } else {
        if (offsetFooter >= window.scrollY) {
          document.getElementById("sidebar").classList.remove("sidebar-sticky");
        }
      }
    }
  };
  useEffect(() => {
    const loginToken = localStorage.getItem("login_token");
    if (checkLoginToken !== loginToken) {
      setLoginToken(loginToken);
    }
  });
  async function logout() {
    let token = localStorage.getItem("login_token");
    const logout_cre = await requestToAPI(
      "",
      "api/v1/services/logout",
      "POST",
      token
    );
    localStorage.removeItem("login_token");
    localStorage.removeItem("userDetail");
    window.location.href = "/";
  }
  function gotosearch(city, status) {
    let filters = {
      searchFilter: {},
      preField: {},
    };
    let field = {
      text: city,
      value: city,
      category: "Cities",
      group: "City",
      isNavSearch: true,
    };
    filters.searchFilter.text_search = city;
    filters.preField.text_search = field;
    localStorage.setItem("filters", JSON.stringify(filters));
    localStorage.setItem("status", status);
    localStorage.setItem("navmapClicked", true);
    if (window.location.href.includes("map")) {
      props.popularSearch(true);
    } else {
      // setting url
      let filt = JSON.parse(localStorage.getItem("filters"));
      let textSearch = filt.preField.text_search.value;
      let category = filt.preField.text_search.category;
      let status = localStorage.getItem("status");
      console.log(status, "status bin");
      let params = "/map?";
      params += `text_search=${textSearch}`;
      params += `&propertySubType=["Detached","Semi-Detached","Freehold Townhouse","Condo Townhouse","Condo Apt"]&propertyType=Residential&status=${status}&Dom=90&soldStatus=A`;
      if (category !== "text_search") {
        params += `&group=${category}`;
      }
    }
  }
  function gotoHomevaluation() {
    //router.push('/Homevaluation');
  }
  function closeMenu(index) {
    let element = document.getElementsByClassName("navDropdown");
    for (var i = 0; i < element.length; i++) {
      if (index !== i) {
        element[i].classList.remove("show");
      }
    }
    if (element[index] !== undefined) {
      if (element[index].classList.contains("show")) {
        element[index].classList.remove("show");
      } else {
        element[index].classList.add("show");
      }
    }
  }
  useEffect(() => {
    const onClick = (e) => {
      if (
        e.target.tagName == "IMG" ||
        e.target.tagName == "BUTTON" ||
        e.target.tagName == "I" ||
        e.target.tagName == "A"
      ) {
      } else {
        closeMenu(3);
      }
    };
    document.body.addEventListener("click", onClick);
  }, []);
  return (
    <div className="nav-container">
      <div className="nav-topRow page-max">
        <div className="logoWrapper">
          <Link href="/">
            <a>
              {logo ? (
                <img
                  src={logo ? logo : "/images/logo/logo_v2.png"}
                  onClick={() => closeMenu(3)}
                  alt={logoAlt}
                  className="logo"
                />
              ) : (
                <img
                  src="/images/logo/logo_v2.png"
                  onClick={() => closeMenu(3)}
                  alt={logoAlt}
                  className="logo"
                />
              )}
            </a>
          </Link>
        </div>
        <nav className="nav-bottomRow">
          <ul className="page-max">
            <li onClick={() => closeMenu(3)}>
              <Link href='/map?text_search=Brampton&propertySubType=["Detached","Semi-Detached","Freehold Townhouse","Condo Townhouse","Condo Apt"]&propertyType=Residential&status=Sale&Dom=90&soldStatus=A&group=Cities'>
                <a onClick={() => gotosearch("Brampton", "Sale")} id="forSale">
                  For Sale
                </a>
              </Link>
            </li>
            <li onClick={() => closeMenu(3)}>
              <Link href='/map?text_search=Toronto&propertySubType=["Detached","Semi-Detached","Freehold Townhouse","Condo Townhouse","Condo Apt"]&propertyType=Residential&status=Lease&Dom=90&soldStatus=A&group=Cities'>
                <a onClick={() => gotosearch("Toronto", "Lease")} id="forRent">
                  For Rent
                </a>
              </Link>
            </li>
            <li onClick={() => closeMenu(3)}>
              <Link href="/#gotoHomevaluation" onClick={gotoHomevaluation}>
                Sell
              </Link>
            </li>
            <li onClick={() => closeMenu(3)}>
              <Link href="/blog/HOME">
                <a>Blog</a>
              </Link>
            </li>
            <li className=" aboutTab navDropdown_trigger">
              <a
                href="javascript:void(0)"
                className="barIcon"
                onClick={() => closeMenu(0)}
              >
                {/*<i className="fa fa-bars" aria-hidden="true"></i>*/}
                <img
                  src="../images/icon/barsss.png"
                  width="20px"
                  height="20px"
                />
              </a>
              <div className="navDropdown nav-drop-item-list">
                <ul className=" dropdownContent page-max">
                  <li onClick={() => closeMenu(0)}>
                    <Link href="/construction">New Homes & Condos</Link>
                  </li>
                  <li onClick={() => closeMenu(0)}>
                    <Link href="/marketStats">Market Statistics</Link>
                  </li>

                  <li onClick={() => closeMenu(0)}>
                    <Link href="/calculator/mortgage-calculator">
                      Calculators
                    </Link>
                  </li>
                  <li onClick={() => closeMenu(0)}>
                    <Link href="/aboutUs">About Us</Link>
                  </li>
                  <li onClick={() => closeMenu(0)}>
                    <Link href="/ContactUs">Contact Us</Link>
                  </li>
                  {/* <li><a href="javascript:void(0)">Strata Agents</a></li>
										<li><a href="javascript:void(0)">Why Hire Us</a></li>
										<li><a href="javascript:void(0)">Become a Strata Agent</a></li> */}
                </ul>
              </div>
            </li>
            {checkLoginToken && checkLoginToken !== null && (
              <li className=" aboutTab navDropdown_trigger">
                <button className="" onClick={() => closeMenu(1)}>
                  <i className="fa fa-user"></i>
                </button>
                <div className="navDropdown">
                  <ul className=" dropdownContent page-max">
                    <li onClick={() => closeMenu(1)}>
                      <Link href="/profile/user">
                        <a>My Profile</a>
                      </Link>
                    </li>
                    <li onClick={() => closeMenu(1)}>
                      <a href="javascript:void(0)" onClick={logout}>
                        Logout
                      </a>
                    </li>
                  </ul>
                </div>
              </li>
            )}
            {checkLoginToken && checkLoginToken !== null && (
              <li className=" aboutTab navDropdown_trigger">
                <button className="" onClick={() => closeMenu(2)}>
                  {/* <img {...EmptyHeart} width='25' height="25" /> */}
                  <i className="fa fa-heart"></i>
                </button>
                <div className="navDropdown">
                  <ul className=" dropdownContent page-max">
                    <li onClick={() => closeMenu(2)}>
                      <Link href="/profile/SavedSearches">
                        <a>Saved Searches</a>
                      </Link>
                    </li>
                    <li onClick={() => closeMenu(2)}>
                      <Link href="/profile/SavedHomes">
                        <a>Saved Homes</a>
                      </Link>
                    </li>
                    <li onClick={() => closeMenu(2)}>
                      <Link href="/profile/WatchList">
                        <a>Watched listings / communities </a>
                      </Link>
                    </li>
                  </ul>
                </div>
              </li>
            )}
          </ul>
        </nav>
        {/*<div className="searchBoxWrapper">
			         <div className=" autocomplete dark"><input type="text" value="" placeholder="Search any listing, building or location" className="" /></div>
			         <div className=" imgWrapper openButton"><img src="https://strata.ca/images/mag-white.png" alt="" className="" /></div>
			         <div className=" imgWrapper closeButton"><img src="https://strata.ca/images/close-x-black-thick.png" alt="" className="" /></div>
			      </div>*/}

        {(!checkLoginToken || checkLoginToken === null) && (
          <div className="accountLink desktopOnly">
            <button
              type="button"
              className="thin rounded"
              id="togglebtn"
              onClick={props.togglePopUp}
            >
              {" "}
              Sign In
            </button>
          </div>
        )}
      </div>
      {isOpen && <PopupForm handleClose={props.togglePopUp} />}
    </div>
  );
};
export default Navbar;
