import React, { useState, useEffect, useRef } from "react";
import Constants from "../constants/GlobalConstants";
import Link from "next/link";
let autoComplete;
import global from "../constants/GlobalConstants";

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
    {componentRestrictions: { country: "ca" } }
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
  props.setMetaInfo(global.pageMeta.buldings);
  useEffect(() => {
    let ApiKey = Constants.REACT_APP_GOOGLE_API_KEY;
    if (localStorage.getItem("websetting")) {
      let websetting = localStorage.getItem("websetting");
      if (websetting && websetting !== "undefined") {
        JSON.parse(websetting);
        if (websetting.GoogleMapApiKey != null) {
          ApiKey = websetting.GoogleMapApiKey;
        }
      }
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
            {/* <h1 className="heading-text text-center">
                            Buying Real Estate ?
                                <hr className="hr" />
                        </h1> */}
            <div className="row">
              <div className="col-lg-12">
                <h1>WHAT'S YOUR HOME WORTH?</h1>
                <h5>
                  Thinking of selling or interested in learning about a
                  neighbor's house? We can help you see what it's worth.
                </h5>
              </div>
              <div className="col-md-10 col-lg-10 pt-4">
                <form className="searchForm row" onSubmit={handleSubmit}>
                  <div className="col-lg-8 colmd-8">
                    <input
                      type="text"
                      name="searchinput"
                      className="form-control py-2"
                      placeholder="Enter your street address"
                      ref={autoCompleteRef}
                    />
                    <p className="errormsg">{msg}</p>
                  </div>
                  <div className="col-lg-4 colmd-4">
                    <button className="common-btn search-btn py-2 rounded">
                      Show me now!
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </section>
    </>
  );
}

export default HomeValue;
