import React, { Component } from "react";
// import 'react-toastify/dist/ReactToastify.css';
import { ToastContainer, toast } from 'react-toastify';
import LeadFormJson from "./../../../constants/Forms/LeadsFormConstant";
import ScheduleForm from "./../../../constants/Forms/ScheduleFormConstant";
import utilityGlobal from "./../../utility/utilityGlobal";
import { leadFormApi } from "./../../../constants/Global";
import API from "./../../utility/api";
import Schedule from './../ScheduleShowing';
import Constants from "../../../constants/Global";
// 
import Loader from '../../../components/loader/loader';
import AgentProfile from './../../../public/images/agentInfo/profile.png'

const agentInfo = {
    "name": "Jone Doe",
    "title": "Sales Representative",
    "mobileNumber": "xxx-xxx-x90",
    "profile": "https://agentiwebs.com/assets/assist/propertydetailnew/images/avatar.png"
}
class RightSection extends Component {
    constructor(props) {
        console.log("details==> ", props);
        super(props);
        this.handleChange = this.handleChange.bind(this);
        this.submitForm = this.submitForm.bind(this);

        this.bookAShowing = this.bookAShowing.bind(this);
        this.forceToLogin = this.forceToLogin.bind(this);
        this.setText = this.setText.bind(this);


        this.state = {
            detail: props,
            formConfig: LeadFormJson.leadForm,
            validateField: LeadFormJson.validateFields.leadForm,
            showBtn: false,
            bookAShowing: false,
            userDetails: props.userDetails ? props.userDetails : {},
            dataFlag: false,
            agentInfo: Constants.agentInfo
        };
    }
    componentDidMount() {
        let atr = document.getElementById('message');
    }
    handleChange(e) {
        let data = {};
        let inpval = e.target.value;
        if (e.target.name === "phone") {
            var x = e.target.value.replace(/\D/g, '').match(/(\d{0,3})(\d{0,3})(\d{0,4})/);
            inpval = !x[2] ? x[1] : '(' + x[1] + ') ' + x[2] + (x[3] ? '-' + x[3] : '');
        }
        data[e.target.name] = inpval;
        e.target.value = inpval
        this.setState(data, () => {
            this.setState({
                showBtn: utilityGlobal.validateData(this.state.validateField, this).status,
            });
        });
    }

    submitForm(e) {
        e.preventDefault();
        this.setState({
            dataFlag: true
        });
        this.setState({
            showBtn: false
        });

        const { validateField } = this.state;
        const data = {};
        for (let i = 0; i < validateField.length; i++) {
            data[validateField[i]] = this.state[validateField[i]]
        }
        let msg = document.getElementById('message').innerHTML;
        data.message = msg;
        data.property_url = window.location.href;
        data.property_id = this.props.Ml_num;
        data.user_id = this.state.userDetails.login_user_id ? this.state.userDetails.login_user_id : 1;
        data.page_from = "propertydetails";
        data.agent_id = Constants.agentId;
        data.propertyaddress = this.props.Addr;
        API.jsonApiCall(leadFormApi, data, "POST", null, {
            "Content-Type": "application/json",
        }).then((res) => {
            toast.success("Submit Successfully");
            this.setState({
                showBtn: false,
                dataFlag: false
            });
            e.target.name.value = "";
            e.target.email.value = "";
            e.target.phone.value = "";
            e.target.message.value = "";

        })
            .catch((e) => {
                toast.error("Something went wrong try later!");
                this.setState({
                    dataFlag: false
                });
            });
    }
    forceToLogin() {
        this.props.togglePopUp();
    }
    setText() {
        document.getElementById('message').innerHTML = 'It was a dark and stormy night…';
        // I'd like to buy/sell something similar to: {this.state.detail?this.state.detail.Ml_num:""}, {this.state.detail?this.state.detail.Addr:""}
    }

    bookAShowing(click) {
        this.setState({
            bookAShowing: !this.state.bookAShowing
        });
    }
    render() {
        return (
            <>
                <Schedule props={this.state} bookshow={this.bookAShowing} />
                
                <div className="schedule_section mt-2 " id="contactForm-v2" >
                    <div className="my-form ">
                        <h2 className="contact-agent-title mt-2 mb-2" > Contact Housen.ca Agent </h2>
                        <form id="contactForm" action="" className="sidebar_propform" onSubmit={this.submitForm}>
                            {utilityGlobal.renderConfig(this.state.formConfig, this.state, this)}
                            <div className="mt-1 font-normal inputs-areas form-group"><label className=""></label>
                                {
                                    this.props.Ml_num &&
                                    <textarea className="form-control" id="message" rows="4" value={"I would like more information on: [" + this.props.Ml_num + "], " + this.props.Addr + ", " + this.props.Municipality} cols="50"> </textarea>
                                }
                            </div>
                            <button className="btn  sendBtn sendClass submit-btn " disabled={this.state.showBtn ? false : true} type="submit">Contact Agent</button>
                        </form>
                    </div>
                </div>
                {this.state.dataFlag &&
                    <Loader />
                }
            </>
        )
    }
}
export default RightSection