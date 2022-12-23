import React, { useState, useEffect, useRef } from "react";
const Schedule = (props) => {
    //console.log("props", props);
    const extraProps = [];
    const prop = [];
    const [showModalClass, setShow] = useState(props.show ? "d-block" : "d-block")
    useEffect(() => {
        if (props.show) {
            setShow("d-block")
        }
        else {
            setShow("d-block")
        }
    }, [props.show]);
    const closeModal = (e) => {
        setShow("");
        if (props.handleClose) {
            props.handleClose();
        }
    }
    // handleShowSchedule
    return (
        <div className="reactModal">
            <div className={`modal d-block`}>
                <div className="modal-header">
                    <span className="closeBtn" onClick={closeModal}>x</span>
                </div>
                <div className="modal-body">
                    <section className="schedule">
                        <div className="container">
                            <div className="date_block">
                                <div className="digit">
                                    <p>19</p>
                                </div>
                                <div className="wording"><p>JUNE</p><p>2021</p></div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    );
}
export default Schedule;