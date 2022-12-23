import { useState, useEffect } from "react";
import { Modal, Form, Button } from 'react-bootstrap'
import Constants from "../../constants/Global";
import Autocomplete from '../../ReactCommon/Components/AutoSuggestion'
import API from "../../ReactCommon/utility/api";
import { ToastContainer, toast } from 'react-toastify';
import Slider from '@material-ui/core/Slider';
import Loader1 from "../loader/loader1.js"
const Preference = (props) => {
	const { isLogin, togglePopUp } = props;
	const [show, setShow] = useState(false);
	const [value, setValue] = useState([0, 5000000]);
	const [loader, setloader] = useState(false);
	const [step,setstep] = useState(50000);
	const rangeSelector = (event, newValue) => {
		if(newValue[0]!==value[0]){
			if(newValue[0]>=0){
				setstep(50000);
			}
			if(newValue[0]>=1000000){
				setstep(100000);
			}
			if(newValue[0]>=1500000){
				setstep(250000);
			}
		}
		if(newValue[1]!==value[1]){
			if(newValue[1]>=0){
				setstep(50000);
			}
			if(newValue[1]>=1000000){
				setstep(100000);
			}
			if(newValue[1]>=1500000){
				setstep(250000);
			}
		}
		setValue(newValue);
		// console.log(newValue)
	};
	let prevHeading = false;

	// let sliderRange = ['500k', '1m', '2m', '3m', '4m','5m', 'Max'];
	let sliderRange = ['300k', '1m', '2m', '3m', '4m','Max'];

	const handleClose = () => setShow(false);
	var formatter = new Intl.NumberFormat("en-US", {
        style: "currency",
        currency: "USD",
        minimumFractionDigits: 0,
    });
	const handleShow = () => {
		if (isLogin) {
			setShow(true);
		}
		else {
			togglePopUp();
		}

	}
	let subtype1 = [
		{ "PropertySubType": "Detached" },
		{ "PropertySubType": "Semi-Detached" },
		{ "PropertySubType": "Freehold Townhouse" },
		{ "PropertySubType": "Condo Townhouse" },
		{ "PropertySubType": "Condo Apt" },
		{ "PropertySubType": "Link" },
		{ "PropertySubType": "Land" }
	]
	// let city1=[
	// 	"GTA - Central"={"City":""},
	// 	]
	let city1 = [

		{
			"isHeading": true,
			"text": "GTA - Central ",
			 
			"City": ""
		},
		{
			"text": "Toronto",
			"City": "Toronto"
		},
		{
			"text": "",
			"City": "North York"
		},
		{
			"text": "",
			"City": "Scarborough"
		},
		{
			"text": "",
			"City": "Etobicoke"
		},
		{
			"isHeading": true,
			"text": "GTA - North",
			"City": ""
		},
		{
			"text": "GTA - North",
			"City": "Markham"
		},
		{
			"text": "",
			"City": "Richmond Hill"
		},
		{
			"text": "",
			"City": "Vaughan"
		},
		{
			"text": "",
			"City": "Whitchurch-Stouffville"
		},
		{
			"text": "",
			"City": "Aurora"
		},
		{
			"text": "",
			"City": "Newmarket"
		},
		{
			"City": "King"
		},
		{
			"text": "",
			"City": "Georgina"
		},
		{
			"text": "",
			"City": "East Gwillimbury"
		},
		{
			"isHeading": true,
			"text": "GTA - East ",
			"City": ""
		},

		{
			"text": "",
			"City": "Ajax"
		},
		{
			"text": "",
			"City": "Clarington"
		},
		{
			"City": "Brock"
		},
		{
			"text": "",
			"City": "Pickering"
		},
		{
			"City": "Whitby"
		},
		{
			"text": "",
			"City": "Oshawa"
		},
		{
			"text": "",
			"City": "Scugog"
		},
		{
			"text": "",
			"City": "Uxbridge"
		},
		{
			"isHeading": true,
			"text": "GTA - West ",
			"City": ""
		},
		{
			"text": "",
			"City": "Brampton"
		},
		{
			"text": "",
			"City": "Mississauga"
		},
		{
			"text": "",
			"City": "Oakville"
		},
		{
			"text": "",
			"City": "Burlington"
		},
		{
			"text": "",
			"City": "Milton"
		},
		{
			"text": "",
			"City": "Halton Hills"
		},
		{
			"text": "",
			"City": "Caledon"
		},
		
		{
			"isHeading": true,
			"text": "Hamilton - Niagara ",
			"City": ""
		},

		{
			"text": "",
			"City": "Hamilton"
		},
		{
			"text": "",
			"City": "Brantford"
		},
		{
			"text": "",
			"City": "St. Catharines"
		},
		{
			"text": "",
			"City": "Niagara Falls"
		},
		{
			"text": "",
			"City": "Welland"
		},
		{
			"text": "",
			"City": "Lincoln"
		},
		{
			"text": "",
			"City": "Thorold"
		},
		{
			"text": "",
			"City": "Norfolk"
		},
		{
			"text": "",
			"City": "Fort Erie"
		},
		{
			"text": "",
			"City": "Grimsby"
		},
		{
			"isHeading": true,
			"text": "Central Ontario ",
			"City": ""
		},
		{
			"text": "",
			"City": "Barrie"
		},
		{
			"text": "",
			"City": "Innisfil"
		},
		{
			"text": "",
			"City": "Bradford West Gwillimbury"
		},
		{
			"text": "",
			"City": "New Tecumseth"
		},
		{
			"text": "",
			"City": "Collingwood"
		},
		{
			"isHeading": true,
			"text": "Southwestern Ontario ",
			"City": ""
		},
		{
			"text": "",
			"City": "Guelph"
		},
		{
			"text": "",
			"City": "Cambridge"
		},
		{
			"text": "",
			"City": "Kitchener"
		},
		{
			"text": "",
			"City": "Waterloo"
		},
		{
			"text": "",
			"City": "London"
		}
		,
		{
			"text": "",
			"City": "Woodstock"
		},
		{
			"isHeading": true,
			"text": "Eastern Ontario ",
			"City": ""
		},

		{
			"text": "",
			"City": "Kawartha Lakes"
		},
		{
			"text": "",
			"City": "Peterborough"
		},
		{
			"text": "",
			"City": "Belleville"
		},
		{
			"isHeading": true,
			"text": "Ottawa Area ",
			"City": ""
		},
		{
			"text": "",
			"City": "Ottawa"
		},
		{
			"text": "",
			"City": "Kanata"
		},
		{
			"text": "",
			"City": "Nepean"
		},
		{
			"text": "",
			"City": "Orleans-Gloucester"
		},
		{
			"text": "",
			"City": "Stittsville-Goulbourn"
		},
		{
			"text": "",
			"City": "Clarance-Rockland"
		},
		{
			"text": "",
			"City": "Rideau"
		},
		{
			"text": "",
			"City": "Russell"
		},
	]
	let cityLabel1 = [
		"GTA - Central",
		"GTA - North",
		"GTA - East",
		"Ottawa Area",
		"Hamilton - Niagara",
		"Central Ontario",
		"Southwestern Ontario",
		"Eastern Ontario"
	]

	const [subtype, setSubtype] = useState(subtype1);
	const [city, setCity] = useState(city1);
	const [cityLabel, setcityLabel] = useState(cityLabel1);

	// console.log("citydata", city[0]['GTA - Central']);
	const [preMinPrice, setPreMinPrice] = useState("");
	const [preMaxPrice, setPreMaxPrice] = useState("");
	const [preCity, setPreCity] = useState([]);
	const [preType, setPreType] = useState([]);

	useEffect(() => {
		const getFilters = async () => {
			const filters = await API.jsonApiCall(Constants.base_url + "api/v1/services/GetPreferenceData",
				'', "POST", {}
			);
			if (filters.PropertySubType) {
				// setSubtype(filters.PropertySubType);
				// console.log("ApiKey",filters.PropertySubType);
				// setCity(filters.City);
				// console.log("ApiKey",filters.City);
			}

		}
		getFilters();

		let body = {
			AgentId: Constants.agentId, housenProject: 1,
			userId: props.userId, action: 'get'
		}
		const GetPreference = async () => {
			const preference = await API.jsonApiCall(Constants.base_url + "api/v1/services/AddUserPreference",
				body, "POST", {}
			);
			let lowPrice = 0;
			let highPrice = 5000000;
			if (preference.minPrice) {
				lowPrice=preference.minPrice;
			}
			if(preference.maxPrice){
				highPrice=preference.maxPrice;
			}
			setValue([lowPrice, highPrice]);

			if (preference.city) {
				setPreCity(preference.city);
			}
			if (preference.preportySubType) {
				setPreType(preference.preportySubType);
			}
		}
		
		if (props.userId !== '') {
			GetPreference();
		}


	}, [props.userId]);

	function checkAll(e) {
		let val = e.target.value;
		if (val === "allPropertyType") {
			let el = document.getElementsByClassName('propertyType');
			if (e.target.checked === true) {
				for (var i = 0; i < el.length; i++) {
					el[i].checked = true;
				}
			}
			else {
				for (var i = 0; i < el.length; i++) {
					el[i].checked = false;
				}
			}
		}
		if (val === "allCity") {
			let el = document.getElementsByClassName('city');
			if (e.target.checked === true) {
				for (var i = 0; i < el.length; i++) {
					el[i].checked = true;
				}
			}
			else {
				for (var i = 0; i < el.length; i++) {
					el[i].checked = false;
				}
			}
		}

	}
	function savePreference(e) {
		setloader(true);
		e.preventDefault();
		// if(e.target.price_min)
		let minimumPrice = value[0];
		let maximumPrice = value[1];
		// let checkMIn = Constants.minPriceConstant;
		// let checkMax = Constants.maxPrice;
		// if(e.target.price_min.value!=="No min" && e.target.price_min.value)
		// {
		// 	for (var i = 0; i < checkMIn.length; i++) {
		// 		if(checkMIn[i].text==e.target.price_min.value){
		// 			minimumPrice=checkMIn[i].value;
		// 		}
		// 	}

		// }
		// if(e.target.price_max.value!=="No max" && e.target.price_max.value)
		// {
		// 	for (var i = 0; i < checkMIn.length; i++) {
		// 		if(checkMIn[i].text==e.target.price_max.value){
		// 			maximumPrice=checkMIn[i].value;
		// 		}
		// 	}
		// }
		let preportysub = document.getElementsByClassName('propertyType');
		let propertySubType = '';
		for (var i = 0; i < preportysub.length; i++) {
			if (preportysub[i].checked == true) {
				propertySubType += preportysub[i].value + ',';
			}
		}
		let city = document.getElementsByClassName('city');
		let cities = '';
		for (var i = 0; i < city.length; i++) {
			if (city[i].checked == true) {
				cities += city[i].value + ',';
			}
		}
		let body = {
			AgentId: Constants.agentId, housenProject: 1,
			minPrice: minimumPrice, maxPrice: maximumPrice, preportySubType: propertySubType,
			city: cities, userId: props.userId, action: 'Add'
		}
		const AddPreference = async () => {
			const preference = await API.jsonApiCall(Constants.base_url + "api/v1/services/AddUserPreference",
				body, "POST", {}
			);
			toast.success(preference.success);
			setPreMinPrice(preference.preference['minPrice']);
			setPreMaxPrice(preference.preference['maxPrice']);
			setPreCity(preference.preference['city']);
			setPreType(preference.preference['preportySubType']);
			props.changeFlag();
			handleClose();
			setloader(false);
		}
		if (props.userId !== null) {
			AddPreference();
		}

	}
	function changeValue(str) {
		let checkMIn = Constants.minPriceConstant;

		for (var i = 0; i < checkMIn.length; i++) {
			if (checkMIn[i].value == str) {
				return checkMIn[i].text;
			}
		}

	}
	function clearFields() {
		setValue([0, 5000000]);
		let el = document.getElementsByClassName('city');
		for (var i = 0; i < el.length; i++) {
			el[i].checked = false;
		}
		let prop = document.getElementsByClassName('propertyType');
		for (var i = 0; i < prop.length; i++) {
			prop[i].checked = false;
		}
	}
	return (
		<>
			<div className="container featuredListing">
				<div className="position-relative">
					<button className="custom-button-transparent float-right round prefrence-btns" onClick={handleShow}><i className="fa fa-cog"></i> Customize Your Listings</button>
				</div>
				{isLogin &&
					<Modal size="lg" show={show} onHide={handleClose}>
						<Modal.Header className="pre_modal" closeButton>
							<Modal.Title>Personalize Your Home Page Listings</Modal.Title>
						</Modal.Header>
						<Modal.Body>
							<Form onSubmit={savePreference} id="preferenceFrom">
								<div className="row">
									<div className="col-md-12 col-lg-12">
										<div style={{
											margin: 'auto',
											display: 'block',
											width: '97%'
										}}>
											{formatter.format(value[0])} - {value[1]>=5000000?'Max':formatter.format(value[1])}
											<Slider
												value={value}
												onChange={rangeSelector}
												valueLabelDisplay="auto"
												min={0}
												max={5000000}
												step={step}
												valueLabelDisplay="off"
											/>
											<div className="price_label">
												{sliderRange.map((item) => {
													return (<span>{item}</span>)
												})}
											</div>
										</div>
									</div>
									<div className="col-md-6" hidden>
										<label>Minimum Price</label>
										<Autocomplete
											inputProps={{
												name: "price_min",
												className: "auto  placeHolderCls form-control auto-suggestion-inp filter-input inp-focus-cls theme-text labelCls",
												placeholder: "Price min",
												title: "Price min",
												readOnly: true,
												id: "price_min"

											}}
											allList={Constants.minPriceConstant}
											// autoCompleteCb={props.autoCompleteSuggestion}
											cb={props.handleTypeHead}
											selectedText={changeValue(preMinPrice)}
											// callBackMap={props.mapCallBack}
											extraProps={{}}
										/>
									</div>
									<div className="col-md-6" hidden>
										<label>Maximum Price</label>
										<Autocomplete
											inputProps={{
												name: "price_max",
												className: "auto  placeHolderCls form-control auto-suggestion-inp filter-input inp-focus-cls theme-text labelCls",
												placeholder: "Price max",
												title: "Price max",
												readOnly: true,
												id: "price_max"
											}}
											allList={Constants.maxPrice}
											// autoCompleteCb={props.autoCompleteSuggestion}
											cb={props.handleTypeHead}
											selectedText={changeValue(preMaxPrice)}
											// callBackMap={props.mapCallBack}
											extraProps={{}}
										/>
									</div>
									<div className="col-md-12 pt-4">
										<p className="preferenceLabel"><i className="fa fa-bars"></i> Property Type


				            				<label><input type="checkbox" className="allCheckBox" value="allPropertyType" onClick={checkAll} /> All</label>
										</p>


										{subtype.map((item, key) => {

											if (item.PropertySubType !== "") {
												if (preType.includes(item.PropertySubType)) {

													return (

														<div className="checkbox-label checkbox-custom-label" key={key}>
															<input type="checkbox" name="propertySubType[]" defaultChecked id={key} className="checkboxState propertyType" value={item.PropertySubType} />
															<span className="checkbox-text">{item.PropertySubType}</span>
														</div>
													)
												}
												else {
													return (

														<div className="checkbox-label checkbox-custom-label" key={key}>
															<input type="checkbox" name="propertySubType[]" id={key} className="checkboxState propertyType" value={item.PropertySubType} />
															<span className="checkbox-text">{item.PropertySubType}</span>
														</div>
													)
												}
											}
										})}


									</div>
									<div className="col-md-12 pt-4">
										<p className="preferenceLabel"><i className="fa fa-bars"></i> City
				            				<label><input type="checkbox" className="allCheckBox" value="allCity" onClick={checkAll} /> All</label>
										</p>

										{/*city.map((item,key) => {
				            				if(item.City!==""){
				            				if(preCity.includes(item.City)){
													return (
														<div className="checkbox-label checkbox-custom-label" key={key}>
															<input type="checkbox" name="propertySubType" defaultChecked id={key} className="checkboxState city" value={item.City}/>
															<span className="checkbox-text">{item.City}</span>
														</div>
													)
												}
												else
												{
													return (
														<div className="checkbox-label checkbox-custom-label" key={key}>
															<input type="checkbox" name="propertySubType" id={key} className="checkboxState city" value={item.City}/>
															<span className="checkbox-text">{item.City}</span>
														</div>
													)
												}
											}
												})*/}
										{city.map((item, key) => {
											let inp = "";
										 
											if (preCity.includes(item.City)) {
												inp = item.City ? <input type="checkbox" name="propertySubType" defaultChecked id={key} className="checkboxState city" value={item.City} /> : "";
											}
											else {
												inp = item.City ? <input type="checkbox" name="propertySubType" id={key} className="checkboxState city" value={item.City} /> : "";

											}
											return (
												<>
													{item.isHeading === true ? <div className={`${key?'mb-1 mt-3':""}`} key={key}>
														<span className="checkbox-text1 gta-heading "> &nbsp; {item.text}</span>
													</div> : <div className="checkbox-label checkbox-custom-label" key={key}>
														{inp}
														<span className="checkbox-text">{item.City}</span>
													</div>}
												</>

											)


										})}




									</div>
									<div className="preferencePageButtons col-md-12 pt-4">
										{loader && <Loader1 />}
										<button type="button" onClick={clearFields} className="custom-button-transparent round text-none"> Clear All</button>
										{/*<button type="button" className="custom-button-transparent round text-none"> Cancel</button>*/}
										<button type="submit" className="custom-button-transparent round text-none saveBtn"> Save</button>
									</div>
								</div>

							</Form>

						</Modal.Body>

					</Modal>
				}

			</div>
		</>
	)
}
export default Preference