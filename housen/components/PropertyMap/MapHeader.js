import { useState, useEffect, useRef } from "react";
import Constants from "../../constants/Global";
import Autocomplete from "../../ReactCommon/Components/AutoSuggestion";
import Slider from "@material-ui/core/Slider";
import detect from "../../ReactCommon/utility/detect";
import { useRouter } from "next/router";
const Header = (props) => {
  const router = useRouter();
  const priceDivRef = useRef(null);

  const [open, setOpen] = useState(false);
  const [Sale, setSale] = useState("filter-btn-active");
  const [Lease, setLease] = useState(false);
  const [Sold, setSold] = useState("for-sold-bg-color");
  const [Active, setActive] = useState("for-active-bg-color");
  const [Listed, setListed] = useState("for-delisted-bg-color");
  const [value, setValue] = useState([0, 5000000]);
  const [openPrice, setopenPrice] = useState(false);
  const [soldORLease, setSoldOrLease] = useState("Sold");
  const [disable1, setdisable1] = useState(false);
  const [disable2, setdisable2] = useState(true);
  const [disable3, setdisable3] = useState(true);
  const [Dom, setDom] = useState(false);
  const [step, setstep] = useState(50000);
  const rangeSelector = (event, newValue) => {
    if (newValue[0] !== value[0]) {
      if (newValue[0] >= 0) {
        setstep(50000);
      }
      if (newValue[0] >= 1000000) {
        setstep(100000);
      }
      if (newValue[0] >= 1500000) {
        setstep(250000);
      }
    }
    if (newValue[1] !== value[1]) {
      if (newValue[1] >= 0) {
        setstep(50000);
      }
      if (newValue[1] >= 1000000) {
        setstep(100000);
      }
      if (newValue[1] >= 1500000) {
        setstep(250000);
      }
    }
    setValue(newValue);
  };
  var formatter = new Intl.NumberFormat("en-US", {
    style: "currency",
    currency: "USD",
    minimumFractionDigits: 0,
  });
  let sliderRange = ["300k", "1m", "2m", "3m", "4m", "Max"];
  const handleInput = (e) => {
    let value = e.target.value;
    document.getElementById("textSearch").value = value;
  };
  const handleChangeCommitted = (event, newValue) => {
    props.getPriceFilter(value[0], value[1]);
    // Here will only be triggered once the user releases the slider.
  };
  useEffect(() => {
    if (props.isReset) {
      setDom(true);
      setActive("filter-btn-active");
      setSold("for-sold-bg-color");
      setListed("for-delisted-bg-color");
      setdisable1(false);
      setdisable2(true);
      setdisable3(true);
      if (props.isReset) {
        setValue([0,5000000]);
      }
    }
  }, [props.isReset]);
  useEffect(() => {
    Constants.Dom.map((item)=>{
      if(item.value===props.propertySearchFilter.Dom){
        setDom(item)
      }
    });
    setStatus();
    setPriceSlider();
    setBaths();
    setBeds();
    setSubType();
  }, [props]);

  useEffect(() => {
    document.addEventListener("click", handleOuterClick);
  }, []);
  function setPriceSlider(){
    let min=props.propertySearchFilter.price_min?props.propertySearchFilter.price_min:0;
    let max=props.propertySearchFilter.price_max?props.propertySearchFilter.price_max:5000000;
    setValue([min,max]);
  }
  function setBaths() {
    try {
      document.getElementById("baths-" + props.bathsState).checked = true;
    } catch (error) {}
  }
  function setBeds() {
    try {
      document.getElementById("beds-" + props.bedsState).checked = true;
    } catch (error) {}
  }
  function setSubType() {
    if (props.isDefaultSubTypes && props.propertySubTyp && props.propertySubTyp.length) {
      setTimeout(() => {
        try {
          document.getElementById("All").checked = true;
        } catch (error) {}
      }, 1000);
    } else {
      try {
        let SubType = Constants.SubType;
        document.getElementById("All").checked = false;
        props.propertySubType.map((item, k) => {
          let index = SubType.indexOf(item);
          if (SubType.indexOf(item) > -1) {
            document.getElementById("propertySubType" + index).checked = true;
          }
        });
      } catch (error) {}
    }
  }
  function setStatus(){
    if (props.propertySearchFilter.status == "Sale") {
      setSale("filter-btn-active");
      setLease("");
      setSoldOrLease("Sold");
    } else if (props.propertySearchFilter.status == "Lease") {
      setLease("filter-btn-active");
      setSale("");
      setSoldOrLease("Leased");
    } else if (props.propertySearchFilter.status) {
      setSale("");
      setLease("");
    }else{
      setSale("");
      setLease("");
    }
  }
  function handleStatusType(e) {
    if (e.target.value == "Sale") {
      setSale("filter-btn-active");
      setLease("");
      setSoldOrLease("Sold");
    } else if (e.target.value == "Lease") {
      setLease("filter-btn-active");
      setSale("");
      setSoldOrLease("Leased");
    } else if (e.target.value) {
      setSale("");
      setLease("");
    }
    props.handleStatusType(e);
  }
  function handleActiveSold(e) {
    setDom(true);
    if (e.target.value == "A") {
      setActive("filter-btn-active");
      setSold("for-sold-bg-color");
      setListed("for-delisted-bg-color");
      setdisable1(false);
      setdisable2(true);
      setdisable3(true);
    } else if (e.target.value == "U") {
      setSold("filter-btn-active");
      setActive("for-active-bg-color");
      setListed("for-delisted-bg-color");
      setdisable1(true);
      setdisable2(false);
      setdisable3(true);
    } else if (e.target.value == "D") {
      setListed("filter-btn-active");
      setActive("for-active-bg-color");
      setSold("for-sold-bg-color");
      setdisable1(true);
      setdisable2(true);
      setdisable3(false);
    }
    props.ActiveSold(e);
  }
  function handleOuterClick(e) {
    props.showmenu(e);
    if (
      priceDivRef !== null &&
      priceDivRef.current !== null &&
      !priceDivRef.current.contains(e.target)
    ) {
      setopenPrice(false);
    }
    //
  }
  useEffect(() => {
    let status = "";
    if (localStorage.getItem("status")) {
      status = localStorage.getItem("status");
    }
    if (props.propertySearchFilter.status) {
      status = props.propertySearchFilter.status;
    }

    if (status == "Sale") {
      setSale("filter-btn-active");
      setLease("");
    } else if (status == "Sold") {
      setSold("filter-btn-active");
      setActive("");
    } else {
      setSale("");
      setLease("filter-btn-active");
    }
  }, [props.propertySearchFilter.status]);
  useEffect(() => {
    if (!detect.isMobile()) {
      setOpen(true);
    }
  }, []);

  function All_remove(e) {
    if (e.target.value === "All") {
      let checked = document.getElementsByClassName("SubTypeCheck");
      for (var i = 0; i < checked.length; i++) {
        checked[i].checked = false;
      }
    } else {
      document.getElementById("All").checked = false;
    }
    props.propertySubData(e);
  }

  const redirectToMap = async (e) => {
    if (e) {
      e.preventDefault();
      let value = e.target.text_search.value;
      let name = e.target.text_search.name;
      let filters = {
        searchFilter: {},
        preField: {},
      };
      let field = { text: value, value: value, category: "text_search" };
      props.handleTypeHead(field, "text_search");
    }
  };
  useEffect(() => {
    if (props.open) {
      setOpen(props.open);
      props.openModal(false);
    }
  }, [props.open]);

  return (
    <>
      <div className="">
        {!open && (
          <button
            className="btn mobile_filter_btn shadow"
            onClick={() => setOpen(!open)}
          >
            <i className="fa fa-filter"></i> Filters
          </button>
        )}
        <div id="filter_section" className={open ? "" : "hide"}>
          <div className="filter-top">
            <form
              className="filter-input-container filter-part-1"
              onSubmit={redirectToMap}
            >
              <div className="filter-input-area">
                {props.inputCity ? (
                  <Autocomplete
                    inputProps={{
                      id: "autoSuggestion",
                      name: "text_search",
                      className:
                        "auto form-control auto-suggestion-inp filter-input inp-focus-cls",
                      placeholder: "MLS# ,Address,Neighborhood",
                      title: "Search @MLS , Neighborhood",
                      readOnly: false,
                      id: "searchByText",
                      autocomplete: "off",
                    }}
                    allList={[]}
                    autoCompleteCb={props.autoCompleteSuggestion}
                    cb={props.handleTypeHead}
                    selectedText={
                      props.text_search ? props.text_search.text : ""
                    }
                    // callBackMap={props.mapCallBack}
                    extraProps={{}}
                  />
                ) : (
                  <Autocomplete
                    inputProps={{
                      id: "autoSuggestion123",
                      name: "text_search",
                      className:
                        "auto form-control auto-suggestion-inp filter-input inp-focus-cls",
                      placeholder: "MLS# ,Address,Neighborhood,City",
                      title: "Search @MLS , City , Neighborhood",
                      readOnly: false,
                      id: "searchByText",
                      autocomplete: "off",
                    }}
                    allList={[]}
                    autoCompleteCb={props.autoCompleteSuggestion}
                    cb={props.handleTypeHead}
                    selectedText={
                      props.text_search ? props.text_search.text : ""
                    }
                    // callBackMap={props.mapCallBack}
                    extraProps={{}}
                  />
                )}
                <button
                  onClick={props.handleTypeHeadClick}
                  className="filter-search-btn"
                  name=""
                  value=""
                  id="textSearch"
                >
                  <i className="fa fa-search"></i>
                </button>
              </div>
            </form>
            {open && (
              <div className="filter-part-2">
                <div className="filter-option-container checkbox-opt" hidden>
                  <label
                    for="Type"
                    className="theme-text gray-text"
                    onClick={props.showmenu}
                    data-suffix=" Property Types"
                    data-default="All Property Type"
                  >
                    Residential Properties
                  </label>
                  <div className="checkbox-opt-container" hidden>
                    <div className="checkbox-title theme-text">
                      Property Type
                    </div>
                    <div className="checkbox-line">
                      {props.property_type &&
                        Array.isArray(props.property_type) &&
                        props.property_type.map((item, key) => {
                          return (
                            <div className="checkbox-label" key={key}>
                              <input
                                type="radio"
                                onClick={props.handleMoreFilter}
                                name="propertyType"
                                value={item.value}
                              />
                              <span className="checkbox-text">
                                {item.value}
                              </span>
                            </div>
                          );
                        })}
                    </div>
                  </div>
                </div>
                <div className="filter-buttom-group button-group-1">
                  <button
                    onClick={handleStatusType}
                    name="status"
                    value="Sale"
                    className={"filter-btn filter-btn-left  " + Sale}
                    id="sale_btn1"
                    data-target="lease_btn"
                    data-value="Sale"
                    data-filter="SaleLease"
                    data-lease="false"
                  >
                    for Sale
                  </button>
                  <button
                    onClick={handleStatusType}
                    name="status"
                    value="Lease"
                    className={"filter-btn  filter-btn-right  " + Lease}
                    data-target="sale_btn"
                    data-value="Lease"
                    data-filter="SaleLease"
                    data-lease="true"
                  >
                    for Lease
                  </button>
                </div>
                <div className="filter-buttom-group button-group-2">
                  <button
                    onClick={handleActiveSold}
                    name="ListingStatus"
                    value="A"
                    className={"filter-btn filter-btn-left   " + Active}
                    id="active_btn1"
                    data-target="lease_btn"
                    data-value="Active"
                    data-filter="SaleLease"
                    data-lease="false"
                  >
                    Active
                  </button>
                  <div className="filter-option-container sold-opt">
                    <span
                      className="bold-text theme-text autoSuggestion-inner-text"
                      style={{ position: "absolute" }}
                    >
                      {" "}
                      <i
                        className="fa fa-sort-down headerMarginTop"
                        style={{
                          background: "#fff",
                          padding: "18%",
                          marginTop: "-40% !important",
                        }}
                      ></i>
                    </span>
                    <Autocomplete
                      inputProps={{
                        name: "Dom",
                        className:
                          "hoverAble auto  placeHolderCls form-control auto-suggestion-inp filter-input inp-focus-cls theme-text labelCls leftBorder ",
                        placeholder: "DOM",
                        title: "DOM",
                        readOnly: true,
                        id: "Dom",
                        disabled: disable1,
                      }}
                      readOnly="true"
                      allList={Constants.Dom}
                      // autoCompleteCb={props.autoCompleteSuggestion}
                      cb={props.handleTypeHead}
                      selectedText={Dom ? Dom.text : "Last 90 days"}
                      // callBackMap={props.mapCallBack}
                      extraProps={{}}
                    />
                  </div>
                </div>

                <div className="filter-buttom-group button-group-2">
                  <button
                    onClick={handleActiveSold}
                    name="ListingStatus"
                    value="U"
                    className={"filter-btn    " + Sold}
                    id="sold_btn"
                    data-target="active_btn"
                    data-value="Sold"
                    data-filter="Status"
                  >
                    {soldORLease}
                  </button>
                  <div className="filter-option-container sold-opt">
                    <span
                      className="bold-text theme-text autoSuggestion-inner-text"
                      style={{ position: "absolute" }}
                    >
                      {" "}
                      <i
                        className="fa fa-sort-down headerMarginTop"
                        style={{
                          background: "#fff",
                          padding: "18%",
                          marginTop: "-40% !important",
                        }}
                      ></i>
                    </span>
                    <Autocomplete
                      inputProps={{
                        name: "Dom",
                        className:
                          "hoverAble auto  placeHolderCls form-control auto-suggestion-inp filter-input inp-focus-cls theme-text labelCls leftBorder",
                        placeholder: "DOM",
                        title: "DOM",
                        readOnly: true,
                        id: "Dom",
                        disabled: disable2,
                      }}
                      allList={Constants.Dom}
                      // autoCompleteCb={props.autoCompleteSuggestion}
                      cb={props.handleTypeHead}
                      selectedText={Dom ? Dom.text : "Last 90 days"}
                      // callBackMap={props.mapCallBack}
                      extraProps={{}}
                    />
                  </div>
                </div>
                <div className="filter-buttom-group button-group-2">
                  <button
                    onClick={handleActiveSold}
                    name="ListingStatus"
                    value="D"
                    className={"filter-btn   " + Listed}
                    id="sold_btn"
                    data-target="active_btn"
                    data-value="De-listed"
                    data-filter="Status"
                  >
                    De-listed
                  </button>
                  <div className="filter-option-container sold-opt">
                    <span
                      className="bold-text theme-text autoSuggestion-inner-text"
                      style={{ position: "absolute" }}
                    >
                      {" "}
                      <i
                        className="fa fa-sort-down headerMarginTop"
                        style={{
                          background: "#fff",
                          padding: "18%",
                          marginTop: "-40% !important",
                        }}
                      ></i>
                    </span>
                    <Autocomplete
                      inputProps={{
                        name: "Dom",
                        className:
                          "hoverAble auto   placeHolderCls form-control auto-suggestion-inp filter-input inp-focus-cls theme-text labelCls leftBorder filter-btn-right",
                        placeholder: "DOM",
                        title: "DOM",
                        readOnly: true,
                        id: "Dom",
                        disabled: disable3,
                      }}
                      allList={Constants.Dom}
                      // autoCompleteCb={props.autoCompleteSuggestion}
                      cb={props.handleTypeHead}
                      selectedText={Dom ? Dom.text : "Last 90 days"}
                      // callBackMap={props.mapCallBack}
                      extraProps={{}}
                    />
                  </div>
                </div>
              </div>
            )}
          </div>
          {open && (
            <div className="filter-div desktop-content filter-flex-opt">
              {/*<div className="filter-buttom-group">
								<button onClick={handleStatusType} name="status" value="Sale" className={"filter-btn filter-btn-left " + Sale} id="sale_btn1" data-target="lease_btn" data-value="Sale" data-filter="SaleLease" data-lease="false">Sale</button>
								<button onClick={handleStatusType} name="status" value="Lease" className={"filter-btn  filter-btn-right " + Lease} data-target="sale_btn" data-value="Lease" data-filter="SaleLease" data-lease="true">Lease</button>

							</div>
							<div className="filter-buttom-group">
								<button onClick={handleActiveSold} name="ListingStatus" value="A" className={"filter-btn filter-btn-left " + Active} id="active_btn1" data-target="lease_btn" data-value="Active" data-filter="SaleLease" data-lease="false">Active</button>
								<button onClick={handleActiveSold} name="ListingStatus" value="U" className={"filter-btn filter-btn-right " + Sold} id="sold_btn" data-target="active_btn" data-value="Sold" data-filter="Status">Sold</button>
							</div>*/}
              <div className="filter-option-container checkbox-opt">
                <label
                  for="Type"
                  className="theme-text gray-text"
                  onClick={props.showmenu}
                  data-suffix=" Property Types"
                  data-default="All Property Type"
                >
                  Property Sub Type <i className="fa fa-sort-down"></i>
                </label>
                <div className="checkbox-opt-container subtypeCheckbox subtypebox">
                  <div className="checkbox-title theme-text">
                    {/* Property Sub Type */}
                  </div>
                  <div className="checkbox-line">
                    <div className="checkbox-label d-block" key="">
                      <input
                        type="checkbox"
                        name="propertySubType"
                        id="All"
                        className="checkboxState"
                        value="All"
                        onChange={All_remove}
                      />
                      <span className="checkbox-text w-100 text-left">All</span>
                    </div>
                    {Constants.SubType.map((item, key) => {
                      return (
                        <div className="checkbox-label d-block" key={key}>
                          <input
                            type="checkbox"
                            name="propertySubType"
                            id={"propertySubType" + key}
                            className="checkboxState SubTypeCheck"
                            value={item}
                            onChange={All_remove}
                          />
                          <span className="checkbox-text w-100 text-left">
                            {item}
                          </span>
                        </div>
                      );
                    })}
                    <div className="checkbox-label d-block">
                      <input
                        type="checkbox"
                        name="propertySubType"
                        className="checkboxState SubTypeCheck"
                        value="others"
                        onChange={All_remove}
                      />
                      <span className="checkbox-text w-100 text-left">
                        Other
                      </span>
                    </div>
                  </div>
                </div>
              </div>
              <div className="filter-option-container checkbox-opt">
                <label
                  for="Bedroom"
                  className="theme-text gray-text"
                  onClick={props.showmenu}
                  data-suffix=" Bedroom"
                  data-default="Any Bedroom"
                >
                  Any Bedroom <i className="fa fa-sort-down"></i>
                </label>
                <div className="checkbox-opt-container ">
                  <div className="checkbox-title theme-text">Bedroom</div>
                  <div className="checkbox-line">
                    {Constants.filterBeds.map((item) => {
                      return (
                        <div className="checkbox-label" key={"bed" + item.text}>
                          <input
                            type="radio"
                            onClick={props.handleMoreFilter}
                            name="beds"
                            value={item.value}
                            id={"beds-" + item.value}
                            className="checkboxState"
                          />
                          <span className="checkbox-text">{item.text}</span>
                        </div>
                      );
                    })}
                  </div>
                </div>
              </div>
              <div className="filter-option-container checkbox-opt">
                <label
                  for="Bathroom"
                  className="theme-text gray-text"
                  onClick={props.showmenu}
                  data-suffix=" Bathroom"
                  data-default="Any Bathroom"
                >
                  Any Bathroom <i className="fa fa-sort-down"></i>
                </label>
                <div className="checkbox-opt-container">
                  <div className="checkbox-title theme-text">Bathroom</div>
                  <div className="checkbox-line">
                    {Constants.filterBaths.map((item) => {
                      return (
                        <div
                          className="checkbox-label"
                          key={"bath" + item.text}
                        >
                          <input
                            type="radio"
                            onClick={props.handleMoreFilter}
                            name="baths"
                            value={item.value}
                            id={"baths-" + item.value}
                            className="checkboxState"
                          />
                          <span className="checkbox-text">{item.text}</span>
                        </div>
                      );
                    })}
                  </div>
                </div>
              </div>
              <div
                className="filter-option-container position-relative"
                ref={priceDivRef}
              >
                <label
                  for="Bathroom"
                  className="hoverAble theme-text gray-text"
                  onClick={() => setopenPrice(!openPrice)}
                  data-suffix=" Bathroom"
                  data-default="Any Bathroom"
                >
                  Price <i className="fa fa-sort-down"></i>
                </label>

                {openPrice && (
                  <div className="rangSliderPrice">
                    <div
                      style={{
                        margin: "auto",
                        display: "block",
                        width: "97%",
                      }}
                    >
                      {formatter.format(value[0])} -{" "}
                      {value[1] >= 5000000 ? "Max" : formatter.format(value[1])}
                      <Slider
                        value={value}
                        onChange={rangeSelector}
                        min={0}
                        max={5000000}
                        step={step}
                        valueLabelDisplay="off"
                        onChangeCommitted={handleChangeCommitted}
                      />
                      <div class="price_label">
                        {sliderRange.map((item) => {
                          return <span>{item}</span>;
                        })}
                      </div>
                    </div>
                  </div>
                )}
              </div>
              <div className="filter-option-container sold-opt">
                <span
                  className=" bold-text theme-text autoSuggestion-inner-text"
                  style={{ position: "absolute" }}
                >
                  {" "}
                  <i
                    className="fa fa-sort-down headerMarginTop"
                    style={{
                      background: "#fff",
                      padding: "18%",
                      marginTop: "-40% !important",
                    }}
                  ></i>
                </span>
                <Autocomplete
                  inputProps={{
                    name: "sort_by",
                    className:
                      "hoverAble auto  placeHolderCls form-control auto-suggestion-inp filter-input inp-focus-cls theme-text labelCls",
                    placeholder: "Sort by",
                    title: "Sort by",
                    readOnly: true,
                    id: "sort_by",
                  }}
                  allList={Constants.sortStatus}
                  // autoCompleteCb={props.autoCompleteSuggestion}
                  cb={props.handleTypeHead}
                  selectedText={props.sort_by ? props.sort_by.text : ""}
                  // callBackMap={props.mapCallBack}
                  extraProps={{}}
                />
              </div>
              <button
                onClick={props.resetBtn}
                className="filter-menu-btn filter-top-reset-btn theme-btn-primary"
                type="reset"
              >
                {" "}
                Reset
              </button>
              &nbsp;
              <button
                className="filter-menu-btn filter-top-reset-btn back-ground-color theme-btn-primary"
                onClick={props.savedSearch}
              >
                Save search
              </button>
              {/* <div className="checkbox-opt residential">
								<label for="Type" className="theme-text gray-text">Residential</label>
							</div> */}
              <div className="show_in_mobile">
                <button
                  className="btn applyFilter cancelbtn filter-menu-btn  theme-btn-primary "
                  onClick={() => setOpen(!open)}
                >
                  Cancel
                </button>

                <button
                  className="btn applyFilter applybtn filter-menu-btn  theme-btn-primary "
                  onClick={() => setOpen(!open)}
                >
                  Apply
                </button>
              </div>
            </div>
          )}
        </div>
      </div>
    </>
  );
};
export default Header;
