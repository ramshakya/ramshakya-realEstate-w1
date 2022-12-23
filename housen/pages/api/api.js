import axios from "axios";
import Constants from  "../../constants/Global"
const baseURL = Constants.base_url;
export const requestToAPI = async (body, urlPath, method = 'GET',token='') => {

    // console.log(token);
    try {
        let resultData = null;
        let response = null;
        if (method === 'GET' || method === 'DELETE') {
            response = await fetch(`${baseURL}${urlPath}`, {
                headers: { 'Content-Type': 'application/json'},
                method,

            });
        } else {
            response = await fetch(`${baseURL}${urlPath}`, {
                method,
                headers: { 'Content-Type': 'application/json','Authorization': `Bearer ${token}`},
                body: body
            });
        }
        resultData = await response.json();
        if (!response.ok) {
            let msg = 'Some error occurred';
            if (resultData) {
                msg = resultData.message?resultData.message:"";
            }else{
                msg = "Some error occurred";
                return msg;
            }
            //throw new Error(msg);
        }
        // console.log("result data",resultData);
        return resultData;
    } catch (err) {
        return Promise.reject(err);
    }
};

