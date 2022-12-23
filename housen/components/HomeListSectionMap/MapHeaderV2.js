import { useState, useEffect, useRef, Component } from "react";
import Styles from "../../styles/Home.module.css";
import Autocomplete from "../../ReactCommon/Components/AutoSuggestion";
import Button from "../../ReactCommon/Components/Button";
import Constants from "../../constants/GlobalConstants";
import Input from "../../ReactCommon/Components/Input";
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
const renderPriceList = () => {
    if (
        Constants.minPriceConstant &&
        Constants.minPriceConstant &&
        Constants.minPriceConstant.length >= 0 ||
        Array.isArray(Constants.minPriceConstant)
    ) {
        return Constants.minPriceConstant.map((data, index) => {
            if (index == 0) {
                return (
                    <li key={index} data-set={data.value} className="priceList" >
                        <a>Any</a>
                    </li>
                )
            }
            return (
                <li key={index} data-set={data.value} className="priceList" >
                    <a>{data.text}</a>
                </li>
            )
        });
    }
}
const MapHeader = (props) => {
    const [open, setOpen] = useState(false);
    const maxProps = {
        type: "number",
        name: "max_price",
        placeholder: "Max Price",
        className: "form-control borderRadius placeHolder",
        autoComplete: "off",
        id: "max_price",
    };
    const minProps = {
        type: "number",
        name: "min_price",
        placeholder: "Min Price",
        className: "form-control borderRadius placeHolder",
        autoComplete: "off",
        id: "min_price",
    };
    return (
        <>
            <div className="container mapHeaderV2">
                <div className="row">
                    <div className="col-md-3 form-group">
                        <Autocomplete
                            inputProps={{
                                id: "autoSuggestion",
                                name: "text_search",
                                className: "auto form-control auto-suggestion-inp borderRadius",
                                placeholder: "City,Address,or Community",
                                title: "Search @MLS , City , Neighborhood",
                                readOnly: false,
                            }}
                            allList={[]}
                            autoCompleteCb={props.autoCompleteSuggestion}
                            cb={props.handleTypeHead}
                            selectedText={props.text_search ? props.text_search.text : ''}
                            // callBackMap={props.mapCallBack}
                            extraProps={{}}
                        />
                    </div>
                    <div className="col-md-3">
                        <div className="row">
                            <div className="col-md-3 form-group status">
                                <Autocomplete
                                    inputProps={{
                                        id: "status",
                                        name: "status",
                                        className:
                                            "form-control on-focus-cls inputStatus custom-form-control bg-white borderRadius",
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
                            <div className="col-md-7 form-group prices">
                                <input type="text" placeholder="Any Price" className="form-control borderRadius priceInp" />
                                <div className="pricePop">
                                    <ul className="minList">
                                        <li>
                                            <Input props={minProps} extraProps={{}} />
                                        </li>
                                        {
                                            renderPriceList()
                                        }
                                    </ul>
                                    <ul className="maxList">
                                        <li>
                                            <Input props={maxProps} extraProps={{}} />
                                        </li>
                                        {
                                            renderPriceList()
                                        }
                                    </ul>
                                </div>
                            </div>
                            <div className="col-md-2 form-group ">
                                <p>pppppppd</p>
                            </div>
                        </div>
                    </div>
                    <div className="col-md-3">

                    </div>
                    <div className="col-md-3">

                    </div>
                </div>
            </div>


            {/* <div className=" p-0">
                <div className={("row mt-4  ml-2 ", Styles.gridView)}>

                    <div className="col-2 form-group">
                        <div className="row p-0">
                            <div className="col-6 form-group">
                                <Autocomplete
                                    inputProps={{
                                        id: "propertyType",
                                        name: "propertyType",
                                        className:
                                            "form-control on-focus-cls custom-form-control bg-white",
                                        placeholder: "Property Type",
                                        title: "Property Type",
                                        readOnly: true,
                                    }}
                                    allList={props.property_type}
                                    cb={props.handleTypeHead}
                                    extraProps={{}}
                                    selectedText={props.propertyType ? props.propertyType.text : ""}
                                />
                            </div>
                            <div className="col-6 form-group">
                                <Autocomplete
                                    inputProps={{
                                        id: "propertySubType",
                                        name: "propertySubType",
                                        className:
                                            "form-control on-focus-cls custom-form-control bg-white",
                                        placeholder: "Property Sub Type",
                                        title: "Property Sub Type",
                                        readOnly: true,
                                    }}
                                    allList={props.subtype}
                                    cb={props.handleTypeHead}
                                    extraProps={{}}
                                    selectedText={props.propertySubType ? props.propertySubType.text : ""}
                                />
                            </div>
                        </div>
                    </div>
                    <div className="col-2 form-group">
                        <div className="height">
                            <div className="row p-0">
                                <div className="col-6 form-group">
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
                                <div className="col-6 form-group">
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
                    <div className="col-1 form-group">
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
                    <div className="col-1 form-group">
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

                    <div className="col-1 form-group">
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
                    <div className="col-1 form-group ">
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
                    <div className="col-md-1 col-sm-1 col-xs-12 col-lg-1">
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
                            cb={props.resetBtn}
                            showBtn={true}
                        />
                    </div>
                </div>
                <div className={"row labelSection"}>
                    <div className="col-7">
                        <p>
                            <i>{props.searchLabel}</i>
                        </p>
                    </div>
                    <div className="col-5 gridMapViewBtn">

                    </div>
                </div>
            </div> */}
            <style jsx>
                {`
          .row > * {
            padding-right: 0px !important;
          }
        `}
            </style>
        </>
    );
};

const MoreOption = (props) => {
    const { basement } = props;
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
                        <select
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
                        </select>
                    </div>
                    <div className="col-4">
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
                        <select
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
                        </select>
                    </div>
                </div>
                <div className="row">
                    <div className="col-12 pt-2">
                        <label>
                            <strong>Property Type</strong>
                        </label>
                    </div>
                    <div className="col-4">
                        <div className="form-check">
                            <input
                                type="checkbox"
                                value="Att/Row/Twnhouse"
                                className="form-check-input"
                                id="exampleCheck1"
                                onChange={props.multiplePropType}
                            />
                            <label className="form-check-label" htmlFor="exampleCheck1">
                                Att/Row/Twnhouse
                            </label>
                        </div>
                    </div>
                    <div className="col-4">
                        <div className="form-check">
                            <input
                                type="checkbox"
                                value="Condo Apt"
                                className="form-check-input "
                                onChange={props.multiplePropType}
                                id="exampleCheck2"
                            />
                            <label className="form-check-label" htmlFor="exampleCheck2">
                                Condo Apt
                            </label>
                        </div>
                    </div>
                    <div className="col-4">
                        <div className="form-check">
                            <input
                                type="checkbox"
                                value="Cottage"
                                className="form-check-input"
                                onChange={props.multiplePropType}
                                id="exampleCheck3"
                            />
                            <label className="form-check-label" htmlFor="exampleCheck3">
                                Cottage
                            </label>
                        </div>
                    </div>
                    <div className="col-4">
                        <div className="form-check">
                            <input
                                type="checkbox"
                                value="Detached"
                                className="form-check-input"
                                onChange={props.multiplePropType}
                                id="exampleCheck4"
                            />
                            <label className="form-check-label" htmlFor="exampleCheck4">
                                Detached
                            </label>
                        </div>
                    </div>
                    <div className="col-4">
                        <div className="form-check">
                            <input
                                type="checkbox"
                                value="Duplex"
                                className="form-check-input"
                                onChange={props.multiplePropType}
                                id="exampleCheck5"
                            />
                            <label className="form-check-label" htmlFor="exampleCheck5">
                                Duplex
                            </label>
                        </div>
                    </div>
                    <div className="col-4">
                        <div className="form-check">
                            <input
                                type="checkbox"
                                value="Farm"
                                className="form-check-input"
                                onChange={props.multiplePropType}
                                id="exampleCheck6"
                            />
                            <label className="form-check-label" htmlFor="exampleCheck6">
                                Farm
                            </label>
                        </div>
                    </div>
                    <div className="col-4">
                        <div className="form-check">
                            <input
                                type="checkbox"
                                value="Fourplex"
                                className="form-check-input"
                                onChange={props.multiplePropType}
                                id="exampleCheck7"
                            />
                            <label className="form-check-label" htmlFor="exampleCheck7">
                                Fourplex
                            </label>
                        </div>
                    </div>
                    <div className="col-4">
                        <div className="form-check">
                            <input
                                type="checkbox"
                                value="Investment"
                                className="form-check-input"
                                onChange={props.multiplePropType}
                                id="exampleCheck8"
                            />
                            <label className="form-check-label" htmlFor="exampleCheck8">
                                Investment
                            </label>
                        </div>
                    </div>
                    <div className="col-4">
                        <div className="form-check">
                            <input
                                type="checkbox"
                                value="Link"
                                className="form-check-input"
                                onChange={props.multiplePropType}
                                id="exampleCheck9"
                            />
                            <label className="form-check-label" htmlFor="exampleCheck9">
                                Link
                            </label>
                        </div>
                    </div>
                    <div className="col-4">
                        <div className="form-check">
                            <input
                                type="checkbox"
                                value="Lower Level"
                                className="form-check-input"
                                onChange={props.multiplePropType}
                                id="exampleCheck10"
                            />
                            <label className="form-check-label" htmlFor="exampleCheck10">
                                Lower Level
                            </label>
                        </div>
                    </div>
                    <div className="col-4">
                        <div className="form-check">
                            <input
                                type="checkbox"
                                value="Room"
                                className="form-check-input"
                                onChange={props.multiplePropType}
                                id="exampleCheck11"
                            />
                            <label className="form-check-label" htmlFor="exampleCheck11">
                                Room
                            </label>
                        </div>
                    </div>
                    <div className="col-4">
                        <div className="form-check">
                            <input
                                type="checkbox"
                                value="Other"
                                className="form-check-input"
                                onChange={props.multiplePropType}
                                id="exampleCheck12"
                            />
                            <label className="form-check-label" htmlFor="exampleCheck12">
                                Other
                            </label>
                        </div>
                    </div>
                    <div className="col-4">
                        <div className="form-check">
                            <input
                                type="checkbox"
                                value="Multiplex"
                                className="form-check-input"
                                onChange={props.multiplePropType}
                                id="exampleCheck13"
                            />
                            <label className="form-check-label" htmlFor="exampleCheck13">
                                Multiplex
                            </label>
                        </div>
                    </div>
                    <div className="col-4">
                        <div className="form-check">
                            <input
                                type="checkbox"
                                value="Mobile/Trailer"
                                className="form-check-input"
                                onChange={props.multiplePropType}
                                id="exampleCheck14"
                            />
                            <label className="form-check-label" htmlFor="exampleCheck14">
                                Mobile/Trailer
                            </label>
                        </div>
                    </div>
                    <div className="col-4">
                        <div className="form-check">
                            <input
                                type="checkbox"
                                value="Upper Level"
                                className="form-check-input"
                                onChange={props.multiplePropType}
                                id="exampleCheck15"
                            />
                            <label className="form-check-label" htmlFor="exampleCheck15">
                                Upper Level
                            </label>
                        </div>
                    </div>
                    <div className="col-4">
                        <div className="form-check">
                            <input
                                type="checkbox"
                                value="Triplex"
                                className="form-check-input"
                                onChange={props.multiplePropType}
                                id="exampleCheck16"
                            />
                            <label className="form-check-label" htmlFor="exampleCheck16">
                                Triplex
                            </label>
                        </div>
                    </div>
                    <div className="col-4">
                        <div className="form-check">
                            <input
                                type="checkbox"
                                value="Store W/Apt/Offc"
                                className="form-check-input"
                                onChange={props.multiplePropType}
                                id="exampleCheck17"
                            />
                            <label className="form-check-label" htmlFor="exampleCheck17">
                                Store W/Apt/Offc
                            </label>
                        </div>
                    </div>
                    <div className="col-4">
                        <div className="form-check">
                            <input
                                type="checkbox"
                                value="Semi-Detached"
                                className="form-check-input"
                                onChange={props.multiplePropType}
                                id="exampleCheck18"
                            />
                            <label className="form-check-label" htmlFor="exampleCheck18">
                                Semi-Detached
                            </label>
                        </div>
                    </div>
                    <div className="col-4">
                        <div className="form-check">
                            <input
                                type="checkbox"
                                value="Rural Resid"
                                className="form-check-input"
                                onChange={props.multiplePropType}
                                id="exampleCheck19"
                            />
                            <label className="form-check-label" htmlFor="exampleCheck19">
                                Rural Resid
                            </label>
                        </div>
                    </div>

                    <div className="col-4">
                        <div className="form-check">
                            <input
                                type="checkbox"
                                value="Vacant Land"
                                className="form-check-input"
                                onChange={props.multiplePropType}
                                id="exampleCheck20"
                            />
                            <label className="form-check-label" htmlFor="exampleCheck20">
                                Vacant Land
                            </label>
                        </div>
                    </div>
                    <div className="col-12">
                        <hr />
                    </div>
                    <div className="col-12">
                        <label>
                            <b>Features</b>
                        </label>
                    </div>
                    <div className="col-4">
                        <div className="form-check">
                            <input
                                type="checkbox"
                                value=""
                                className="form-check-input"
                                id="feature1"
                            />
                            <label className="form-check-label" htmlFor="feature1">
                                Central A/C
                            </label>
                        </div>
                    </div>
                    <div className="col-4">
                        <div className="form-check">
                            <input
                                type="checkbox"
                                value=""
                                className="form-check-input"
                                id="feature2"
                            />
                            <label className="form-check-label" htmlFor="feature2">
                                Central Vacuum
                            </label>
                        </div>
                    </div>
                    <div className="col-4">
                        <div className="form-check">
                            <input
                                type="checkbox"
                                value=""
                                className="form-check-input"
                                id="feature3"
                            />
                            <label className="form-check-label" htmlFor="feature3">
                                Elevator
                            </label>
                        </div>
                    </div>
                    <div className="col-4">
                        <div className="form-check">
                            <input
                                type="checkbox"
                                value=""
                                className="form-check-input"
                                id="feature4"
                            />
                            <label className="form-check-label" htmlFor="feature4">
                                Family Room
                            </label>
                        </div>
                    </div>
                    <div className="col-4">
                        <div className="form-check">
                            <input
                                type="checkbox"
                                value=""
                                className="form-check-input"
                                id="feature5"
                            />
                            <label className="form-check-label" htmlFor="feature5">
                                Garage
                            </label>
                        </div>
                    </div>
                    <div className="col-4">
                        <div className="form-check">
                            <input
                                type="checkbox"
                                value=""
                                className="form-check-input"
                                id="feature6"
                            />
                            <label className="form-check-label" htmlFor="feature6">
                                Gym
                            </label>
                        </div>
                    </div>
                    <div className="col-4">
                        <div className="form-check">
                            <input
                                type="checkbox"
                                value=""
                                className="form-check-input"
                                id="feature7"
                            />
                            <label className="form-check-label" htmlFor="feature7">
                                Parking
                            </label>
                        </div>
                    </div>
                    <div className="col-4">
                        <div className="form-check">
                            <input
                                type="checkbox"
                                value=""
                                className="form-check-input"
                                id="feature8"
                            />
                            <label className="form-check-label" htmlFor="feature8">
                                Pool
                            </label>
                        </div>
                    </div>
                    <div className="col-12">
                        <hr />
                    </div>
                    <div className="col-12">
                        <label>
                            <b>Basement</b>
                        </label>
                    </div>
                    {/* {basement.map((item, i) => {    
            let id = "basement" + i;
            return (
              <div className="col-4" key={i}>
                <div className="form-check">
                  <input
                    type="checkbox"
                    className="form-check-input"
                    id={id}
                    value={item}
                    onChange={props.basement}
                  />
                  <label className="form-check-label" htmlFor={id}>
                    {item}
                  </label>
                </div>
              </div>
            );
          })} */}
                </div>
            </div>
        </div>
    );
};
export default MapHeader;
