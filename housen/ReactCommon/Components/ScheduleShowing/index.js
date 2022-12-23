import React, { useState, useEffect, useRef } from "react";
import ReactCarousel from "./../ReactCarousel";
import ScheduleBookForm from "./../../../constants/Forms/ScheduleBookForm";
import utilityGlobal from "./../../utility/utilityGlobal";
import API from "../../utility/api";
import { ToastContainer, toast } from "react-toastify";
import { slotsApi, saveEventApi, agentId } from "./../../../constants/Global";
import crossIcons from "./../../../public/images/icon/cros.svg";
const Schedule = (props) => {
  let prop = props.props;
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
  const [nameMsg, setNameMsg] = useState(false);
  const [emailMsg, setEmailMsg] = useState(false);
  const [phoneMsg, setPhoneMsg] = useState(false);
  const [submitted, setSubmitted] = useState(false);

  const [hideBtn, setHide] = useState(true);
  const [Phone, setPhone] = useState("");
  const [Name, setName] = useState("");
  const [Email, setEmail] = useState("");
  const [Description, setDescription] = useState("");
  let phone = "";
  const [dateData, setDateData] = useState();
  const [timeSlots, setTimeSlots] = useState();
  const [errorMsg, seterrorMsg] = useState("");
  const monthNames = [
    "January",
    "February",
    "March",
    "April",
    "May",
    "June",
    "July",
    "August",
    "September",
    "October",
    "November",
    "December",
  ];
  const days = [
    "Sunday",
    "Monday",
    "Tuesday",
    "Wednesday",
    "Thursday",
    "Friday",
    "Saturday",
  ];
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
    setShowFrom(false);
    setTimeSelected("");
    setSlotSelectState("");
    setActiveTimeSlote(0);
    getSlots();
  }, [prop.bookAShowing, submitted]);
  function addDayToCurrentDate(days) {
    let currentDate = new Date();
    return new Date(currentDate.setDate(currentDate.getDate() + days));
  }
  function getSlots(now) {
    if (!now) {
      const today = addDayToCurrentDate(0);
      const year = today.getFullYear();
      const month = today.getMonth();
      const day = String(today.getDate()).padStart(2, "0");
      now = day + "-" + String(month + 1).padStart(2, "0") + "-" + year;
    }
    // console.log("getSlots=====>>>>", now);
    setDateData(now);
    let data = {
      Date: now,
    };
    API.jsonApiCall(slotsApi, data, "POST", null, {
      "Content-Type": "application/json",
    })
      .then((res) => {
        if (!res.error) {
          setAvailableSlots(res);
          // console.log("slotsApi=====>>>>", res);
        }
      })
      .catch((e) => {});
  }
  function daysInThisMonth(now) {
    return new Date(now.getFullYear(), now.getMonth() + 1, 0).getDate();
  }
  function setdates() {
    const currentDate = new Date();
    const currentMonth = currentDate.getMonth();
    const tDays = 366;
    const totalDays = daysInThisMonth(currentDate);
    if (totalDays) {
      for (let index = 0; index <= tDays; index++) {
        const today = addDayToCurrentDate(index);
        const year = today.getFullYear();
        const dayNames = today.getDay();
        const month = today.getMonth();
        const day = String(today.getDate()).padStart(2, "0");
        const dayName = days[dayNames];
        const months = monthNames[month];
        let obj = {
          day: day,
          dayName: dayName,
          month: months,
          year: year,
          monthNum: String(month + 1).padStart(2, "0"),
        };
        dateSlots.push(obj);
      }
    }
  }
  function selectedSlot(item, index) {
    let date = item.day + "-" + item.monthNum + "-" + item.year;
    getSlots(date);
    setDateData(date);
    console.log("selectedSlot===>> ", date);
    setValidateMessage(false);
    setIsSlotsSelected(item);
    setSlotSelectState(index);
  }
  function selectedTime(e) {
    console.log(
      "setTimeSlots preDefTime->>",
      preDefTime[e.target.dataset.id - 1]
    );
    setTimeSlots(preDefTime[e.target.dataset.id - 1]);
    setValidateMessage(false);
    setIsSlotsSelected(dateSlots[0]);
    setTimeSelected(e.target.dataset.set);
    setActiveTimeSlote(e.target.dataset.id);
  }
  function showFrom() {
    if (slotsSelected.month && timeSelected) {
      setShowFrom(true);
    } else {
      setValidateMessage(true);
      setShowFrom(false);
    }
  }
  function handleChangeForSchdule(e) {
    seterrorMsg("");
    if (!nameMsg) {
      setNameMsg(false);
    }
    if (!emailMsg) {
      setEmailMsg(false);
    }
    if (!phoneMsg) {
      setPhoneMsg(false);
    }

    let data = {};
    let inpval = e.target.value;
    if (e.target.name === "user_phone") {
      var x = e.target.value
        .replace(/\D/g, "")
        .match(/(\d{0,3})(\d{0,3})(\d{0,4})/);
      e.target.value = !x[2]
        ? x[1]
        : "(" + x[1] + ") " + x[2] + (x[3] ? "-" + x[3] : "");
      setPhone(e.target.value);
    }
    if (e.target.name === "user_name") {
      if (!inpval) {
        setNameMsg(true);
        return;
      } else {
        setName(inpval);
        setNameMsg(false);
      }
    }
    if (e.target.name === "user_email") {
      if (!validateEmail(inpval) || !inpval) {
        setEmailMsg(true);
        return;
      } else {
        setEmail(inpval);
        setEmailMsg(false);
      }
    }
    if (e.target.name === "user_message") {
      setDescription(inpval);
    }
    if (!nameMsg && !emailMsg && !phoneMsg) {
      setHide(false);
    } else {
      setHide(true);
    }
  }

  function back() {
    setShowFrom(false);
  }
  function validateEmail(e) {
    return /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(
      e
    );
  }
  function submitFormDate(e) {
    // Url - http://3.144.136.139/api/v1/agent/events/addSlots
    //

    let data = {
      Date: dateData,
      Description: Description,
      StartTime: timeSlots.start,
      EndTime: timeSlots.end,
      Name: Name,
      Phone: Phone,
      Email: Email,
      AgentId: agentId,
      property_url: window.location.href,
    };
    // console.log("====>>>>dateData", data);
    if (Name === "" || Phone === "" || Email === "" || Description === "") {
      seterrorMsg("All fields are required");
      return false;
    }
    seterrorMsg("");
    API.jsonApiCall(saveEventApi, data, "POST", null, {
      "Content-Type": "application/json",
    })
      .then((res) => {
        console.log("saveEventApi=====>>>>", res);
        if (res.status == 200) {
          props.bookshow();
          toast.success(res.success);
          document.getElementById("bookScheduleForm").reset();
          setHide(true);
          setSubmitted(false);
          setPhone("");
          setName("");
          setEmail("");
          setDescription("");
        }
      })
      .catch((e) => {});

    return;
  }
  const renderSlots = (e) => {
    setdates();
    if (dateSlots && Array.isArray(dateSlots) && dateSlots.length > 0) {
      const renderList = dateSlots.map((item, index) => {
        return (
          <>
            <div
              className="date_block "
              key={index}
              htmlFor="selectSlots"
              date-set={index}
            >
              <span className="days" date-set={index}>
                {item.dayName}
              </span>
              <div
                className={`digit  ${
                  isSlotsSelected == index ? "slotsActive" : ""
                }`}
                id={"isSlotsSelected" + index}
                onClick={() => selectedSlot(item, index)}
                data-set={index}
                data-value={item.dayName + " " + item.day + " " + item.month}
              >
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
        <p
          onClick={back}
          className={`backBtnCls  ${showFroms ? "d-block" : "d-none"}`}
        >
          Back
        </p>
        <div className={` ${showFroms ? "d-none" : "d-block"}`}>
          <h2 className="modal__title">Book A Showing</h2>
          <p className="selectTitle">Select a Date and Time</p>
          <div className="placeholder ">
            <div className="container">
              <div className="date_slots">
                <ReactCarousel show={3}>{renderSlots()}</ReactCarousel>
              </div>
              <div id="desktop" className="time-slots flex ">
                <label
                  className={`${
                    availableSlots.includes("10:00 AM") ? "" : "hiddenSlots"
                  } desktpSlots`}
                >
                  {" "}
                  <button
                    data-id="1"
                    type="button"
                    data-set={"10AM - 11AM"}
                    className={`time-slot ${
                      activeTimeSlote == 1 ? "slotsActive" : ""
                    }`}
                    onClick={selectedTime}
                  >
                    10AM - 11AM
                  </button>
                </label>
                &nbsp;
                <label
                  className={`${
                    availableSlots.includes("11:00 AM") ? "" : "hiddenSlots"
                  } desktpSlots `}
                >
                  <button
                    data-id="2"
                    type="button"
                    data-set="11AM - 12PM"
                    className={`time-slot   ${
                      activeTimeSlote == 2 ? "slotsActive" : ""
                    }`}
                    onClick={selectedTime}
                  >
                    11AM - 12PM
                  </button>{" "}
                </label>
                &nbsp;
                <label
                  className={`${
                    availableSlots.includes("12:00 PM") ? "" : "hiddenSlots"
                  } desktpSlots `}
                >
                  {" "}
                  <button
                    data-id="3"
                    type="button"
                    data-set="12PM - 1PM"
                    className={`time-slot   ${
                      activeTimeSlote == 3 ? "slotsActive" : ""
                    }`}
                    onClick={selectedTime}
                  >
                    12PM - 1PM
                  </button>
                </label>
                &nbsp;
                <label
                  className={`${
                    availableSlots.includes("13:00 PM") ? "" : "hiddenSlots"
                  } desktpSlots `}
                >
                  {" "}
                  <button
                    data-id="4"
                    type="button"
                    data-set="1PM - 2PM"
                    className={`time-slot   ${
                      activeTimeSlote == 4 ? "slotsActive" : ""
                    }`}
                    onClick={selectedTime}
                  >
                    1PM - 2PM
                  </button>
                </label>
              </div>
              <div id="desktop" className="time-slots flex ">
                <label
                  className={`${
                    availableSlots.includes("14:00 PM") ? "" : "hiddenSlots"
                  } desktpSlots `}
                >
                  {" "}
                  <button
                    data-id="5"
                    type="button"
                    data-set="2PM - 3PM"
                    className={`time-slot ${
                      activeTimeSlote == 5 ? "slotsActive" : ""
                    }`}
                    onClick={selectedTime}
                  >
                    2PM - 3PM
                  </button>
                </label>
                &nbsp;{" "}
                <label
                  className={`${
                    availableSlots.includes("15:00 PM") ? "" : "hiddenSlots"
                  } desktpSlots `}
                >
                  {" "}
                  <button
                    data-id="6"
                    type="button"
                    data-set="3PM - 4PM"
                    className={`time-slot ${
                      activeTimeSlote == 6 ? "slotsActive" : ""
                    }`}
                    onClick={selectedTime}
                  >
                    3PM - 4PM
                  </button>{" "}
                </label>
                &nbsp;
                <label
                  className={`${
                    availableSlots.includes("16:00 PM") ? "" : "hiddenSlots"
                  } desktpSlots `}
                >
                  {" "}
                  <button
                    data-id="7"
                    type="button"
                    data-set="4PM - 5PM"
                    className={`time-slot ${
                      activeTimeSlote == 7 ? "slotsActive" : ""
                    }`}
                    onClick={selectedTime}
                  >
                    4PM - 5PM
                  </button>
                </label>
                &nbsp;
                <label
                  className={`${
                    availableSlots.includes("17:00 PM") ? "" : "hiddenSlots"
                  } desktpSlots `}
                >
                  {" "}
                  <button
                    data-id="8"
                    type="button"
                    data-set="5PM - 6PM"
                    className={`time-slot ${
                      activeTimeSlote == 8 ? "slotsActive" : ""
                    }`}
                    onClick={selectedTime}
                  >
                    5PM - 6PM
                  </button>
                </label>
              </div>
              <div id="mobile-view" className="time-slots flex ">
                <label
                  className={`${
                    availableSlots.includes("10:00 AM") ? "" : "hiddenSlots"
                  }`}
                >
                  {" "}
                  <button
                    data-id="1"
                    type="button"
                    data-set={"10AM - 11AM"}
                    className={`time-slot ${
                      activeTimeSlote == 1 ? "slotsActive" : ""
                    }`}
                    onClick={selectedTime}
                  >
                    10AM - 11AM
                  </button>
                </label>
                <label
                  className={`${
                    availableSlots.includes("11:00 AM") ? "" : "hiddenSlots"
                  }`}
                >
                  <button
                    data-id="2"
                    type="button"
                    data-set="11AM - 12PM"
                    className={`time-slot   ${
                      activeTimeSlote == 2 ? "slotsActive" : ""
                    }`}
                    onClick={selectedTime}
                  >
                    11AM - 12PM
                  </button>{" "}
                </label>
              </div>
              <div id="mobile-view" className="time-slots flex  ">
                <label
                  className={`${
                    availableSlots.includes("12:00 PM") ? "" : "hiddenSlots"
                  }`}
                >
                  {" "}
                  <button
                    data-id="3"
                    type="button"
                    data-set="12PM - 1PM"
                    className={`time-slot   ${
                      activeTimeSlote == 3 ? "slotsActive" : ""
                    }`}
                    onClick={selectedTime}
                  >
                    12PM - 1PM
                  </button>
                </label>
                <label
                  className={`${
                    availableSlots.includes("13:00 PM") ? "" : "hiddenSlots"
                  }`}
                >
                  {" "}
                  <button
                    data-id="4"
                    type="button"
                    data-set="1PM - 2PM"
                    className={`time-slot   ${
                      activeTimeSlote == 4 ? "slotsActive" : ""
                    }`}
                    onClick={selectedTime}
                  >
                    1PM - 2PM
                  </button>
                </label>
              </div>
              <div id="mobile-view" className="time-slots flex  ">
                <label
                  className={`${
                    availableSlots.includes("14:00 PM") ? "" : "hiddenSlots"
                  }`}
                >
                  {" "}
                  <button
                    data-id="5"
                    type="button"
                    data-set="2PM - 3PM"
                    className={`time-slot ${
                      activeTimeSlote == 5 ? "slotsActive" : ""
                    }`}
                    onClick={selectedTime}
                  >
                    2PM - 3PM
                  </button>
                </label>
                <label
                  className={`${
                    availableSlots.includes("15:00 PM") ? "" : "hiddenSlots"
                  }`}
                >
                  {" "}
                  <button
                    data-id="6"
                    type="button"
                    data-set="3PM - 4PM"
                    className={`time-slot ${
                      activeTimeSlote == 6 ? "slotsActive" : ""
                    }`}
                    onClick={selectedTime}
                  >
                    3PM - 4PM
                  </button>{" "}
                </label>
              </div>
              <div id="mobile-view" className="time-slots flex  ">
                <label
                  className={`${
                    availableSlots.includes("16:00 PM") ? "" : "hiddenSlots"
                  }`}
                >
                  {" "}
                  <button
                    data-id="7"
                    type="button"
                    data-set="4PM - 5PM"
                    className={`time-slot ${
                      activeTimeSlote == 7 ? "slotsActive" : ""
                    }`}
                    onClick={selectedTime}
                  >
                    4PM - 5PM
                  </button>
                </label>
                <label
                  className={`${
                    availableSlots.includes("17:00 PM") ? "" : "hiddenSlots"
                  }`}
                >
                  {" "}
                  <button
                    data-id="8"
                    type="button"
                    data-set="5PM - 6PM"
                    className={`time-slot ${
                      activeTimeSlote == 8 ? "slotsActive" : ""
                    }`}
                    onClick={selectedTime}
                  >
                    5PM - 6PM
                  </button>
                </label>
              </div>
              <div className="mt-0 row">
                <div className="col-md-2"></div>
                <div className="col-md-8">
                  {validateMessage && (
                    <p className="validateMsg">
                      A date and time must be selected to schedule a showing.
                    </p>
                  )}
                  <button
                    name="showSchedule"
                    type="button"
                    className="btn showSchedule mt-3"
                    id="scheduleShowing"
                    onClick={showFrom}
                  >
                    Schedule Showing
                  </button>
                </div>
                <div className="col-md-2"></div>
                <p className="mt-3">
                  Viewings are free and there's no obligation.
                </p>
              </div>
            </div>
          </div>
        </div>
        <div className={` ${showFroms ? "d-block" : "d-none"}`}>
          <div className="container">
            <h1 className="modal__title">Confirm Your Appointment</h1>
            <p className="selectTitle mt-0">
              We'll never share or sell your information
            </p>
            <div className="placeholderForm mt-2 ">
              <p className="">
                {slotsSelected.month +
                  " , " +
                  slotsSelected.day +
                  " , " +
                  slotsSelected.year}
              </p>
              <p className="">{timeSelected}</p>
              <p className="">{prop.detail ? prop.detail.Addr : ""}</p>
            </div>
            <div className="container">
              <form id="bookScheduleForm" className="bookScheduleForm">
                <div className="mt-2 font-normal inputs-areas ">
                  <label className="">Full Name</label>
                  <input
                    type="text"
                    onChange={handleChangeForSchdule}
                    name="user_name"
                    placeholder=""
                    className="form-control"
                    autoComplete="off"
                    id="user_name"
                    data-inp-error-msg="Name name is required"
                    data-inp-validation="required"
                    data-gsf-name="user_name"
                  />
                  {nameMsg && (
                    <span className="err-inp-msg">Name is required</span>
                  )}
                </div>
                <div className=" font-normal inputs-areas ">
                  <label className="">Email Address</label>
                  <input
                    type="text"
                    onChange={handleChangeForSchdule}
                    name="user_email"
                    placeholder=""
                    className="form-control"
                    autoComplete="off"
                    id="user_email"
                    data-inp-error-msg="Input valid email id"
                    data-inp-validation="required,email"
                    data-gsf-name="user_email"
                  />
                  {emailMsg && (
                    <span className="err-inp-msg">Invalid email id</span>
                  )}
                </div>
                <div className=" font-normal inputs-areas ">
                  <label className="">Mobile Number</label>
                  <input
                    type="text"
                    onChange={handleChangeForSchdule}
                    name="user_phone"
                    placeholder=""
                    className="form-control"
                    autoComplete="off"
                    id="user_phone"
                    data-inp-error-msg="Mobile Number is required"
                    data-inp-validation="required"
                    data-gsf-name="user_phone"
                  />
                  {phoneMsg && (
                    <span className="err-inp-msg">
                      Phone number is required
                    </span>
                  )}
                </div>
                <div className=" font-normal inputs-areas ">
                  <label className="">Additional Notes (optional)</label>
                  <input
                    type="text"
                    onChange={handleChangeForSchdule}
                    name="user_message"
                    placeholder=""
                    className="form-control"
                    autoComplete="off"
                    id="user_message"
                    data-inp-error-msg=""
                    data-inp-validation=""
                    data-gsf-name="user_message"
                  />
                </div>
                <div className="col-md-6 sendBtnSection ">
                  <span className="err-inp-msg">{errorMsg}</span>
                  <button
                    disabled={hideBtn ? true : false}
                    name="send"
                    onClick={submitFormDate}
                    type="button"
                    id="scheduleBook"
                    className="btn submit-btn btn  addEventBtn"
                  >
                    Submit Request
                  </button>
                </div>
              </form>
              <br />
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};
export default Schedule;