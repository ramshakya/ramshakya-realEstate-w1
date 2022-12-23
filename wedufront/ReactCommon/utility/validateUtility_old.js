const defaultRegx = {
    email: /^[a-z0-9]+[\.a-z0-9+_-]+(\.[a-z0-9+_-]+)*@([a-z0-9_][-a-z0-9_]*(\.[-a-z0-9_]+)*\.(aero|arpa|biz|com|coop|edu|gov|info|int|consulting|mil|museum|name|net|org|pro|travel|mobi|[a-z][a-z])|([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}))(:[0-9]{1,5})?$/i,
    number: /^[0-9]+$/,
    alnum: /^[A-Za-z0-9]+$/,
    alnumwithspace: /^[A-Za-z0-9 ]+$/,
    alpha: /^[A-Za-z]+$/,
    alphawithspace: /^[ A-Za-z ]+$/,
    fullnamewithspace: /^[a-zA-z]+\s[a-zA-z]+/,
}

let validateUtility = {

    validate(selector, errorCb) {
        this.dynamicRegx();
        let error = false
        let validationField = selector.getAttribute('data-inp-validation');
        let finalValue = selector.value;
        validationField = validationField.split(",");
        for (let i = 0; i < validationField.length; i++) {
            const elementType = validationField[i].trim();

            if (validateUtility[elementType] && !validateUtility[elementType](finalValue)) {

                error = true;
                break;
            } else if (this.dynamicRegx()) {

            }

        }
        errorCb(error);
        return error;

    },
    stopDefault(selector) {
        let validationField = selector.target.getAttribute('data-inp-validation');
        if (!validationField || validationField === null)
            return;
        const type = validationField.split(",");
        if (type.length === 0) return;
        let key = String.fromCharCode(selector.which);
        if (type.indexOf("number") !== -1) {
            if (!defaultRegx['number'].test(key))
                selector.preventDefault();
        } else if (type.indexOf("alpha") !== -1) {
            if (!defaultRegx['alpha'].test(key))
                selector.preventDefault();
        } else if (type.indexOf("alnum") !== -1) {
            if (!defaultRegx['alnum'].test(key))
                selector.preventDefault();
        } else if (type.indexOf("alnumwithspace") !== -1) {
            if (!defaultRegx['alnumwithspace'].test(key))
                selector.preventDefault();
        } else if (type.indexOf("fullnamewithspace") !== -1) {
            if (!defaultRegx['fullnamewithspace'].test(key))
                selector.preventDefault();
        }

    },
    required(value) {
        return value && value.toString().trim().length !== 0;
    },

    dynamicRegx() {
        //console.log("");
        for (var regexp in defaultRegx) {
            this._regexpCheck(regexp, defaultRegx);
        }
        return this;
    },

    _regexpCheck: function (regexp, rgexps) {
        validateUtility[regexp] = function (value) {
            return rgexps[regexp].test(value);
        };
    },
};

// validateUtility = validateUtility.dynamicRegx();
// module.exports ={
//     validateUtility
// }
// exports = validateUtility;
export default validateUtility;

