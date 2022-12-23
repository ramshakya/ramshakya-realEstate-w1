import React from "react";
import validateUtility from './validateUtility'
const utilityGlobal = {
  renderConfig(items, state, self) {
    let promise = items.map((val, index) => {
      let { propAttr } = val;
      let finalProp = this.cloneObject(propAttr);
      finalProp.key = finalProp.key || index;
      if (val.children && Array.isArray(val.children)) {
      } else {
        if (
          finalProp.settings &&
          Array.isArray(finalProp.settings) &&
          finalProp.settings.length > 0
        ) {
          for (let i = 0; i < finalProp.settings.length; i++) {
            const field = finalProp.settings[i];
            if (field.apiKey) {
              finalProp[field.prop] = this.getDeepObject(state, field.apiKey);
            } else if (field.funcName) {
              finalProp[field.prop] = self[field.funcName];
            }
          }
        }
        delete finalProp.settings;
        return React.createElement(val.component, finalProp, val.children);
      }
    });
    return promise;
  },

  getDeepObject(obj, path) {
    try {
      for (var i = 0, path = path.split("."), len = path.length; i < len; i++) {
        obj = obj[path[i]];
      }
      return obj;
    } catch (e) {
      return null;
    }
  },

  cloneObject(obj) {
    if (null == obj || "object" != typeof obj) return obj;
    if (obj instanceof Array) {
      var copy = [];
      for (var i = 0, len = obj.length; i < len; i++) {
        copy[i] = this.cloneObject(obj[i]);
      }
      return copy;
    }

    // Handle Object
    if (obj instanceof Object) {
      var copy = {};
      for (var attr in obj) {
        if (obj.hasOwnProperty(attr)) copy[attr] = this.cloneObject(obj[attr]);
      }
      return copy;
    }
    return obj;
  },

  validateFields(fieldName, leadObj) {
    let obj;
    // let obj= {status:false};
    try {
      switch (fieldName) {
		  case 'confirmationPassword':
			  obj = {
				  confirmationPassword:leadObj.state.confirmationPassword,
				  status:leadObj.state.confirmationPassword === leadObj.state.password
			  }

			  break;
        default:
          let field = document.querySelector(
            `[data-gsf-name=${fieldName.trim()}]`
          );

		  // console.log('kamaliiieee===', field , field && field.dataset.inpValidation
		  // ? validateUtility.validate(field):'kamal')
          obj = {
            [fieldName]: leadObj.state[fieldName],
            status:
              field && field.dataset.inpValidation
                ? validateUtility.validate(field)
                : typeof leadObj.state[fieldName] !== "undefined" &&
                  validateUtility.required(leadObj.state[fieldName])
                ? true
                : false,
          };
          break;
      }
	//   console.log('kamal obj',obj)
	//   obj.status= !obj.status
      return obj;
    } catch (e) {
      //console.log(e);
    }
  },

  validateData(validateArray, leadObj) {
    let validateObj = [];
    let finalObj = [];
    if (validateArray.length <= 0) {
      finalObj["status"] = true;

      return finalObj;
    }
    for (let i = 0; i < validateArray.length; ++i) {
      validateObj[i] = this.validateFields(validateArray[i], leadObj);
      Object.assign(finalObj, validateObj[i]);
    }
    for (let key in validateObj) {
      finalObj.status = finalObj.status && validateObj[key].status;
    }
    return finalObj;
  },
  buildComponent(item,state){

  }
};
export default utilityGlobal;
