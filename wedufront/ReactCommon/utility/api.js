const axios = require("axios");
const API = {
  jsonApiCall: function (
    url,
    options,
    method,
    slashApiObj,
    headers,
    queryString = null
  ) {
    try {
      let configUrl = url;
      //let apiUrl = configs.instaApiUrl + confAPI[key].url
      if (
        slashApiObj &&
        slashApiObj !== null &&
        Object.keys(slashApiObj).length > 0
      ) {
        configUrl = this.makeUrlSlash(configUrl, slashApiObj);
      }
      let apiUrl =  configUrl;
      if (
        queryString &&
        queryString !== null &&
        Object.keys(queryString).length > 0
      ) {
        const qs = this.objectToQueryString(queryString);
        apiUrl = `${apiUrl}?${qs}`;
      }
      const requestObj = {
        method,
        url: apiUrl,
        headers,
      };
      if (options && options !== null) {
        requestObj.data = options;
      }
      return axios(requestObj)
        .then((res) => {
          if (res.status == 200) {
            if (typeof res.data != "object") {
              //console.error("API Content ERR: ", JSON.stringify(res.config));
            }

            return res.data;
          }
        })
        .catch((error) => {
          console.error(error);
          return { error: true, errMsg: error.toString(), errorResp: error };
        });
    } catch (err) {
     // console.log("error: ", err);
    }
  },
  objectToQueryString: function (obj) {
    var str = [];
    for (var p in obj)
      if (obj.hasOwnProperty(p)) {
        str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
      }
    return str.join("&");
  },
  makeUrlSlash: function (configUrl, queryParams) {
    let str = configUrl;
    for (const params in queryParams) {
      const str1 = `{${params}}`;
      str = str.replace(str1, queryParams[params]);
    }
    return str;
  },
};
export default API
