import React, { useEffect } from "react";
import Pagination from "react-bootstrap/Pagination";
// import { scrollToTop } from "../helpers/scroll";
import PropTypes from "prop-types";
//
const PaginationComponent = ({
  itemsCount,
  itemsPerPage,
  currentPage,
  setCurrentPage,
  alwaysShown = true,
}) => {
  const pagesCount = Math.ceil(itemsCount / itemsPerPage);
  const isPaginationShown = alwaysShown ? true : pagesCount > 1;
  const isCurrentPageFirst = currentPage === 1;
  const isCurrentPageLast = currentPage === pagesCount;

  const changePage = (number) => {
    if (currentPage === number) return;
    setCurrentPage(number);
  };

  const onPageNumberClick = (pageNumber) => {
    changePage(pageNumber);
  };

  const onPreviousPageClick = () => {
    let calculatedPage = currentPage - 1;
    changePage(calculatedPage);
  };

  const onNextPageClick = () => {
    let calculatedPage = currentPage + 1;
    changePage(calculatedPage);
  };

  const setLastPageAsCurrent = () => {
    if (currentPage > pagesCount) {
      setCurrentPage(pagesCount);
    }
  };
  const onNextLastPage = () => {
    changePage(pagesCount);
  };

  let isPageNumberOutOfRange;

  const pageNumbers = [...new Array(pagesCount)].map((_, index) => {
    const pageNumber = index + 1;
    const isPageNumberFirst = pageNumber === 1;
    const isPageNumberLast = pageNumber === pagesCount;
    const isCurrentPageWithinTwoPageNumbers =
      Math.abs(pageNumber - currentPage) <= 2;

    if (
      isPageNumberFirst ||
      isPageNumberLast ||
      isCurrentPageWithinTwoPageNumbers
    ) {
      isPageNumberOutOfRange = false;
      return (
        <Pagination.Item
          key={pageNumber}
          onClick={() => onPageNumberClick(pageNumber)}
          active={pageNumber === currentPage}
        >
          {pageNumber}
        </Pagination.Item>
      );
    }

    if (!isPageNumberOutOfRange) {
      isPageNumberOutOfRange = true;
      return (
        <Pagination.Ellipsis
          key={pageNumber}
          onClick={() => onPageNumberClick(pageNumber)}
          className="muted"
        />
      );
    }

    return null;
  });

  useEffect(setLastPageAsCurrent, [pagesCount]);
  return (
    <>
      {isPaginationShown && (
        <Pagination>
          <Pagination.Prev
            onClick={onPreviousPageClick}
            disabled={isCurrentPageFirst}
          />
          {pageNumbers}
          <Pagination.Next
            onClick={onNextPageClick}
            disabled={isCurrentPageLast}
          />
          {pagesCount > 4&& pagesCount > currentPage&& (
            <Pagination.Last
              onClick={onNextLastPage}
              disabled={isCurrentPageLast}
            />
          )}
        </Pagination>
      )}
    </>
  );
};

PaginationComponent.propTypes = {
  itemsCount: PropTypes.number.isRequired,
  currentPage: PropTypes.number.isRequired,
  setCurrentPage: PropTypes.func.isRequired,
  alwaysShown: PropTypes.bool,
};

export default PaginationComponent;
