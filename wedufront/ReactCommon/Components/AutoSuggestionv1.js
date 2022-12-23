import React, { useState, useEffect, useRef } from "react";
import Style from "../../styles/css/ReactCommon/autoSuggestion.module.css";
const AutoSuggestion = (props) => {
  const inputEl = useRef(null);
  const inputDivRef = useRef(null);
  const [allList, setAllList] = useState(props.allList);
  const [selectedText, setSelectedText] = useState(props.selectedText);
  const [showList, setShowList] = useState(false);

  const { extraProps, inputProps } = props;

  useEffect(() => {
    document.addEventListener("click", handleOuterClick);
  }, []);
  useEffect(() => {
    setSelectedText(props.selectedText)
    if (JSON.stringify(props.allList) !== JSON.stringify(allList) && !props.autoCompleteCb) {
      setAllList(props.allList)
    }
  }, [props.selectedText, props.allList]);

  const handleOuterClick = e => {
    if (inputDivRef !== null && inputDivRef.current !== null && !inputDivRef.current.contains(e.target)) {
      setShowList(false);
    }
  };

  const handleInputChange = (e) => {
    let inpVal = e.target.value;

    if (
      props.allList &&
      Array.isArray(props.allList) &&
      props.allList.length > 0
    ) {
      if (inpVal !== "") {
        let finalArr = [];
        for (let i = 0; i < props.allList.length; i++) {
          const text = props.allList[i].text.toLowerCase();
          if (
            text &&
            !props.allList[i].isHeading &&
            text.indexOf(inpVal.toLowerCase()) !== -1
          ) {
            finalArr.push(props.allList[i]);
          }
        }
        setAllList(finalArr);
      } else {
        setAllList(props.allList);
      }
    }

    if (props.autoCompleteCb) {
      props.autoCompleteCb(inpVal, e.target.name, function (obj) {
        if (obj.allList) {
          setAllList(obj.allList);
        }
      });
    }
    setSelectedText(inpVal);
  };

  const renderList = () => {
    if (
      !showList ||
      !allList ||
      allList.length <= 0 ||
      !Array.isArray(allList)
    ) {
      return (
        <li>
          <p className="noDataFound">
            <span> No Data Found...</span>
          </p>
        </li>
      );
    }

    const renderList = allList.map((data, index) => {
     
      if (data.value) {
        if (data.isHeading) {
          return (
            <li className={Style.listHeading} key={index} >
              {data.text}
            </li>
          );
        }
        return (
          <li
            data-value={data.value}
            data-gs-ta-val={JSON.stringify(data)}
            className={`${Style.itemList} type-head`}
            key={index}
            onClick={handleOnClick}
          >
            {data.text}
          </li>
        );
      }
    });

    return renderList;
  };

  const handleOnFocus = (e) => {
    setShowList(true);
  };

  const handleOnBlur = (e) => {
    if (props.onBlur) {
      props.onBlur(e);
    }
  };

  const handleOnClick = (e) => {
    let inpVal = e.target.innerText;
    setSelectedText(inpVal);
    setShowList(false);
    if (typeof props.cb === 'function') {
      const selectedBody = JSON.parse(e.target.getAttribute('data-gs-ta-val'))
      props.cb(selectedBody, inputEl.current.name, e, inputEl);
    }
  };

  return (
    <div
      className={`sss ${extraProps.parentcls ? extraProps.parentcls : ""}  ${Style.autoSuggestionCls
        }`}
      ref={inputDivRef}
    >
      <input
        {...inputProps}
        className={`${inputProps.className} type-head`}
        type="text"
        value={selectedText}
        onClick={handleOnFocus}
        onFocus={handleOnFocus}
        onBlur={handleOnBlur}
        onChange={handleInputChange}
        autoComplete="off"
        ref={inputEl}
      />
      {showList && allList && allList.length > 0 && Array.isArray(allList) && (
        <ul className={Style.resultDisplay}>{renderList()}</ul>
      )}
    </div>
  );
};
export default AutoSuggestion;
