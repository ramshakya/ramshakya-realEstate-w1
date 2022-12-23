import React from 'react'
import Constants from './../../../constants/GlobalConstants';
import Style from './css/index.module.css'
import Header from './MapHeader'
const mapDefaultImage = Constants.mapDefaultImage;
 
class Index extends React.Component {
    constructor(props) {
        super(props)
    }
    componentDidUpdate(prevProps, prevState, snapshot) {
    }
    componentDidMount() {
    }
    render() {
        return (
            <div className={Style.containerSection}>
                <div className="row">
                    <div className="col-md-12">
                        <Header />
                    </div>
                    <div className="col-md-6">
                            {/* Property Card  */}
                    </div>
                    <div className={`col-md-6`}>
                        <div className={`row ${Style.textWrap}`}>
                            {/* Property Card  */}
                            <div className={`col-md-6`}>
                                <p className={''}>pppppppppppppppppppppppppppppppppppppppp</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        )
    }
}
export default Index


