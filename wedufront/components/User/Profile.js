import React,{useEffect,useState} from "react";
import {Tab,Row,Col,Nav,Form,Button,Tabs} from "react-bootstrap";
import AccountSetting from "./AccountSetting";
import Fovourite from "./Fovourite";
import LoginInfo from "./LoginInfo";
import SavedSearch from "./SavedSearch";
import EmailHistory from "./EmailHistory"
import UpdatePassword from "./UpdatePassword"
const Profile = (props) => {
	
	// if(!checkLoginToken){
	// 	window.location.href = "/";
	// }
	return(
			<div className="UserProfile">
				<h4 className="profile-title">My Profile</h4>
				<Tab.Container id="left-tabs-example" defaultActiveKey="first">
		        <Row>
		          <Col sm={3}>
		            <Nav variant="pills" className="flex-column">
		              <Nav.Item>
		                <Nav.Link eventKey="first">Profile</Nav.Link>
		              </Nav.Item>
		              <Nav.Item>
		                <Nav.Link eventKey="second">Favourites</Nav.Link>
		              </Nav.Item>
		              <Nav.Item>
		                <Nav.Link eventKey="third">Saved Alert</Nav.Link>
		              </Nav.Item>
		              <Nav.Item>
		                <Nav.Link eventKey="fourth">Login Info</Nav.Link>
		              </Nav.Item>
		            </Nav>
		          </Col>
		          <Col sm={9}>
		            <Tab.Content>
		              <Tab.Pane eventKey="first">
		              		<div className="row">
			              		<div className="col-md-12 col-lg-12 profileItem">
					          		<Tabs defaultActiveKey="BasicDetail" id="uncontrolled-tab-example" className="mb-3">
									  <Tab eventKey="BasicDetail" title="Basic Details">
									    		<AccountSetting />
					          				
									  </Tab>
									  <Tab eventKey="UpdatePassword" title="Update Password">
									  		<UpdatePassword />				          				
									  </Tab>
									 
									</Tabs>
					          			
					          		</div>
				          		
				            </div>
		              </Tab.Pane>
		              <Tab.Pane eventKey="second">
		                	<div className="row">
				          		<div className="col-md-12 col-lg-12 prorfileAccount">
				          			<div className="profile-heading"><h5>Favourites Properties</h5></div>
				          			<Fovourite />
				          		</div>
				            </div>
		              </Tab.Pane>
		              <Tab.Pane eventKey="third">
		                	<div className="row">
				          		<div className="col-md-12 col-lg-12 profileItem">
				          		<Tabs defaultActiveKey="home" id="uncontrolled-tab-example" className="mb-3">
								  <Tab eventKey="home" title="Saved search">
								    	<div className="profile-heading"><h5>Saved Searches</h5></div>
				          				<SavedSearch />
								  </Tab>
								  <Tab eventKey="profile" title="Email history">
								  		<div className="profile-heading"><h5>Email history</h5></div>
				          				<EmailHistory />
								  </Tab>
								 
								</Tabs>
				          			
				          		</div>
				            </div>
		              </Tab.Pane>
		              <Tab.Pane eventKey="fourth">
		              		<div className="row">
				          		<div className="col-md-12 col-lg-12 prorfileAccount">
				          			<div className="profile-heading"><h5>Login Info</h5></div>
				          			<LoginInfo />
				          		</div>
				            </div>
		                	
		              </Tab.Pane>
		            </Tab.Content>
		          </Col>
		        </Row>
		      </Tab.Container>
		         
				   </div>
				);
}
export default Profile;