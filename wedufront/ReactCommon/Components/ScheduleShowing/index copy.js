import React, { useState, useEffect, useRef } from 'react'
import ReactCarousel from './../ReactCarousel';
import ScheduleBookForm from "./../../../constants/Forms/ScheduleBookForm";
import utilityGlobal from "./../../utility/utilityGlobal";
import API from "../../utility/api";
import { slotsApi } from './../../../constants/GlobalConstants'

import crossIcons from './../../../public/images/icons/cros.svg'
const Schedule = (props) => {
	let prop = props.props;
	// console.log('detail schedule=====>>', prop.userDetails.id);
	// console.log("setTimeSlots preDefTime->>",preDefTime[e.target.dataset.id-1]);
	let state = {
		formConfig: ScheduleBookForm.ScheduleForm,
		validateField: ScheduleBookForm.validateFields.ScheduleForm,
		showBtn: false,
	};
	const [closePopup, setClosePopup] = useState(prop.bookAShowing);
	const [activeTimeSlote, setActiveTimeSlote] = useState(0);
	const [isSlotsSelected, setSlotSelectState] = useState("");
	const [slotsSelected, setIsSlotsSelected] = useState({});
	const [timeSelected, setTimeSelected] = useState("");
	const [validateMessage, setValidateMessage] = useState(false);
	const [showFroms, setShowFrom] = useState(false);
	const [availableSlots, setAvailableSlots] = useState([]);


	const [dateData, setDateData] = useState();
	const [timeSlots, setTimeSlots] = useState();
	const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
	const days = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
	const preDefTime = [
		{
			start: "10:00 AM",
			end: "11:00 AM",
		},
		{
			start: "11:00 AM",
			end: "12:00 PM",
		},
		{
			start: "12:00 PM",
			end: "13:00 PM",
		},
		{
			start: "13:00 PM",
			end: "14:00 PM",
		},
		{
			start: "14:00 PM",
			end: "15:00 PM",
		},
		{
			start: "15:00 PM",
			end: "16:00 PM",
		},
		{
			start: "16:00 PM",
			end: "17:00 PM",
		},
		{
			start: "17:00 PM",
			end: "18:00 PM",
		},
	];
	const dateSlots = Array();
	useEffect(() => {
		setClosePopup(prop.bookAShowing);
		setIsSlotsSelected(dateSlots[0]);
		setShowFrom(false)
		setTimeSelected("");
		setSlotSelectState("");
		setActiveTimeSlote(0);
		getSlots();
	}, [prop.bookAShowing]);
	function addDayToCurrentDate(days) {
		let currentDate = new Date()
		return new Date(currentDate.setDate(currentDate.getDate() + days))
	}
	function getSlots(now) {
		if (!now) {
			const today = addDayToCurrentDate(0);
			const year = today.getFullYear();
			const month = today.getMonth();
			const day = String(today.getDate()).padStart(2, '0');

			now = day + "-" + String(month + 1).padStart(2, '0') + "-" + year
		}
		console.log("getSlots=====>>>>", now);
		let data = {
			"Date": now
		}
		API.jsonApiCall(slotsApi, data, "POST", null, {
			"Content-Type": "application/json",
		})
			.then((res) => {
				setAvailableSlots(res);
				console.log("slotsApi=====>>>>", res);
			})
			.catch((e) => {

			});
	}
	function daysInThisMonth(now) {
		return new Date(now.getFullYear(), now.getMonth() + 1, 0).getDate();
	}
	function setdates() {
		const currentDate = new Date()
		const currentMonth = currentDate.getMonth();
		const tDays = 366;
		const totalDays = daysInThisMonth(currentDate);
		if (totalDays) {
			for (let index = 0; index <= tDays; index++) {
				const today = addDayToCurrentDate(index);
				const year = today.getFullYear();
				const dayNames = today.getDay();
				const month = today.getMonth();
				const day = String(today.getDate()).padStart(2, '0');
				const dayName = days[dayNames];
				const months = monthNames[month];
				let obj = {
					"day": day,
					"dayName": dayName,
					"month": months,
					"year": year,
					"monthNum": String(month + 1).padStart(2, '0'),
				}
				dateSlots.push(obj);
			}
		}
	}
	function handleChange(e) {
        let data = {};
        let inpval=e.target.value;
        if (e.target.name === "phone") {
            const { value, maxLength } = e.target;
            var x = e.target.value.replace(/\D/g, '').match(/(\d{0,3})(\d{0,3})(\d{0,4})/);
            if (String(value).length >= maxLength) {
                e.preventDefault();
                return;
            }
            if (String(value).length === 10) {
                let formated= '(' + x[1] + ') ' + x[2] + ' ' + x[3];
                inpval =formated;
            }
        }
        data[e.target.name] = inpval;
        this.setState(data, () => {
            console.log("in forms",utilityGlobal.validateData(this.state.validateField, this).status);
            this.setState({
                showBtn: utilityGlobal.validateData(this.state.validateField, this).status,
            });
        });
    }
	function selectedSlot(item, index) {
		let date = item.day + "-" + item.monthNum + "-" + item.year
		getSlots(date);
		setDateData(date);
		console.log("selectedSlot===>> ", date);
		setValidateMessage(false);
		setIsSlotsSelected(item);
		setSlotSelectState(index);
	}
	function selectedTime(e) {
		// console.log("setTimeSlots preDefTime->>",preDefTime[e.target.dataset.id-1]);
		setTimeSlots(preDefTime[e.target.dataset.id - 1]);
		setValidateMessage(false);
		setIsSlotsSelected(dateSlots[0]);
		setTimeSelected(e.target.dataset.set);
		setActiveTimeSlote(e.target.dataset.id);
	}
	function showFrom() {
		if (slotsSelected.month && timeSelected) {
			setShowFrom(true)
		}
		else {
			setValidateMessage(true)
			setShowFrom(false)

		}
	}
	function back() {
		setShowFrom(false)
	}
	function submitFormDate(e) {
		alert("on working change api path");
		return
	}
	const renderSlots = (e) => {
		setdates();
		if (dateSlots && Array.isArray(dateSlots) && dateSlots.length > 0
		) {
			const renderList = dateSlots.map((item, index) => {
				return (
					<>
						<div className="date_block " key={index} htmlFor="selectSlots" date-set={index} >
							<span className="days" date-set={index}>{item.dayName}</span>
							<div className={`digit  ${isSlotsSelected == index ? "slotsActive" : ""}`} id={'isSlotsSelected' + index} onClick={() => selectedSlot(item, index)} data-set={index} data-value={item.dayName + " " + item.day + " " + item.month}>
								<p date-set={index}>{item.day}</p>
							</div>
							<span className="months">{item.month}</span>
						</div>
					</>
				);
			});
			return renderList;
		}
	};
	return (
		<div className={`scheduleTime ${closePopup ? "d-block" : "d-none"}`}>
			<img {...crossIcons} id="closeBtn" onClick={props.bookshow} />
			<div className="modal__container">
				<p onClick={back} className={`backBtnCls  ${showFroms ? "d-block" : "d-none"}`}>Back</p>
				<div className={` ${showFroms ? "d-none" : "d-block"}`}>
					<h2 className="modal__title">Book A Showing</h2>
					<p className="selectTitle">Select a Date and Time</p>
					<div className="placeholder ">
						<div className="container">
							<div className="date_slots">
								<ReactCarousel show={3}>
									{
										renderSlots()
									}
								</ReactCarousel>
							</div>
							<div className="time-slots flex">
								<label className={`${availableSlots.includes('10:00 AM') ? "" : "hiddenSlots"}`}> <button data-id="1" type="button" data-set={"10AM - 11AM"} className={`time-slot ${activeTimeSlote == 1 ? "slotsActive" : ""}`} onClick={selectedTime} >10AM - 11AM</button></label>
								&nbsp;<label className={`${availableSlots.includes('11:00 AM') ? "" : "hiddenSlots"}`}><button data-id="2" type="button" data-set="11AM - 12PM" className={`time-slot   ${activeTimeSlote == 2 ? "slotsActive" : ""}`} onClick={selectedTime} >11AM - 12PM</button> </label>
								&nbsp;<label className={`${availableSlots.includes('12:00 PM') ? "" : "hiddenSlots"}`}> <button data-id="3" type="button" data-set="12PM - 1PM" className={`time-slot   ${activeTimeSlote == 3 ? "slotsActive" : ""}`} onClick={selectedTime} >12PM - 1PM</button></label>
								&nbsp;<label className={`${availableSlots.includes('13:00 PM') ? "" : "hiddenSlots"}`}> <button data-id="4" type="button" data-set="1PM - 2PM" className={`time-slot   ${activeTimeSlote == 4 ? "slotsActive" : ""}`} onClick={selectedTime} >1PM - 2PM</button></label>
							</div>
							<div className="time-slots flex">
								<label className={`${availableSlots.includes('14:00 PM') ? "" : "hiddenSlots"}`}>        <button data-id="5" type="button" data-set="2PM - 3PM" className={`time-slot ${activeTimeSlote == 5 ? "slotsActive" : ""}`} onClick={selectedTime} >2PM - 3PM</button></label>
								&nbsp;	<label className={`${availableSlots.includes('15:00 PM') ? "" : "hiddenSlots"}`}> <button data-id="6" type="button" data-set="3PM - 4PM" className={`time-slot ${activeTimeSlote == 6 ? "slotsActive" : ""}`} onClick={selectedTime} >3PM - 4PM</button> </label>
								&nbsp;<label className={`${availableSlots.includes('16:00 PM') ? "" : "hiddenSlots"}`}>  <button data-id="7" type="button" data-set="4PM - 5PM" className={`time-slot ${activeTimeSlote == 7 ? "slotsActive" : ""}`} onClick={selectedTime} >4PM - 5PM</button></label>
								&nbsp;<label className={`${availableSlots.includes('17:00 PM') ? "" : "hiddenSlots"}`}>  <button data-id="8" type="button" data-set="5PM - 6PM" className={`time-slot ${activeTimeSlote == 8 ? "slotsActive" : ""}`} onClick={selectedTime} >5PM - 6PM</button></label>
							</div>
							<div className="mt-0 row">
								<div className="col-md-2">
								</div>
								<div className="col-md-8">
									{validateMessage && <p className="validateMsg">A date and time must be selected to schedule a showing.</p>}
									<button name="showSchedule" type="button" className="btn showSchedule mt-3" id="scheduleShowing" onClick={showFrom}>Schedule Showing</button>
								</div>
								<div className="col-md-2">
								</div>
								<p className="mt-3">Viewings are free and there's no obligation.</p>
							</div>
						</div>
					</div>
				</div>
				<div className={` ${showFroms ? "d-block" : "d-none"}`}>
					<div className="container">
						<h1 className="modal__title">Confirm Your Appointment</h1>
						<p className="selectTitle mt-0">We'll never share or sell your information</p>
						<div className="placeholderForm mt-2 ">
							<p className="">{slotsSelected.month + " , " + slotsSelected.day + " , " + slotsSelected.year}</p>
							<p className="">{timeSelected}</p>
							<p className="">{prop.detail ? prop.detail.Addr : ""}</p>
						</div>
						<form id="bookScheduleForm" className="bookScheduleForm">
							{utilityGlobal.renderConfig(state.formConfig, state, "")}
						</form>
						<br />
					</div>
				</div>
			</div>
		</div>
	);
};
export default Schedule;