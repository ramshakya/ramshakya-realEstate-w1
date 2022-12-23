import React, { Component, useState, useEffect, useRef } from "react";
import API from "../../ReactCommon/utility/api";
import Constants from '../../constants/GlobalConstants';
const extra_url = Constants.extra_url;
import Layout from '../../components/Layout/Layout';
import Link from "next/link";
import AgentCard from '../../components/Card/AgentCard';
import { useRouter } from 'next/router';
import { Tab, Row, Col, Nav, Form, Button, Tabs } from "react-bootstrap";

const Agents = (props) => {
	const [key, setKey] = useState('first');
	const [records, setRecords] = useState([]);
	const router = useRouter();
	const staffId = router.query.slug;
	const [Staffimages, setStaffimages] = useState([]);
	useEffect(() => {
		if (staffId != undefined) {
			let body = JSON.stringify({
				getInfo: 1,
				AgentId: Constants.agentId,
				staffId: staffId
			});
			API.jsonApiCall((extra_url + 'GetStaffs'), body, "POST", null, {
				"Content-Type": "application/json",
			}).then((res) => {
				setRecords(res.StaffDetail);
				setStaffimages(res.ImageUrl);
			});
		}
	}, []);

	let agentImage = "../images/avatar.jpg";
	if (Staffimages != null) {
		agentImage = Staffimages;
	}
	return (
		<>
			<section className="section-padding">
				<div className="container">
					<div className="row">
						<div className="col-md-12">
							<div className="row">
								<div className="col-md-3">
									<img src={agentImage} width={'100%'} />
								</div>
								<div className="col-md-5">
									<h4>{records.name}</h4>
									<p>Broker</p>
									<p><b>Mobile: </b> {records.phone_number}</p>
									<p><b>Office: </b> -- </p>
									<p><b>Fax: </b> -- </p>
									<p><b>Email: </b> {records.email} </p>
									<p><b>Address: </b> -- </p>
								</div>
								<div className="col-md-4">
									<h4>Let's Get In Touch</h4>
									<form className="AgentContactForm">
										<div className="mb-3">

											<input
												type="text"
												className="form-control"
												name="Name"
												placeholder="Your Name *"
												required=""
											/>
										</div>
										<div className="mb-3">

											<input
												type="text"
												className="form-control"
												name="Name"
												placeholder="Email Address *"
												required=""
											/>
										</div>

										<div className="mb-3">
											<button className="common-btn">How Can I Help? <i className="fa fa-angle-double-right" aria-hidden="true"></i></button>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
					<div className="row mt-5 custom-tabs">
						<Tabs
							defaultActiveKey="first"
							transition={false}
							id="noanim-tab-example"
							className="mb-3"
						>
							<Tab eventKey="first" title="About">

								<p><b>Mobile: </b> {records.phone_number}</p>
								<p><b>Office: </b> -- </p>
								<p><b>Fax: </b> -- </p>
								<p><b>Email: </b> {records.email} </p>
								<p><b>Address: </b> -- </p>
							</Tab>
							<Tab eventKey="second" title="Get to know your agent">
								Empty
							  </Tab>
							<Tab eventKey="contact" title="Listings">
								No Listings available
							   </Tab>
						</Tabs>
					</div>
				</div>
			</section>
		</>
	);
}
export default Agents;
// git check