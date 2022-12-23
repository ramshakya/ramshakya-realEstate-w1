import React, { Component } from 'react';
import { Container, Row, Col } from "react-bootstrap";
import 'react-toastify/dist/ReactToastify.css';
import { ToastContainer, toast } from 'react-toastify';
import FeedBackForm from "./../constants/Forms/FeedBackForm.js";
import utilityGlobal from "../ReactCommon/utility/utilityGlobal";
import Reaptcha from 'reaptcha';
import API from './../ReactCommon/utility/api'
import { feedbackApi } from "../constants/GlobalConstants";
import Link from "next/link";
class Feedback extends Component {
    constructor(props) {
        super(props);
        this.handleChange = this.handleChange.bind(this);
        this.submitForm = this.submitForm.bind(this);
        this.handleComments = this.handleComments.bind(this);
        this.onVerify = this.onVerify.bind(this);
        this.validateFeilds = this.validateFeilds.bind(this);
        this.state = {
            formConfig: FeedBackForm.feedBackform,
            validateField: FeedBackForm.validateFields.feedbackform,
            showBtn: false,
            captcha: false,
            captchaToken: "",
            comments: "",
            commentsErr: false
        };
    }
    onVerify(e) {
        setTimeout(() => {
            this.setState({
                captcha: true,
            });
            this.validateFeilds();
        }, 100);
    }
    handleComments(e) {
        this.setState({ comments: e.target.value });
        this.setState({
            commentsErr: e.target.value ? false : true
        })
        setTimeout(() => {
            this.validateFeilds();
        }, 100);
    }
    handleChange(e) {
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
            this.setState({
                showBtn: utilityGlobal.validateData(this.state.validateField, this).status,
            });
        });
        this.validateFeilds();
    }
    validateFeilds() {
        let flag = false;
        const { validateField, captcha, captchaToken, comments } = this.state;
        for (let i = 0; i < validateField.length; i++) {
            if (this.state[validateField[i]]) {
                flag = true
            } else {
                flag = false
            }
        }
        if (captcha && comments) {
            flag = true;
        } else {
            flag = false
        }
        setTimeout(() => {
            this.setState({ showBtn: flag ? true : false });
        }, 100);
    }
    submitForm(e) {
        let validateFlage = false;
        const { validateField, captcha, captchaToken, comments } = this.state;
        const data = {};
        for (let i = 0; i < validateField.length; i++) {
            if (this.state[validateField[i]]) {
                validateFlage = true
            }
            data[validateField[i]] = this.state[validateField[i]]
        }
        if (!captcha) {
            toast.error("Please Verify Captcha !");
        }
        data.user_id = 1;
        data.comments = comments;
        data.page_from = window.location.href;
        // alert("test");
        API.jsonApiCall(feedbackApi, data, "POST", null, {
            "Content-Type": "application/json",
        })
            .then((res) => {
                console.log("ressss", res);
                if (res.status == 200) {
                    toast.success(res.message);
                }
            })
            .catch((e) => {
                console.log("error", e.message);
                toast.error("Something went wrong try later!");
            });
    }
    render() {
        return (
            <>
                <section className="contact-wrapper">
                    <div className="feedback-wrapper container">
                        <div className="row aboutSection">
                            <div className="col-sm-12">
                                <h3 className="contactus ">
                                    Feedback Form
                                    <hr className="hr" />
                                </h3>
                             </div>
                        </div>
                        <div className="row aboutSection">
                            <div className="col-sm-12 aboutForm1">
                                <div className="letAboutForm1">
                                    {utilityGlobal.renderConfig(this.state.formConfig, this.state, this)}
                                    <textarea className="mt-0 form-control comments" value={this.state.comments} onChange={this.handleComments} placeholder="Comments or Questions" cols={22} rows={5} />
                                    {this.state.commentsErr && <span className="err-inp-msg customErr" >Field is required</span>}
                                </div>
                             </div>
                        </div>
                        <div className="row aboutSection">
                            <div className="col-sm-12 g-captcha-container mt-4">
                                <Reaptcha sitekey="6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI" onVerify={this.onVerify} />
                             </div>
                        </div>
                        <div className="row aboutSection mt-4">
                            <div className="col-sm-12">
                                <div className="letAboutForm1">
                                    <button className="custom-button-red nav-button btn btn-primary submit_button" disabled={this.state.showBtn ? false : true} onClick={this.submitForm}>Submit</button>
                                </div>
                             </div>
                        </div>

                    </div>
                </section>
                <ToastContainer />
            </>
        );
    };
}
export default Feedback;