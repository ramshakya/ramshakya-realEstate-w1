import React, { Component } from "react";
import { Row, Col, Modal, Button } from "react-bootstrap";
import 'react-toastify/dist/ReactToastify.css';
import { ToastContainer, toast } from 'react-toastify';
import { leadFormApi } from "./../../../constants/GlobalConstants";
import API from "./../../utility/api";
import Constants from "../../../constants/GlobalConstants";
class ShareEmail extends Component {
    constructor(props) {
        console.log("ShareEmail===>>", props);
        super(props);
        this.state = {
            name: false,//for show/hide error msg
            email: false,//for show/hide error msg
            emails: false,//for show/hide error msg
            message: false,//for show/hide error msg
            showBtn: true,
            nameVal: "",
            senderEmailVal: "",
            recEmailsVal: "",
            messageVal: "",
            details:props.details
             
        }
        this.handleChanges = this.handleChanges.bind(this);
        this.sendDetails = this.sendDetails.bind(this);
    }
    sendDetails(e){
    }
    handleChanges(e) {
        console.log("handleChanges  " + e.target.name, e.target.value);
        if (e.target.name === "sender_name") {
            this.setState({
                nameVal:e.target.value,
                name : e.target.value ? false : true
            });
            // setName(e.target.value?false:true);
        }
        if (e.target.name === "sender_email") {
            this.setState({
                senderEmailVal:e.target.value,
                email : e.target.value ? false : true
            });
            
        }
        if (e.target.name === "message") {
            this.setState({
                messageVal:e.target.value,
                message : e.target.value ? false : true
            });
            //  setMessage(e.target.value?false:true);
        }
        if (e.target.name === "email") {
            this.setState({
                recEmailsVal:e.target.value,
                emails : e.target.value ? false : true
            });
        }
        if (!this.state.name && !this.state.email && !this.state.emails && !this.state.message) {
            console.log("1");
            this.setState({
                showBtn : false
            });
        } else {
            console.log("2");
            this.setState({
                showBtn : true
            });
        }
    }
    render() {
        return (
            <>
                <div className="share-listings-models">
                    <Modal
                        
                        className="emailShareModel"
                        size="lg"
                        aria-labelledby="contained-modal-title-vcenter"

                    >
                        <Modal.Header closeButton>
                            <Modal.Title id="contained-modal-title-vcenter">
                                Share Listings
          </Modal.Title>
                        </Modal.Header>
                        <Modal.Body>
                            <div className="popup_propform" id="modalEmailForm" >
                                <div className="row" id="2">
                                    <div className="col-md-6 col-sm-6 col-lg-6 form-group ">
                                        <label className="">Your Name*</label>
                                        <input type="text" onChange={this.handleChanges} onBlur={this.handleChanges} className=" form-control senderName input-box" placeholder="Name*" name="sender_name" required="" />
                                        <span className={`validateError  ${this.state.name ? "" : "hide"}`} >Name is required.</span>
                                    </div>
                                    <div className="form-group col-md-6 col-sm-6 col-lg-6">
                                        <label className="">Your Email*</label>
                                        <input type="email" onChange={this.handleChanges} onBlur={this.handleChanges} className="  form-control senderEmail input-box" placeholder="Email*" name="sender_email" required="" />
                                        <span className={`validateError  ${this.state.email ? "" : "hide"}`} >Email is required.</span>
                                    </div>
                                    <div className="form-group col-md-12 col-sm-12 col-lg-12 mt-3">
                                        <label className="">Your Friends Emails*</label>
                                        <input type="email" onChange={this.handleChanges} onBlur={this.handleChanges} className=" form-control recipentEmail input-box" placeholder="Emails*" name="email" required="" />
                                        <span className={`validateError  ${this.state.emails ? "" : "hide"}`} >Emails are required.</span>
                                    </div>
                                    <div className="form-group col-lg-12 mt-3">
                                        <label className="">Message*</label>
                                        <textarea onChange={this.handleChanges} onBlur={this.handleChanges} className="form-control input-box" name="message" style={{ "height": "66px" }} placeholder={`I would like to get more info about ${this.state.details.Addr}`}></textarea>
                                        <span className={`validateError  ${this.state.message ? "" : "hide"}`} >Message is required.</span>
                                    </div>
                                    <input type="hidden" name="property_url" value={this.props.shareLink} id="property_url" />
                                    <input type="hidden" name="property_id" value={this.state.details.id} />
                                    <input type="hidden" name="property_mls_no" value={this.state.details.Ml_num} />
                                    <input type="hidden" name="page_from" value="property details share email form" />
                                    <div className="col-md-4">
                                    </div>
                                    <div className="col-md-4">
                                        <div className="shareEmail">
                                            <button id="shareEmailBtn" disabled={this.state.showBtn} type="btn" className="btn showSchedule btn-sm mt-4" onClick={this.sendDetails} > <i className="fa fa-spinner fa-spin"> </i>Submit</button>
                                        </div>
                                    </div>
                                    <div className="col-md-4">
                                    </div>
                                </div>
                            </div>
                        </Modal.Body>
                    </Modal>
                </div>

            </>
        )
    }
}
export default ShareEmail;