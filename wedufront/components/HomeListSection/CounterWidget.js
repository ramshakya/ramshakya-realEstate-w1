import React from 'react';
const CounterWidget = (props) => {
    return (
        <>
            {/*<!-- counter row  --> */}
            <div className="row">
                <div className="col-sm-12 col-md-7 col-lg-5">
                    <div className="prop-count">
                        <div className="row">
                            {props.counterData && Array.isArray(props.counterData) && props.counterData.length > 0 && props.counterData.map((val, index) => {
                                return (
                                    <div className="col-4 col-sm-4 col-md-4" key={index}>
                                        <span className="purecounter fz-40">{val.value}</span>
                                        <p className="fz-21">{val.label}</p>
                                    </div>
                                )
                            })}
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}
export default CounterWidget;