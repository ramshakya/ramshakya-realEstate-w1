import React, { Component } from 'react';
import PropTypes from 'prop-types';

export class Autocomplete extends Component {
    static propTypes = {
        options: PropTypes.instanceOf(Array).isRequired
    };
    state = {
        activeOption: 0,
        filteredOptions: [],
        showOptions: false,
        userInput: ''
    };

    onChange = (e) => {
        //console.log('onChanges');

        const { options } = this.props;
        const userInput = e.currentTarget.value;

        const filteredOptions = options.filter(
            (optionName) =>
                optionName.toLowerCase().indexOf(userInput.toLowerCase()) > -1
        );

        this.setState({
            activeOption: 0,
            filteredOptions,
            showOptions: true,
            userInput: e.currentTarget.value
        });
    };

    onClick = (e) => {
        this.setState({
            activeOption: 0,
            filteredOptions: [],
            showOptions: false,
            userInput: e.currentTarget.innerText
        });
    };
    onKeyDown = (e) => {
        const { activeOption, filteredOptions } = this.state;

        if (e.keyCode === 13) {
            this.setState({
                activeOption: 0,
                showOptions: false,
                userInput: filteredOptions[activeOption]
            });
        } else if (e.keyCode === 38) {
            if (activeOption === 0) {
                return;
            }
            this.setState({ activeOption: activeOption - 1 });
        } else if (e.keyCode === 40) {
            if (activeOption === filteredOptions.length - 1) {
               // console.log(activeOption);
                return;
            }
            this.setState({ activeOption: activeOption + 1 });
        }
    };

    render() {
        const {
            onChange,
            onClick,
            onKeyDown,

            state: { activeOption, filteredOptions, showOptions, userInput }
        } = this;
        let optionList;
        if (showOptions && userInput) {
            if (filteredOptions.length) {
                optionList = (
                    <ul className="options">
                        {filteredOptions.map((optionName, index) => {
                            let className;
                            if (index === activeOption) {
                                className = 'option-active';
                            }
                            return (
                                <li className={className} key={optionName} onClick={onClick}>
                                    {optionName}
                                </li>
                            );
                        })}
                    </ul>
                );
            } else {
                optionList = (
                    <div className="no-options">
                        <em>No Option!</em>
                    </div>
                );
            }
        }
        return (
            <React.Fragment>
                <div className="search_inp form-group">
                    <input
                        type="text"
                        className="search_inp-box form-control"
                        onChange={onChange}
                        onKeyDown={onKeyDown}
                        value={userInput}
                    />
                </div>
                {optionList}
            </React.Fragment>
        );
    }
}

export default Autocomplete;



// import React, { useState } from 'react';
// const AutoSuggestion = (props) => {
//     const [val, setVal] = useState({})
//     const handleClick = () => {
//         setVal(top100Films[0]);//you pass any value from the array of top100Films
//         // set value in TextField from dropdown list
//     };
//     return (
//         <>
//             <Autocomplete
//                 value={val}
//                 options={[
//                     'Papaya',
//                     'Persimmon',
//                     'Paw Paw',
//                     'Prickly Pear',
//                     'Peach',
//                     'Pomegranate',
//                     'Pineapple'
//                   ]}
//                 getOptionLabel={option => option.title}
//                 style={{ width: 300 }}
//                 renderInput={params => (
//                     <TextField
//                         {...params}
//                         label="Combo box"
//                         variant="outlined"
//                         fullWidth

//                     />
//                 )}
//             />
//         </>
//     )
// }
// export default AutoSuggestion
