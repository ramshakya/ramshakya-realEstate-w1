import { useState, useEffect, useRef, Component } from "react";
import Styles from "../../styles/Home.module.css";
import Autocomplete from "../../ReactCommon/Components/AutoSuggestion";
import Button from "../../ReactCommon/Components/Button";
import Constants from "../../constants/GlobalConstants";
import API from "../../ReactCommon/utility/api";
const propSqft = [
  "0-99",
  "100-199",
  "200-299",
  "300-399",
  "400-499",
  "500-599",
  "600-699",
  "700-799",
  "800-899",
  "900-999",
  "1000-1199",
  "1200-1399",
  "1400-1599",
  "1600-1799",
  "1800-1999",
  "2000-2249",
  "2250-2499",
  "3000-3249",
  "3250-3299",
  "3300-3349",
  "3350-3399",
  "3400-3449",
  "3450-3499",
  "3500-3549",
  "3550-3599",
  "3600-3649",
  "3650-3699",
  "3700-3749",
  "3750-3799",
  "3800-3849",
  "3850-3899",
  "3900-3949",
  "3950-3999",
  "4000-4499",
];


const MapHeader = (props) => {
  const [open, setOpen] = useState(false);
  const [show, setShow] = useState(true);
  const [isSelected, setIsSelected] = useState();
  useEffect(() => {
    if (screen.width < 650) {
      setShow(false);
    }
  }, []);
  
  function clearFilters(params) {
    props.resetBtn();
  }

  return (
    <>
      <div className="cityMapPage">
        <div className={("row mt-4  ml-2 ", Styles.gridView)}>
          <div className="col-md-4 col-12 form-group">
            <Autocomplete
              inputProps={{
                id: "autoSuggestion",
                name: "text_search",
                className: "auto form-control auto-suggestion-inp",
                placeholder: "MLS# ,Address,Neighborhood",
                title: "Search @MLS, Neighborhood",
                readOnly: false,
                autocomplete:"off"
              }}
              allList={[]}
              autoCompleteCb={props.autoCompleteSuggestion}
              cb={props.handleTypeHead}
              selectedText={props.text_search ? props.text_search.text : ''}
              // callBackMap={props.mapCallBack}
              extraProps={{}}
            />
          </div>
          <div className="filtersBtn" onClick={() => setShow(!show)}><i className="fa fa-filter"></i> More Filters</div>
          
          {show &&
            <div className="col-4 form-group filterValue">
              <div className="height">
                <div className="row p-0">
                  <div className="col-6 form-group pr-0">
                    <Autocomplete
                      inputProps={{
                        id: "price_min",
                        name: "price_min",
                        className:
                          "form-control on-focus-cls custom-form-control bg-white",
                        placeholder: "Min Price",
                        title: "Min Price",
                        readOnly: true,
                      }}
                      allList={Constants.minPriceConstant}
                      cb={props.handleTypeHead}
                      selectedText={props.price_min ? props.price_min.text : ""}
                      extraProps={{}}
                    />
                  </div>
                  <div className="col-6 form-group custom-col1 pl-0">
                    <Autocomplete
                      inputProps={{
                        id: "price_max",
                        name: "price_max",
                        className:
                          "form-control on-focus-cls custom-form-control bg-white",
                        placeholder: "Max Price",
                        title: "Max Price",
                        readOnly: true,
                      }}
                      allList={Constants.maxPrice}
                      cb={props.handleTypeHead}
                      selectedText={props.price_max ? props.price_max.text : ""}
                      extraProps={{}}
                    />
                  </div>
                </div>
              </div>
            </div>
          }
          {show && <div className="col-2 form-group filterValue">
            <Autocomplete
              inputProps={{
                id: "beds",
                name: "beds",
                className:
                  "form-control on-focus-cls custom-form-control bg-white",
                placeholder: "Beds",
                title: "Beds",
                readOnly: true,
              }}
              allList={Constants.filterBeds}
              cb={props.handleTypeHead}
              extraProps={{}}
              selectedText={props.beds ? props.beds.text : ''}
            />
          </div>
          }
          {show && <div className="col-2 form-group custom-col1-md filterValue">
            <Autocomplete
              inputProps={{
                id: "baths",
                name: "baths",
                className:
                  "form-control on-focus-cls custom-form-control bg-white",
                placeholder: "Baths",
                title: "Baths",
                readOnly: true,
              }}
              allList={Constants.filterBaths}
              cb={props.handleTypeHead}
              extraProps={{}}
              selectedText={props.baths ? props.baths.text : ''}
            />
          </div>
          }
          {show && <div className="col-3 form-group filterValue">
            <Autocomplete
              inputProps={{
                id: "status",
                name: "status",
                className:
                  "form-control on-focus-cls custom-form-control bg-white",
                placeholder: "Status",
                title: "Status",
                readOnly: true,
              }}
              allList={Constants.propertyStatus}
              cb={props.handleTypeHead}
              extraProps={{}}
              selectedText={props.status ? props.status.text : ''}
            />
          </div>
          }
          {show &&
            <div className="col-3 form-group custom-col1-md filterValue">
              <button
                type="button"
                className="form-control on-focus-cls custom-form-control"
                style={{ borderRadius: "0px" }}
                onClick={() => setOpen(!open)}
              >
                More
            </button>
              {open == true && <MoreOption cb={setOpen}{...props} basement={props.basement} />}
            </div>
          }
          {show && <div className="col-3 form-group filterValue">
            <Autocomplete
              inputProps={{
                id: "sort_by",
                name: "sort_by",
                className:
                  "form-control on-focus-cls custom-form-control bg-white",
                placeholder: "Sort By",
                title: "Sort By",
                readOnly: true,
              }}
              allList={Constants.sortStatus}
              cb={props.handleTypeHead}
              selectedText={props.sort_by ? props.sort_by.text : ""}
              extraProps={{}}

            />
          </div>
          }
          {show && <div className="col-md-3 col-btn filterValue">
            <Button
              props={{
                className: "btn border reset-btn",
                type: "button",
                name: "resetBtn",
                size: "md",
              }}
              extraProps={{
                label: "Clear",
                btnDivCls: "te",
              }}
              cb={clearFilters}
              showBtn={true}
            />
          </div>}
        </div>
        {/* <div className="mb-2"></div> */}
        <div className={"row labelSection"}>
          <div className="col-7">
            <p>
              <i>{props.searchLabel}</i>
            </p>
          </div>
          <div className="col-5 gridMapViewBtn">
            
          </div>
           
        </div>
      </div>

    </>
  );
};

