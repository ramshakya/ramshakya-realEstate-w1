import { useState, useEffect, useRef, Component } from "react";
import Autocomplete from "../../ReactCommon/Components/AutoSuggestion";
import Button from "../../ReactCommon/Components/Button";
import Constants from "../../constants/GlobalConstants";
import filterImage from '../../public/images/icons/icons8-funnel-50.png';
const MapHeader = (props) => {
  const [open, setOpen] = useState(false);
  const [show, setShow] = useState(true);
  useEffect(() => {
    if (screen.width < 650) {
      setShow(false);
    }
  }, []);
  function resetMap() {
    props.resetBtn()
  }
 
  useEffect(()=>{
     let pType = { "value": "", "text": "Property Type" }
      if(props.property_type){
        props.property_type.unshift(pType);
      }
  },[props.property_type])
  return (
    <>
      <div className="mapHeadersRow mb-3">
        <div className="filtersBtn" onClick={() => setShow(!show)}> {show ? 'x' : <img src={filterImage.src} alt="icon" className="filterImg"/>}</div>
        <div className="row">
          {show && <div className="col-md-2 col-lg-2 col-12">
            <Autocomplete
              inputProps={{
                id: "autoSuggestion",
                name: "text_search",
                className: "auto form-control auto-suggestion-inp",
                placeholder: "MLS# ,Address,Neighborhood,City",
                title: "Search @MLS , City , Neighborhood",
                readOnly: false,
                autoComplete: "Off"
              }}
              allList={[]}
              autoCompleteCb={props.autoCompleteSuggestion}
              cb={props.handleTypeHead}
              selectedText={props.text_search ? props.text_search.text : ''}
              extraProps={{}}
            />
          </div>}
          {show && <div className="col-md-1 col-lg-1 col-12">
            <Autocomplete
              inputProps={{
                id: "propertyType",
                name: "propertyType",
                className:
                  "form-control bg-white propertyType",
                placeholder: "Property Type",
                title: "Propertysss Type",
                readOnly: true,
              }}
              allList={props.property_type}
              cb={props.handleTypeHead}
              extraProps={{}}
              selectedText={props.propertyType ? props.propertyType.text : ""}
            />
          </div>
          }
          {show && <div className="col-md-1 col-lg-1 col-12">
            <Autocomplete
              inputProps={{
                id: "price_min",
                name: "price_min",
                className:
                  "form-control bg-white",
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
          }
          {show && <div className="col-md-1 col-lg-1 col-12">
            <Autocomplete
              inputProps={{
                id: "price_max",
                name: "price_max",
                className:
                  "form-control  bg-white",
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
          }
          {show && <div className="col-md-1 col-lg-1 col-12">
            <Autocomplete
              inputProps={{
                id: "beds",
                name: "beds",
                className:
                  "form-control bg-white",
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
          {show && <div className="col-md-1 col-lg-1 col-12">
            <Autocomplete
              inputProps={{
                id: "baths",
                name: "baths",
                className:
                  "form-control bg-white",
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
          {show && <div className="col-md-1 col-lg-1 col-12">
            <Autocomplete
              inputProps={{
                id: "status",
                name: "status",
                className:
                  "form-control bg-white",
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
          {show && <div className="col-md-1 col-lg-1 col-12">
            <button
              type="button"
              className="form-control"

              onClick={() => setOpen(!open)}
            >
              More
            </button>
            {open == true && <MoreOption cb={setOpen}{...props} basement={props.basement} />}
          </div>
          }
          {show && <div className="col-md-1 col-lg-1 col-12">
            <Autocomplete
              inputProps={{
                id: "sort_by",
                name: "sort_by",
                className:
                  "form-control bg-white",
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
          {show && <div className="col-md-1 col-lg-1 col-6">
            <Button
              props={{
                className: "btn reset-btn",
                type: "button",
                name: "resetBtn",
                size: "md",
              }}
              extraProps={{
                label: "Clear",
                btnDivCls: "te",
              }}
              cb={resetMap}
              showBtn={true}
            />
          </div>
          }
          {show &&
            <div className="col-md-1 col-lg-1 col-6">
              <Button
                props={{
                  className: "btn reset-btn",
                  type: "button",
                  size: "md",
                }}
                extraProps={{
                  label: "Save",
                }}
                cb={props.savedSearch}
              />
            </div>
          }

        </div>
      </div>
    </>
  );
};

const MoreOption = (props) => {
  const { basement, basementKey, propertySubType, selectedFeatures, features } = props;
  console.log(propertySubType,"propertySubType");
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

            {/*  */}
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
            <label>  </label>
            <div
              className="form-control on-focus-cls custom-form-control"
              style={{ borderRadius: "0px", padding: "0.76rem 0.75rem !important;" }}
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
            <label>  </label>
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
          </div>
        </div>
        <div className="row propertyCheckBox">
          <div className="col-12 pt-2">
            <label>
              <strong>Property Sub Type</strong>
            </label>
          </div>
          {props.subtype && props.subtype.length &&
            props.subtype.map((item, key) => {
              return (
                <div className="col-4" key={key}>
                  <div className="form-check">
                    <input
                      type="checkbox"
                      value={item.value}
                      className="form-check-input checkboxState"
                      id={key}
                      title={item.text}
                      checked={propertySubType.includes(`${item.value}`) ? true : false}
                      onChange={props.propertySubData}
                    />
                    <label title={item.text} className="form-check-label" htmlFor={key}>
                      {item.text}
                    </label>
                  </div>
                </div>
              );
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
          {Array.isArray(features) && features.length &&
            features.map((item, i) => {
              let id = "features" + i;
              if (item.text) {
                return (
                  <div className="col-4" key={i}>
                    <div className="form-check">
                      <input
                        type="checkbox"
                        className="form-check-input checkboxState"
                        id={id}
                        title={item.text}
                        value={item.value}
                        checked={selectedFeatures.includes(`${item.value}`) ? "true" : false}
                        onChange={props.featuresData}
                      />
                      <label title={item.text} className="form-check-label" htmlFor={id}>
                        {item.text}
                      </label>
                    </div>
                  </div>
                );
              }
            })}
          <div className="col-12">
            <hr />
          </div>
          <div className="col-12">
            <label>
              <b>Basement</b>
            </label>
          </div>
          {basement && basement.map((item, i) => {
            let id = "basement" + i;
            return (
              <div className="col-4" key={i}>
                <div className="form-check">
                  <input
                    type="checkbox"
                    className="form-check-input checkboxState"
                    id={id}
                    title={item.text}
                    value={item.value}
                    checked={basementKey.includes(`${item.value}`) ? true : false}
                    onChange={props.basementData}
                  />
                  <label title={item.text} className="form-check-label" htmlFor={id}>
                    {item.text}
                  </label>
                </div>
              </div>
            );
          })}
        </div>
      </div>
    </div>
  );
};
export default MapHeader;