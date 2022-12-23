import Image from "next/image";
import { front_url, autoSuggestionApi } from "../../constants/Global";
import React, { useState, useEffect, useRef } from "react";
import Autocomplete from "../../ReactCommon/Components/AutoSuggestion";
import { useRouter } from "next/router";
import data2 from "../../public/json/data.json";
import Constants from "../../constants/Global";
import API from "../../ReactCommon/utility/api";
const HeadingSection = (props) => {
  const { banner } = props;
  const router = useRouter();
  const [Sale, setSale] = useState("btn-active");
  const [Lease, setLease] = useState(false);
  const [status, setstatus] = useState("");
  const [data1, setData1] = useState([]);
  let propData = {
    title: "Property Type",
    className: "form-select",
    name: "propertyType",
    id: "propertyType",
  };
  let propData1 = {
    title: "Property Sub Type",
    className: "form-select",
    name: "propertySubType",
    id: "propertySubType",
  };
  let propPrice = {
    title: "Price",
    className: "form-select",
    id: "price",
    name: "price",
  };
  const [data, setData] = useState([]);
  useEffect(() => {
    // getFiltersData();
  }, []);
  function get_Auto() {
    setData1(data2);
    return;
    API.jsonApiCall(
      Constants.base_url + "api/v1/services/home/getSearchData",
      "",
      "POST",
      {}
    ).then((res) => {
      setData1(res);
    });
  }
  useEffect(() => {
    get_Auto();
  }, []);
  const fetchPropertyData = async (fieldValue, fieldName, cb) => {
    let payload = {
      query: "default",
      type: "",
    };
    //
    let dataList = [];
    if (fieldValue) {
      let matches = data1.filter((findValue) => {
        const regex = new RegExp(`^${fieldValue}`, "gi");
        if (findValue.value !== null) {
          return findValue.value.match(regex);
        }
      });
      if (fieldValue.length === 0) {
        matches = [];
      }
      if (matches.length > 0) {
        let temp_list = [];
        let temp_city = "";
        let temp_community = false;

        matches.map((item, key) => {
          if (key == 0) {
            if (item.category == "Cities") {
              let obj = {
                isHeading: true,
                text: "Cities",
                value: "Cities",
                category: "Cities",
                group: "City",
              };
              temp_list.push(obj);
              temp_list.push(item);
            }
            if (item.category === "Neighborhood") {
              let obj = {
                isHeading: true,
                text: "Neighborhood",
                value: "Neighborhood",
                category: "Neighborhood",
                group: "Community",
              };
              temp_list.push(obj);
              temp_list.push(item);
            }
          } else {
            if (item.category === "Neighborhood") {
              if (!temp_community) {
                let obj = {
                  isHeading: true,
                  text: "Neighborhood",
                  value: "Neighborhood",
                  category: "Neighborhood",
                  group: "Community",
                };
                temp_list.push(obj);
                temp_community = true;
              }
            }
            temp_list.push(item);
          }
        });
        cb({ allList: temp_list });
      } else {
        let requestOptions = {};
        payload.query = fieldValue;
        payload.type = "address";
        requestOptions = {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(payload),
        };
        fetch(autoSuggestionApi, requestOptions)
          .then((response) => response.text())
          .then((res) => JSON.parse(res))
          .then((json) => {
            if (json.length) {
              dataList = dataList.concat(json);
              cb({ allList: dataList });
            }
          })
          .catch((e) => {
            console.log("error", e);
          });
        if (fieldValue.indexOf(" ") <= 0) {
          payload.query = fieldValue;
          payload.type = "listingId";
          requestOptions = {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(payload),
          };
          fetch(autoSuggestionApi, requestOptions)
            .then((response) => response.text())
            .then((res) => JSON.parse(res))
            .then((json) => {
              if (json.length) {
                dataList = dataList.concat(json);
                cb({ allList: dataList });
              }
            })
            .catch((e) => {
              console.log("error", e);
            });
        }
      }
    } else {
      localStorage.removeItem("suggestionList");
      cb({ allList: [] });
    }
  };

  const setValues = async (fieldValue, fieldName) => {
    let Prevfilters = JSON.parse(localStorage.getItem("filters"));
    let filters = {
      searchFilter: {},
      preField: {},
    };
    if (Prevfilters) {
      filters = Prevfilters;
    }
    if (fieldName === "autoSuggestion") {
      filters.searchFilter.text_search = fieldValue.value;
      filters.preField.text_search = fieldValue;
    }
    localStorage.setItem("filters", JSON.stringify(filters));
    redirectToMap("");
  };
  function containsNumbers(str) {
    return /\d/.test(str);
  }
  const redirectToMap = async (e) => {
    if (e) {
      e.preventDefault();
      let value = e.target.autoSuggestion.value;
      let name = e.target.autoSuggestion.name;
      let filters = {
        searchFilter: {},
        preField: {},
      };
      let field = { text: value, value: value, category: "text_search" };
      filters.searchFilter.text_search = value;
      filters.preField.text_search = field;
      localStorage.setItem("filters", JSON.stringify(filters));
    }
    localStorage.removeItem("status");
    localStorage.removeItem("propertytype");
    let filt = JSON.parse(localStorage.getItem("filters"));
    let textSearch = filt.preField.text_search.value;
    let category = filt.preField.text_search.category;
    let params = "/map?";
    if (textSearch) {
      params += `text_search=${textSearch}`;
      if (
        category === "text_search" &&
        !filt.preField.text_search.group &&
        category !== "ListingId"
      ) {
        if (containsNumbers(textSearch)) {
          params += `&fromHome=1`;
        } else
          params += `&propertySubType=["Detached","Semi-Detached","Freehold Townhouse","Condo Townhouse","Condo Apt"]&propertyType=Residential&status=Sale&Dom=90&soldStatus=A`;
      }
      if (
        category !== "text_search" &&
        filt.preField.text_search.group &&
        category !== "ListingId"
      ) {
        params += `&propertySubType=["Detached","Semi-Detached","Freehold Townhouse","Condo Townhouse","Condo Apt"]&propertyType=Residential&status=Sale&Dom=90&soldStatus=A`;
        params += `&group=${category}`;
      }
      if (category === "ListingId") {
        params += `&group=${category}`;
        params += `&fromHome=1`;
      }
    } else {
      params += `propertySubType=["Detached","Semi-Detached","Freehold Townhouse","Condo Townhouse","Condo Apt"]&propertyType=Residential&status=Sale&Dom=90&soldStatus=A`;
    }
    // end setting url
    router.push(params);
  };

  function handleStatusType(e) {
    // localStorage.removeItem("filters");
    // if (e.target.value == 'Sale') {
    //     setSale('btn-active');
    //     setLease('');
    //     // setSold('');
    // }
    // else if (e.target.value == 'Lease') {
    //     setLease('btn-active');
    //     setSale('');
    //     // setSold('');
    // }
    // setstatus(e.target.value);
    // redirectToMap(e.target.value)
  }
  // useEffect(()=>{
  //     redirectToMap();
  // },[])

  return (
    <>
      <form
        onSubmit={redirectToMap}
        id="search_section"
        style={{
          background:
            "linear-gradient(var(--theme-opacity), var(--theme-opacity)), url(" +
            banner +
            ")",
        }}
      >
        <div className="search_section_center">
          <div className="search_quote">
            <h1 className="pb-3">Let’s Find Your Dream Home</h1>
            {/* <p className=" subtitle-text">Search<a href="javascript:void(0)"> 1499 Condos for Sale</a> and<a  href="javascript:void(0)"> 4019 Condos for Rent</a> in the GTA</p> */}
            <p className=" subtitle-text search_quote">
              <b>
                Search Active & Sold Listings, Sold Price Data, Market Stats &
                Daily Listings Alerts!{" "}
              </b>
            </p>
          </div>
          <div className="searchBarWrapper desktopOnly">
            <div className=" searchAutocompleteContainer">
              <div className="autocomplete">
                <Autocomplete
                  inputProps={{
                    id: "autoSuggestion",
                    name: "autoSuggestion",
                    className: "auto form-control pb-2 border-square",
                    // placeholder: "Search any listing #, address or neighbourhood ",
                    placeholder:
                      "Search by MLS Listing #, Address, City, or Neighborhood",
                    title: "Search @MLS , City , Community",
                    readOnly: false,
                    autoComplete: "off",
                  }}
                  allList={[]}
                  autoCompleteCb={fetchPropertyData}
                  cb={setValues}
                  extraProps={{}}
                />
                {/*<input type="text" placeholder="Search by Condo Name, MLS # or filters"/>*/}
              </div>
              <button type="submit" className="btn searchbtn">
                <svg
                  width="20px"
                  height="20px"
                  viewBox="0 0 20 20"
                  version="1.1"
                >
                  <g
                    id="Symbols"
                    stroke="none"
                    strokeWidth="1"
                    fill="none"
                    fillRule="evenodd"
                  >
                    <g
                      id="Asset-Library"
                      transform="translate(-540.000000, -174.000000)"
                      fill="#000000"
                    >
                      <g
                        id="Navigation-Assets"
                        transform="translate(0.000000, 55.000000)"
                      >
                        <path
                          d="M559.748809,137.536731 L556.599328,134.388315 C556.567525,134.35652 556.526653,134.342516 556.491521,134.31669 C559.558224,130.653581 559.375445,125.174931 555.933542,121.733815 C554.170053,119.97096 551.825393,119 549.331825,119 C546.838026,119 544.493481,119.97096 542.730222,121.733815 C539.089926,125.373278 539.089926,131.29511 542.730222,134.934114 C544.493481,136.697544 546.838026,137.668503 549.331825,137.668503 C551.545945,137.668503 553.639973,136.899679 555.316321,135.494031 C555.341809,135.528811 555.355586,135.5691 555.387044,135.600436 L558.536525,138.748852 C558.703919,138.916208 558.923322,139 559.142724,139 C559.362127,139 559.58176,138.916322 559.748924,138.748852 C560.083711,138.414027 560.083711,137.871556 559.748809,137.536731 L559.748809,137.536731 Z M549.331825,135.954201 C547.29589,135.954201 545.382,135.161272 543.942506,133.722107 C540.970866,130.751148 540.970866,125.91747 543.942506,122.945937 C545.382,121.506657 547.296005,120.714302 549.331825,120.714302 C551.367414,120.714302 553.281649,121.506657 554.721143,122.945822 C557.692783,125.917355 557.692783,130.751033 554.721143,133.721993 C553.28142,135.161157 551.367529,135.954201 549.331825,135.954201 L549.331825,135.954201 Z"
                          id="Assets/Navigation/search"
                        ></path>
                      </g>
                    </g>
                  </g>
                </svg>
              </button>
            </div>

            {/*<form className="searchBar__container form">
						      <div className={"searchBarSection ownershipSection"}><button value="Sale" onClick={handleStatusType} type="button" className={Sale}>Sale</button></div>
						      <div className={"searchBarSection ownershipSection"}><button value="Lease" onClick={handleStatusType} type="button" className={Lease}>Rent</button></div>
						      <div className="searchBar__submitButtonContainer">
						         <button type="button" onClick={redirectToMap}>
						            View Listings!
						         </button>
						      </div>
						 </form>*/}
          </div>
        </div>
      </form>
    </>
  );
};
export default HeadingSection;