const MoreOption = (props) => {
  let selectedFeatures = [];
  let basementKey = [];
  let propertySubType = [];
  preSelect();
  function preSelect() {
    let storeData = localStorage.getItem('morefilters');
    if (storeData) {
      console.log("MoreOption==>>storeData", JSON.parse(storeData));
      storeData = JSON.parse(storeData);
      basementKey = storeData.basementKey;
      propertySubType = storeData.propertySubType;
      selectedFeatures = storeData.selectedFeatures;

    }
     
  }
  function featuresData(e) {
    props.featuresData(e);
    console.log("featuresData===>>>1", e.target.value);
    let elm = document.getElementById(e.target.attributes.id.value);

    console.log("elm====>>",elm);
    
    preSelect();
  }
  function basementData(e) {
    props.basementData(e);
    preSelect();
  }
  function propertySubData(e) {
    props.propertySubData(e);
    preSelect();
  }
  const {
    basement
  } = props;
  console.log("===basement", selectedFeatures);
  const morefilter = {
    position: "absolute",
    backgroundColor: "rgb(255, 255, 255)",
    zIndex: "996",
    padding: "10px",
    left: "49%",
    boxShadow: "rgb(28 28 28 / 10%) 1px 1px 8px 2px",
    width: "50%",
    maxHeight: "450px!important",
    overflowY: "scroll",
  };
  return (
    <div id="example-collapse-text" style={morefilter}>
      <span
        style={{
          borderRadius: "0px",
          float: "right",
          position: "relative",
          zIndex: "99",
          cursor: "pointer",
        }}
        onClick={() => props.cb(false)}
      >
        X
      </span>
      <div className="container">
        <div className="row">
          <div className="col-4">
            <label>
              <b>Size </b>
            </label>
            {/* <select
              className="form-control on-focus-cls custom-form-control"
              title="Size"
              name="size"
              onChange={props.sizeSqft}
              style={{ borderRadius: "0px !important" }}
            >
              <option>Select Size</option>

              {propSqft.map((item, key) => {
                return (
                  <option value={item} key={key}>
                    {item} Sq.ft
                  </option>
                );
              })}
              <option value="4500-7000"> Above 4500 + Sq.ft</option>
            </select> */}
            <Autocomplete
              inputProps={{
                id: "Sqft",
                name: "Sqft",
                className:
                  "form-control bg-white",
                placeholder: "Size",
                title: "Size",
                readOnly: true,
              }}
              allList={Constants.propSqft}
              cb={props.handleTypeHead}
              selectedText={props.Sqft ? props.Sqft.text : ""}
              extraProps={{}}
            />
          </div>
          <div className="col-4 openHouse">
            <label></label>
            <div
              className="form-control on-focus-cls custom-form-control"
              style={{ borderRadius: "0px" }}
            >
              <div
                className="form-check"
                style={{ minHeight: "0px", marginBottom: "0px" }}
              >
                <input
                  type="checkbox"
                  disabled
                  className="form-check-input"
                  id="house"
                  onChange={props.openhouse}
                />
                <label className="form-check-label" htmlFor="house">
                  Open House
                </label>
              </div>
            </div>
          </div>
          <div className="col-4">
            <label></label>
            <Autocomplete
              inputProps={{
                id: "Dom",
                name: "Dom",
                className:
                  "form-control bg-white",
                placeholder: "Dom",
                title: "Dom",
                readOnly: true,
              }}
              allList={Constants.dom}
              cb={props.handleTypeHead}
              selectedText={props.Dom ? props.Dom.text : ""}
              extraProps={{}}
            />
            {/* <select
              className="form-control on-focus-cls custom-form-control"
              name="day_on_market"
              title=""
              onChange={props.dom}
              style={{ borderRadius: "0px !important" }}
            >
              <option value="" disabled>
                Days on Market
              </option>
              <option value="3"> 3 days </option>
              <option value="7"> 7 days </option>
              <option value="14"> 14 days </option>
              <option value="30"> 30 days </option>
            </select> */}

          </div>
        </div>
        <div className="row propertyCheckBox">
          <div className="col-12 pt-2">
            <label>
              <strong>Property Sub Type</strong>
            </label>
          </div>
          {
            props.subtype.map((item, key) => {
              return (
                <div className="col-4" key={key}>
                  <div className="form-check">
                    {
                      <input
                      type="checkbox"
                      value={item.value}
                      className="form-check-input"
                      id={`basement${key}`}
                      checked={propertySubType.includes(`${item.value}`) ? "true" : false}
                      onChange={propertySubData}
                    />
                    }
                    <label className="form-check-label" htmlFor={`basement${key}`}>
                      {item.text}
                    </label>
                  </div>
                </div>
              )
            })

          }

          <div className="col-12">
            <hr />
          </div>
          <div className="col-12">
            <label>
              <b>Features</b>
            </label>
          </div>
          {props.features.length &&
            props.features.map((item, i) => {
              let id = "features" + i;
              if (!item.text) {
                return;
              }
              return (
                <div className="col-4" key={i}>
                  <div className="form-check">
                    <input
                      type="checkbox"
                      className="form-check-input checkboxState"
                      id={id}
                      title={item.text}
                      value={item.value}
                      checked={selectedFeatures.includes(`${item.value}`)  ? true : false}
                      onChange={featuresData}
                    />
                    <label title={item.text} className="form-check-label" htmlFor={id}>
                      {item.text}
                    </label>
                  </div>
                </div>
              );
            })}

          <div className="col-12">
            <hr />
          </div>
          <div className="col-12">
            <label>
              <b>Basement</b>
            </label>
          </div>
          {
            basement.map((item, key) => {
              return (
                <div className="col-4" key={key}>
                  <div className="form-check">
                    <input
                      type="checkbox"
                      value={item.value}
                      className="form-check-input"
                      id={`basement${key}`}
                      checked={basementKey.includes(`${item.value}`) ? "true" : false}
                      onChange={basementData}
                    />
                    <label className="form-check-label" htmlFor={`basement${key}`}>
                      {item.text}
                    </label>
                  </div>
                </div>
              )
            })

          }
        </div>
      </div>
    </div>
  );
};
export default MapHeader;
