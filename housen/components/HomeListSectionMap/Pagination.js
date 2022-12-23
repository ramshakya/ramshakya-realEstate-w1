import React, { useEffect, useState } from 'react';
import ReactDOM from 'react-dom';
let itemsData = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14];
let isOne = 1;
//
const PaginatedItems = (props) => {
    const [isSelected, setisSelected] = useState(false);
    const [items, setItems] = useState(itemsData);//total
    const [adjacents, setAdjacent] = useState(1);
    const [limits, setLimit] = useState(10);
    const [pages, setPage] = useState(1);
    // const extraProps = props.extraProps;
    const extraProps = {
        // totalitems:items,
        adjacents: 1,
        limit: 10,
        page: 1
    }



    useEffect(() => {

    }, []);
    const setPagination = () => {

        let adjacent = props.adjacents ? props.adjacents : adjacents;
        let limit = props.limit ? props.limit : limits;
        let page = props.page ? props.page : pages;
        let totalitems = props.total ? props.total : items
        let prev = page - 1;                                  //previous page is page - 1
        let next = page + 1;                                  //next page is page + 1
        let lastpage = Math.ceil(totalitems.length / limit);             //lastpage is = total items / items per page, rounded up.
        let lpm1 = lastpage - 1;
        let isOneBreak = 1;
        let isOneLast = 1;
        let isOneLmp = 1;
        let isCount = 1;

        if (!items || items.length <= 0 || !Array.isArray(items)) {
            return null;
        }
          let setPaginationData = items.map((counter, index) => {
            if (lastpage > 1) {
                if (page < 1) {
                    return (
                        <li className='page-item active' key={index} >
                            <button className='pagination_page ' rel={prev} >{"« prev"}</button>
                        </li>
                    );
                }
                if (page > 1) {
                    if (isOne == 1) {
                        isOne++;
                        return (
                            <li className='page-item active' key={index} >
                                <button className='pagination_page page-link active' rel={prev} >{'« prev'}</button>
                            </li>
                        );
                    }
                }
                if (lastpage < 7 + (adjacents * 2)) { //not enough pages to bother breaking it up
                    for (counter = 1; counter <= lastpage; counter++) {
                        if (counter == page)
                            return (
                                <li className='page-item active' key={index} >
                                    <button className='pagination_page page-link active' rel={prev} >{counter}</button>
                                </li>
                            );
                        else {
                            return (
                                <li className='page-item' key={index} >
                                    <button className='pagination_page page-link ' rel={prev} >{counter}</button>
                                </li>
                            );
                        }
                    }
                }
                else if (lastpage >= 7 + (adjacents * 2))   //enough pages to hide some
                {
                    if (page < 1 + (adjacents * 3)) {
                        for (counter = 1; counter < 4 + (adjacents * 2); counter++) {
                            if (counter == page) {
                                return (
                                    <li className='page-item active'><button className='pagination_page page-link active' rel={counter} >{counter}</button></li>
                                );
                            }
                            else {
                                return (
                                    <li className='page-item '><button className='agination_page page-link' rel={counter} >{counter}</button></li>
                                );
                            }
                        }
                        if (isOneBreak === 1) {
                            // isOneBreak;
                            return (
                                <li className='page-item'><button className='pagination_page page-link' rel='' >...</button></li>
                            );
                        }
                        if (isOneLmp === 1) {
                            // isOneLmp++;
                            return (
                                <li className='page-item'><button className='pagination_page page-link' rel={lpm1} >{lpm1}</button></li>
                            );
                        }
                        if (isOneLast === 1) {
                            // isOneLast++;
                            return (
                                <li className='page-item'><button className='pagination_page page-link' rel={lastpage} >{lastpage}</button></li>
                            );
                        }

                    } //in middle; hide some front and some back
                    else if (lastpage - (adjacents * 2) > page && page > (adjacents * 2)) {
                        if (isCount === 1) {
                            isCount++;
                            return (
                                <li className='page-item '><button className='pagination_page page-link ' rel='1' >1</button></li>
                            );
                        }
                        if (isCount === 2) {
                            isCount--;
                            return (
                                <li className='page-item '><button className='pagination_page page-link ' rel='2' >2</button></li>
                            );
                        }
                        for (counter = page - adjacents; counter <= page + adjacents; counter++) {
                            if (counter == page) {
                                return (
                                    <li className='page-item active'><button className='pagination_page page-link' onclick='paginte($counter)' rel={counter} >{counter}</button></li>
                                );
                            }
                            // $pagination.= "<li className='page-item active'><button className='pagination_page page-link' onclick='paginte($counter)' rel='$counter' >$counter</button></li>";
                            else {
                                return (
                                    <li className='page-item '><button className='pagination_page page-link' onclick='paginte(counter)' rel={counter} >{counter}</button></li>
                                );
                            }

                        }
                        if (isOneBreak === 1) {
                            return (
                                <li className='page-item '><button className='pagination_page page-link' onclick='paginte(counter)' rel='' >...</button></li>
                            );
                        }
                        // $pagination.= "";
                        if (isOneLmp === 1) {
                            return (
                                <li className='page-item'><button className='pagination_page page-link' rel={lpm1} >{lpm1}</button></li>
                            );
                        }
                        if (isOneLast === 1) {
                            return (
                                <li className='page-item'><button className='pagination_page page-link' rel={lastpage} >{lastpage}</button></li>
                            );
                        }
                    }
                    else {
                        if (isCount === 1) {
                            isCount++;
                            return (
                                <li className='page-item'><button className='pagination_page page-link' onclick='paginte(1)' rel='1' >1</button></li>
                            );
                        }
                        if (isCount === 2) {
                            isCount--;
                            return (
                                <li className='page-item'><button className='pagination_page page-link' onclick='paginte(2)' rel='2' >2</button></li>
                            );
                        }
                        if (isOneBreak === 1) {
                            return (
                                <li className='page-item'><button className='pagination_page page-link' rel='' >...</button></li>
                            );
                        }
                        for (counter = lastpage - (1 + (adjacents * 3)); counter <= lastpage; counter++) {
                            if (counter == page) {
                                return (
                                    <li className='page-item active'><button className='pagination_page page-link active' rel={counter} >{counter}</button></li>
                                );
                            } else {
                                return (
                                    <li className='page-item'><button className='pagination_page page-link' onclick='paginte(counter)' rel={counter} >{counter}</button></li>
                                );
                            }
                        }
                    }

                }
                if (page < counter - 1) {
                    if (isLastPage == 1) {
                        return (
                            <li className='page-item'><button className='pagination_page page-link' onclick='paginte(counter)' rel={next} >next »</button></li>
                        );
                    }
                }
                else {
                    if (isLastPage == 1) {
                        return (
                            <li className='page-item'><button className='pagination_page page-link' onclick='paginte(counter)' >next »</button></li>
                        );
                    }
                }

            }

        });
        return setPaginationData
    }

    return (
        <>
            <div className={extraProps.className}>
                {items && items.length > 0 && Array.isArray(items) &&
                    <nav aria-label="Page navigation"><ul className="pagination justify-content">
                        {
                            setPagination()
                        }
                    </ul>
                    </nav>
                }
            </div>
        </>

    );

}
export default PaginatedItems
