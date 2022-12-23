import React, { useState, useEffect, useRef } from "react";
import Constants from "../../constants/Global";
import Link from "next/link";
let autoComplete;
// import global from "../../constants/Global";

const loadScript = (url, callback) => {
  let script = document.createElement("script");
  script.type = "text/javascript";

  if (script.readyState) {
    script.onreadystatechange = function () {
      if (script.readyState === "loaded" || script.readyState === "complete") {
        script.onreadystatechange = null;
        callback();
      }
    };
  } else {
    script.onload = () => callback();
  }

  script.src = url;
  document.getElementsByTagName("head")[0].appendChild(script);
};

function handleScriptLoad(updateQuery, autoCompleteRef) {
  autoComplete = new window.google.maps.places.Autocomplete(
    autoCompleteRef.current,
    { componentRestrictions: { country: "ca" } }
  );
  autoComplete.setFields([
    "address_components",
    "formatted_address",
    "geometry",
  ]);
  autoComplete.addListener("place_changed", () =>
    handlePlaceSelect(updateQuery)
  );
}

async function handlePlaceSelect(updateQuery) {
  const addressObject = autoComplete.getPlace();
  const query = addressObject.formatted_address;
  updateQuery(query);
  const lat = addressObject.geometry.location.lat();
  const lng = addressObject.geometry.location.lng();
  localStorage.setItem("lat", lat);
  localStorage.setItem("lng", lng);
}

function HomeValue(props) {
  const [query, setQuery] = useState("");
  const [flag, setFlag] = useState("");
  const [msg, setMsg] = useState("");
  const autoCompleteRef = useRef(null);
  // props.setMetaInfo(global.pageMeta.homeValuation);
  useEffect(() => {
    let ApiKey = "";
    if (localStorage.getItem("websetting")) {
      try {
        let websetting = JSON.parse(localStorage.getItem("websetting"));
        if (websetting.GoogleMapApiKey != null) {
          ApiKey = websetting.GoogleMapApiKey;
        }
      } catch (error) {}
    }
    loadScript(
      `https://maps.googleapis.com/maps/api/js?key=${ApiKey}&libraries=places`,
      () => handleScriptLoad(setQuery, autoCompleteRef)
    );
  }, []);

  const handleSubmit = () => {
    event.preventDefault();
    if (query !== "" && query !== undefined) {
      localStorage.setItem("googleSearch", query);
      setMsg("");
      // console.log("yes");
      window.location.href = "/homevalueConfirm";
    } else {
      setMsg("Please select your street address ");
    }
  };

  return (
    <>
      <section
        className="homeValue"
        style={{ backgroundImage: `url(../images/homevalue.jpg)` }}
      >
        <div className="backgroundBlur">
          <div className="container">
            <div className="row">
              <div className="col-lg-12 text-center">
                <h3 className="text-white text-uppercase text-center">
                  What's My Home Worth?
                  {/*WHAT'S YOUR HOME WORTH IN TODAY'S MARKET?*/}
                </h3>
                <h6 className="text-white text-center">
                  Type Your Address Below to Recieve Your Home Market Evaluation
                  Online. Free & No Obligation!
                  {/*Thinking of selling or interested in 
					                	learning about what your neighbor's house sold for? Get an estimated value of your home with our free online home evaluation tool*/}
                </h6>
              </div>
              <div className="col-md-1 col-lg-1 pt-4"></div>
              <div className="col-md-10 col-lg-10 pt-4">
                <form className="searchForm row" onSubmit={handleSubmit}>
                  <div className="col-lg-9 col-md-9">
                    <input
                      type="text"
                      name="searchinput"
                      className="form-control py-2"
                      placeholder="Enter your street address"
                      ref={autoCompleteRef}
                    />
                    <p className="errormsg">{msg}</p>
                  </div>
                  <div className="col-lg-3 col-md-3 text-center">
                    <button className="common-btn search-btn py-2 rounded">
                      Get Estimate!
                    </button>
                  </div>
                </form>
              </div>
              <div className="col-md-1 col-lg-1 pt-4"></div>
            </div>
          </div>
        </div>
      </section>
    </>
  );
}

export default HomeValue;
