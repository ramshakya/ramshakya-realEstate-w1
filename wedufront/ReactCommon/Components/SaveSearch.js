import { useState, useEffect, useRef, Component } from "react";
import { Row, Col, Modal, Form } from "react-bootstrap";
const SaveSearch = (props) => {
    console.log("SaveSearch====>>>>",props);
    const [modalShow, setModalShow] = useState(props.modalShow?props.modalShow:false);
    const [show, setShow] = useState(true);
    const [loaderState, setLoaderState] = useState(false);
    const [btnShow, setBtnShow] = useState(false);
    
  useEffect(()=>{
     
  },[]);
  function SavedSearch(state){
    setModalShow(state);
  }
  function changeHendler(e){

  }
  function handleSaveSubmit(){

  }
 
  return (
      
    <>
    <div className="savedSearch">
                    <Modal
                        show={modalShow} onHide={() => SavedSearch(false)}
                        className="saveSearchModel"
                        size="md"
                        aria-labelledby="contained-modal-title-vcenter"
                    >
                        <Modal.Header closeButton>
                            <Modal.Title id="contained-modal-title-vcenter">
                                Saved my search
                        </Modal.Title>
                        </Modal.Header>
                        <Modal.Body>
                            <br />
                            <input id="name_search" type="text" onChange={changeHendler} onBlur={changeHendler} className="form-control form-input-border" name="filter_name" placeholder="Give A Name to your search" required="required" />
                            <br />
                            <h4>Setup New Listing Alert</h4>
                            <p className="mt-3">Receive Alerts Frequency</p>
                            <Form.Check
                                inline
                                label="Instantly"
                                name="frequency"
                                type="radio"
                                value="instantly"
                                onChange={changeHendler}
                            />
                            <Form.Check
                                inline
                                label="Daily"
                                name="frequency"
                                type="radio"
                                value="daily"
                                onChange={changeHendler}
                            />
                            <Form.Check
                                inline
                                label="Monthly"
                                name="frequency"
                                type="radio"
                                value="monthly"
                                onChange={changeHendler}
                            />
                            <Form.Check
                                inline
                                label="Never"
                                name="frequency"
                                type="radio"
                                value="never"
                                onChange={changeHendler}
                            />
                            <div className="saveSearchBtn">
                                {loaderState ?
                                    <>
                                        <input id="modalSaveFilter" className="btn btn-lg btn-block saveSearchForm reset-btn" type="button" value="Submiting....." disabled={true}></input>
                                    </> : <>
                                        <input id="modalSaveFilter" onClick={handleSaveSubmit} className="btn btn-lg btn-block saveSearchForm reset-btn" type="button" value="Save Filter" disabled={btnShow}></input>
                                    </>}

                            </div>
                        </Modal.Body>
                    </Modal>
                </div>
    </>
    
    );
}
export default SaveSearch;